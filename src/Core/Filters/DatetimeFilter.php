<?php

namespace Modules\DataTable\Core\Filters;

use Carbon\Carbon;
use Closure;
use Exception;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Validator;
use Modules\DataTable\Core\Abstracts\DataTableFilter;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class DatetimeColumn
 *
 * @package Modules\DataTable\Core\Filters
 */
class DatetimeFilter extends DataTableFilter
{
    /** @var string */
    public string $type = 'datetime';

    /** @var string */
    public string $format = 'm/d/Y H:i:S';

    /** @var bool */
    public bool $range = false;

    /** @var string */
    public string $start_date_attribute;

    /** @var string */
    public string $end_date_attribute;

    /**
     * @param string $format
     * @return $this
     */
    public function setFormat(string $format): self
    {
        $this->format = $format;

        return $this;
    }

    /**
     * @return $this
     */
    public function enableRange(): self
    {
        $this->range = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function disableRange(): self
    {
        $this->range = false;

        return $this;
    }

    /**
     * @param string $start_date_attribute
     * @return $this
     */
    public function setStartDateAttribute(string $start_date_attribute): self
    {
        $this->start_date_attribute = $start_date_attribute;

        return $this;
    }

    /**
     * @param string $end_date_attribute
     * @return $this
     */
    public function setEndDateAttribute(string $end_date_attribute): self
    {
        $this->end_date_attribute = $end_date_attribute;

        return $this;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function setQuery(Builder $query, $value): Builder
    {
        $value = $this->getDateInterval($value);
        if (!isset($this->start_date_attribute)) $this->start_date_attribute = $this->attribute;
        if (!isset($this->end_date_attribute)) $this->end_date_attribute = $this->attribute;

        if ($this->range) {

            if (isset($value['start_date'])) {
                $query->whereDate($query->getModel()->getTable() . '.' . $this->start_date_attribute, '>=', Carbon::parse($value['start_date'])->toDateString());
            }

            if (isset($value['end_date'])) {
                $query->whereDate($query->getModel()->getTable() . '.' . $this->end_date_attribute, '<=', Carbon::parse($value['end_date'])->toDateString());
            }

            return $query;
        }

        if (isset($value['start_date'])) {
            $query->whereDate($query->getModel()->getTable() . '.' . $this->start_date_attribute, Carbon::parse($value['start_date'])->toDateString());
        }

        return $query;
    }

    /**
     * @param $dates
     * @return null[]
     */
    public function getDateInterval($dates)
    {
        $dates = explode(' to ', $dates);

        if (isset($dates[0])) {
            $validDate = Validator::make(['start_date' => $dates[0]], ['start_date' => 'date']);

            try {
                throw_if($validDate->fails(), new Exception());
                $start_date = Carbon::parse($dates[0])->toDateTimeString();
            } catch (\Throwable $throwable) {
                $start_date = null;
            }
        } else $start_date = null;

        if (isset($dates[1])) {
            $validDate = Validator::make(['start_date' => $dates[1]], ['start_date' => 'date']);

            try {
                throw_if($validDate->fails(), new Exception());
                $end_date = Carbon::parse($dates[1])->toDateTimeString();
            } catch (\Throwable $throwable) {
                $end_date = null;
            }
        } else $end_date = null;

        return [
            'start_date' => $start_date,
            'end_date' => $end_date
        ];
    }

    /**
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\Support\Htmlable|string|\Closure
     */
    protected function render(): View|Htmlable|string|Closure
    {
        return view('datatable::filters.datetime-filter');
    }
}