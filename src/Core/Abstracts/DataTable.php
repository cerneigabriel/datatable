<?php

namespace Modules\DataTable\Core\Abstracts;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Modules\DataTable\Core\Facades\Constraint;
use Modules\DataTable\Core\Interfaces\Abstracts\DataTableInterface;
use Modules\DataTable\Core\Traits;

/**
 * Class DataTable
 *
 * @package Modules\DataTable\Core
 */
abstract class DataTable extends SerializableDataTable implements DataTableInterface
{
    use Traits\HasActions;
    use Traits\HasColumns;
    use Traits\HasConstraints;
    use Traits\HasFilters;
    use Traits\HasPagination;
    use Traits\HasSearch;
    use Traits\HasAuthorizer;

    /** @var array|string[] */
    protected static array $serializationIgnoreProperties = [
        'query'
    ];

    /** @var string */
    public string $name = 'table';

    /** @var string */
    public string $id;

    /** @var \Closure */
    public Closure $customQuery;

    /** @var \Illuminate\Database\Eloquent\Builder|null */
    public ?Builder $query = null;

    /** @var string */
    public string $model;

    /** @var bool */
    public bool $showRecordsCountInDataTableHeader = false; // Show Records Count in Datatable Header

    /**
     * DataTable Constructor
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function __construct(string $id = null)
    {
        $this->setId($id ?? $this->name ?? null);

        $this->handleAuthorizer();

        $this
            ->setColumns()
            ->setFilters()
            ->setConstraints()
            ->setActions()
            ->init();

        $this->mountSorting();
        $this->mountSerialization();
    }

    /**
     * @param string $id
     * @return string
     */
    public function setId(string $id): string
    {
        $this->id = $id ?? uniqid();

        return $this->id;
    }

    /**
     * @return array
     */
    public function viewData(): array
    {
        return [
            'datatable' => $this,
            'records' => $this->getRecordsFromQuery()
        ];
    }

    /**
     * @return array|\Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    protected function getRecordsFromQuery()
    {
        return $this->paginate
            ? $this->prepareQuery()->paginate($this->pageLength)
            : $this->prepareQuery()->get();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder|mixed
     */
    protected function prepareQuery()
    {
        $this->query = $this
            ->newQuery()
            ->where(function (Builder $query) {
                $query->when($this->isFilterable() && $this->filters()->hasValues(), function ($query) {
                    /** @var \Modules\DataTable\Core\Abstracts\DataTableFilter $filter */
                    foreach ($this->filters()->whereNotNullValue() as $filter) {
                        $query = $filter->query($query);
                    }
                });

                return $query;
            })
            ->where(function (Builder $query) {
                $query->when($this->isSearchable() && !empty($this->search), function (Builder $query) {
                    if ($this->hasConstraints() && $this->constraint) {
                        $query = $this->constraint->query($query, $this->search);
                    }

                    if (!$this->constraint) {
                        /** @var \Modules\DataTable\Core\Abstracts\DataTableColumn|\Modules\DataTable\Core\Collections\ColumnsCollection $columns */
                        foreach ($this->columns()->searchable()->queryable($query) as $index => $columns) {
                            $this->queryColumn($columns, $query, $index === 0);
                        }
                    }

                    return $query;
                });

                return $query;
            })
            ->when($this->columns()->get($this->sortBy), function (Builder $query, DataTableColumn $column) {
                if ($column->attributeContainsRelationship()) {
                    return $query->orderByLeftPowerJoins([implode('.', $column->extractRelationshipFromAttribute()), $column->extractAttributeWithoutRelationship()], $this->sortDir);
                }

                return $query->orderBy((new $this->model())->getTable() . ".$column->attribute", $this->sortDir);
            });

        $this->debug(
            'debug',
            'core.datatable.query',
            [
                "Datatable $this->id Query: " .
                vsprintf(
                    str_replace('?', '%s', $this->query->toSql()),
                    collect($this->query->getBindings())
                        ->map(fn ($binding) => is_numeric($binding) ? $binding : "'{$binding}'")
                        ->toArray()
                )
            ]
        );

        return $this->query;
    }

    /**
     * @param string $type
     * @param string $message
     * @param array  $context
     * @return void
     */
    private function debug(string $type, string $message = '', array $context = []): void
    {
        if (config('datatable.debug')) {
            Log::channel('datatable')->{$type}($message, $context);
        }
    }

    /**
     * Get Query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function newQuery(): mixed
    {
        /** @var \Illuminate\Database\Eloquent\Model $model */
        $model = $this->model;
        $query = $model::query();

        if (method_exists($this::class, 'query')) {
            $query = $query->where(function ($query) {
                return $this->query($query);
            });
        }

        if (isset($this->customQuery)) {
            $query = ($this->customQuery)($query);
        }

        return $query;
    }

    /**
     * @param \Closure $customQuery
     * @return $this
     */
    public function setQuery(Closure $customQuery): self
    {
        $this->customQuery = $customQuery;

        return $this;
    }

    /**
     * @return int
     */
    public function getRecordsCount(): int
    {
        return $this->prepareQuery()->count();
    }

    /**
     * @param \Modules\DataTable\Core\Abstracts\DataTableColumn $column
     * @param \Illuminate\Database\Eloquent\Builder             $query
     * @param bool                                              $andOperator
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function queryColumn(DataTableColumn $column, Builder $query, bool $andOperator = true): Builder
    {
        $term = trim($this->search);
        /** @var \Modules\DataTable\Core\Abstracts\DataTableConstraint $constraint */
        if (($constraint = $this->constraints()->firstWhere('name', $column->name)) || ((Constraint::$factory_classes[$column->type] ?? false) && $constraint = $column->makeConstraint())) {
            return $constraint->query($query, $term, $andOperator);
        }

        return $query->{$andOperator ? 'where' : 'orWhere'}("{$query->getModel()->getTable()}.{$column->extractAttributeWithoutRelationship()}", 'like', "%$term%");
    }
}
