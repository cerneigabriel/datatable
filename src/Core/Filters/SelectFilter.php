<?php

namespace Modules\DataTable\Core\Filters;

use Closure;
use Exception;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Modules\DataTable\Core\Abstracts\DataTableFilter;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class MultipleColumn
 *
 * @package Modules\DataTable\Core\Filters
 */
class SelectFilter extends DataTableFilter
{
    /** @var string */
    public string $type = 'select';

    /** @var array */
    public array $options = [];

    /**
     * @param array|\Closure $options
     * @return $this
     * @throws \Exception
     */
    public function setOptions(array|Closure $options): self
    {
        if ($options instanceof Closure) {
            $options = $options();
        }

        if (is_array($options) || is_object($options)) {
            $this->options = collect($options)
                ->mapWithKeys(function ($label, $value) {
                    if (is_string($label) || is_numeric($label)) {
                        return [$value => $label];
                    }

                    if (is_numeric($value) && (is_array($label) || is_object($label))) {
                        $label = (object)$label;
                        if (isset($label->label) && isset($label->value)) {
                            return [$label->value => $label->label];
                        }
                    }

                    return [$value => null];
                })
                ->filter(function ($value) {
                    return !is_null($value);
                })
                ->toArray();


            return $this;
        }

        return throw new Exception("The options passed are not formed correctly for filter $this->label");
    }

    /**
     * @param string|null $nullValueLabel
     * @return $this
     */
    public function setOptionsNullValue(string $nullValueLabel = null): static
    {
        $this->options = [null => $nullValueLabel ?? $this->placeholder] + $this->options;

        return $this;
    }

    /**
     * @return array
     */
    public function options(): array
    {
        return $this->options;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $value
     * @return \Illuminate\Database\Eloquent\Builder
     * @throws \Exception
     */
    protected function setQuery(Builder $query, $value): Builder
    {
        return $query->where($query->getModel()->getTable() . '.' . $this->extractAttributeWithoutRelationship(), $value);
    }

    /**
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\Support\Htmlable|string|\Closure
     */
    protected function render(): View|Htmlable|string|Closure
    {
        return view('datatable::filters.select-filter');
    }
}