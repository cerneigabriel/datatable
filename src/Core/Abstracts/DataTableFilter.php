<?php

namespace Modules\DataTable\Core\Abstracts;

use Closure;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Modules\DataTable\Core\Interfaces\Abstracts\DataTableFilterInterface;
use Modules\DataTable\Core\Traits;

/**
 * Class DataTableFilter
 *
 * @package Modules\DataTable\Core\Abstracts
 */
abstract class DataTableFilter extends SerializableDataTable implements DataTableFilterInterface
{
    use Traits\HasRelationships;
    use Traits\HasViews;

    public const Groups = [
        self::GroupDefault,
        self::GroupSearch,
        self::GroupDataTableTopRight,
    ];

    /** @var string */
    public const GroupDefault = 'default';

    /** @var string */
    public const GroupSearch = 'search';

    /** @var string */
    public const GroupDataTableTopRight = 'datatable-top-right';

    /** @var string */
    public string $type;

    /** @var string */
    public string $name;

    /** @var string */
    public string $attribute;

    /** @var string|mixed|null */
    public ?string $label = null;

    /** @var string|null */
    public ?string $placeholder = null;

    /** @var mixed|null */
    public mixed $value = null;

    /** @var mixed|null */
    public mixed $defaultValue = null;

    /** @var string */
    public string $group = self::GroupDefault;

    /** @var \Closure */
    protected Closure $queryCallback;

    /**
     * @param string $name
     * @param string|null $attribute
     * @param string|null $label
     */
    public function __construct(string $name, string $attribute = null, string $label = null)
    {
        $this->mountSerialization();
        $this->name = $name;
        $this->attribute = $attribute ?? $name;
        $this->placeholder = $label ?? $this->makeLabel($name);

        $this->mountViews();
        $this->defaultViewBag->put('filter', $this);
    }

    /**
     * @param $name
     * @return string
     */
    protected function makeLabel($name): string
    {
        return ucwords(Str::replace(['-', '_', '.'], ' ', $name));
    }

    /**
     * @param $placeholder
     * @return $this
     */
    public function setPlaceholder($placeholder)
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $this->validateValue($value);

        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function setDefaultValue($value): static
    {
        $this->defaultValue = $this->value = $this->validateValue($value);

        return $this;
    }

    /**
     * @param $value
     * @return bool|float|int|mixed[]|string|null
     */
    protected function validateValue($value)
    {
        if (is_bool($value)) {
            return (bool)$value;
        } elseif (is_numeric($value) || (is_string($value) && !empty($value = trim($value)))) {
            return $value;
        } elseif (is_array($value)) {
            return collect($value)->map(function ($value) {
                if (is_string($value) && !empty($value = trim($value))) {
                    return $value;
                } elseif (is_numeric($value)) {
                    return $value;
                } elseif (is_array($value) && !empty($value)) {
                    return $value;
                }
                return null;
            })->toArray();
        }

        return null;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\HigherOrderBuilderProxy|mixed
     */
    public function query(Builder $query)
    {
        if ($q = $this->resolveQueryCallback($query, $this->value())) {
            return $q;
        } elseif (method_exists($this::class, 'setQuery')) {
            if ($this->attributeContainsRelationship()) {
                return $query->whereHas(implode('.', $this->extractRelationshipFromAttribute()), function ($query) {
                    return $this->setQuery($query, $this->value());
                });
            }
            return $this->setQuery($query, $this->value());
        }

        return $query;
    }

    /**
     * @param $query
     * @param $value
     * @return mixed
     */
    public function resolveQueryCallback($query, $value): mixed
    {
        if (isset($this->queryCallback)) {
            return $this->queryCallback->call($this, $query, $value, $this);
        }

        return null;
    }

    /**
     * @param string|null $key
     * @param $default
     * @return mixed|null
     */
    public function value(string $key = null, $default = null)
    {
        return (
            $key && is_array($this->value)
                ? $this->value[$key] ?? null
                : $this->value
            ) ?? $default;
    }

    /**
     * @param string|null $key
     * @param $default
     * @return mixed|null
     */
    public function defaultValue(string $key = null, $default = null)
    {
        return (
            $key && is_array($this->defaultValue)
                ? $this->defaultValue[$key] ?? null
                : $this->defaultValue
            ) ?? $default;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    abstract protected function setQuery(Builder $query, $value): Builder;

    /**
     * @param \Closure $queryCallback
     * @return $this
     */
    public function setQueryCallback(Closure $queryCallback): self
    {
        $this->queryCallback = $queryCallback;

        return $this;
    }

    /**
     * @param string $group
     *
     * @return \Modules\DataTable\Core\Abstracts\DataTableFilter
     * @throws \Exception
     */
    public function setGroup(string $group): self
    {
        if (!in_array($group, self::Groups)) {
            throw new Exception("Group {$group} not defined.");
        }

        $this->group = $group;

        return $this;
    }
}