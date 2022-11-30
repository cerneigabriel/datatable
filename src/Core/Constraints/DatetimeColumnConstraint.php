<?php

namespace Modules\DataTable\Core\Constraints;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;
use Modules\DataTable\Core\Abstracts\DataTableConstraint;

/**
 * @package Modules\DataTable\Core\Filters
 */
class DatetimeColumnConstraint extends DataTableConstraint
{
    /** @var string */
    public string $type = 'datetime';

    /** @var string */
    public string $start_date_attribute;

    /** @var string */
    public string $end_date_attribute;

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function setQuery(Builder $query, $value): Builder
    {
        $value = $this->getDateInterval($value);
        if (!isset($this->start_date_attribute)) $this->start_date_attribute = $this->extractRelationshipFromAttribute() ? $this->extractAttributeWithoutRelationship() : $this->attribute;
        if (!isset($this->end_date_attribute)) $this->end_date_attribute = $this->extractRelationshipFromAttribute() ? $this->extractAttributeWithoutRelationship() : $this->attribute;

        if (isset($value['start_date']) && isset($value['end_date'])) {
            return $query
                ->whereDate($query->getModel()->getTable() . '.' . $this->start_date_attribute, '>=', Carbon::parse($value['start_date'])->toDateString())
                ->whereDate($query->getModel()->getTable() . '.' . $this->end_date_attribute, '<=', Carbon::parse($value['end_date'])->toDateString());
        } elseif (isset($value['start_date'])) {
            $query->whereDate($query->getModel()->getTable() . '.' . $this->start_date_attribute, Carbon::parse($value['start_date'])->toDateString());
        }

        return $query;
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
     * @param $value
     * @return bool
     */
    public function canQuery($value): bool
    {
        $value = $this->getDateInterval($value);
        return isset($value['start_date']) && isset($value['end_date']) || isset($value['start_date']);
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
}