<?php

namespace Modules\DataTable\Core\Eloquent;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Kirschbaum\PowerJoins\PowerJoins;
use Modules\DataTable\Core\Eloquent\Scopes\OwnScope;
use Spatie\Permission\Contracts\Role;

/**
 * Trait EloquentDataTable
 *
 * Power Joins Methods
 *
 * @method Builder joinRelationship($relationName, $callback = null, $joinType = 'join', $useAlias = false, bool $disableExtraConditions = false)
 * @method Builder joinRelationshipUsingAlias($relationName, $callback = null, bool $disableExtraConditions = false)
 * @method Builder leftJoinRelationshipUsingAlias($relationName, $callback = null, bool $disableExtraConditions = false)
 * @method Builder rightJoinRelationshipUsingAlias($relationName, $callback = null, bool $disableExtraConditions = false)
 * @method Builder joinRelation($relationName, $callback = null, $joinType = 'join', $useAlias = false, bool $disableExtraConditions = false)
 * @method Builder leftJoinRelationship($relation, $callback = null, $useAlias = false, bool $disableExtraConditions = false)
 * @method Builder leftJoinRelation($relation, $callback = null, $useAlias = false, bool $disableExtraConditions = false)
 * @method Builder rightJoinRelationship($relation, $callback = null, $useAlias = false, bool $disableExtraConditions = false)
 * @method Builder rightJoinRelation($relation, $callback = null, $useAlias = false, bool $disableExtraConditions = false)
 * @method Builder joinNestedRelationship(string $relationships, $callback = null, $joinType = 'join', $useAlias = false, bool $disableExtraConditions = false)
 * @method Builder orderByPowerJoins($sort, $direction = 'asc', $aggregation = null, $joinType = 'join')
 * @method Builder orderByLeftPowerJoins($sort, $direction = 'asc')
 * @method Builder orderByPowerJoinsCount($sort, $direction = 'asc')
 * @method Builder orderByLeftPowerJoinsCount($sort, $direction = 'asc')
 * @method Builder orderByPowerJoinsSum($sort, $direction = 'asc')
 * @method Builder orderByLeftPowerJoinsSum($sort, $direction = 'asc')
 * @method Builder orderByPowerJoinsAvg($sort, $direction = 'asc')
 * @method Builder orderByLeftPowerJoinsAvg($sort, $direction = 'asc')
 * @method Builder orderByPowerJoinsMin($sort, $direction = 'asc')
 * @method Builder orderByLeftPowerJoinsMin($sort, $direction = 'asc')
 * @method Builder orderByPowerJoinsMax($sort, $direction = 'asc')
 * @method Builder orderByLeftPowerJoinsMax($sort, $direction = 'asc')
 * @method Builder powerJoinHas($relation, $operator = '>=', $count = 1, $boolean = 'and', $callback = null)
 * @method Builder hasNestedUsingJoins($relations, $operator = '>=', $count = 1, $boolean = 'and', Closure $callback = null)
 * @method Builder powerJoinDoesntHave($relation, $boolean = 'and', Closure $callback = null)
 * @method Builder powerJoinWhereHas($relation, $callback = null, $operator = '>=', $count = 1)
 *
 * @package Modules\DataTable\Core\Eloquent\Traits
 */
trait EloquentDataTable
{
    use PowerJoins;
}