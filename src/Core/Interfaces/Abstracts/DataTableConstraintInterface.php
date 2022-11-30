<?php

namespace Modules\DataTable\Core\Interfaces\Abstracts;


use Illuminate\Database\Eloquent\Builder;
use Kirschbaum\PowerJoins\PowerJoinClause;

/**
 * @package Modules\DataTable\Core\Abstracts
 */
interface DataTableConstraintInterface
{
    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $term
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Kirschbaum\PowerJoins\PowerJoinClause
     */
    public function query(Builder $query, string $term): Builder|PowerJoinClause;

    /**
     * @return string|null
     */
    public function extractAttributeWithoutRelationship(): string|null;
}