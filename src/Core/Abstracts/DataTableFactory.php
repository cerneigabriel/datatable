<?php

namespace Modules\DataTable\Core\Abstracts;

use ReflectionMethod;

/**
 * Class DataTableFactory
 *
 * @package Modules\DataTable\Core\Abstracts
 */
abstract class DataTableFactory
{
    /** @var array|string[] */
    private static array $factory_classes;

    /**
     * @static
     * @param $calledType
     * @param $arguments
     * @return mixed
     * @throws \ReflectionException
     */
    public static function __callStatic($calledType, $arguments)
    {
        if (!in_array($calledType, array_keys(static::$factory_classes))) {
            return static::missingClassTypeException($calledType);
        }

        $factoryClass = static::$factory_classes[$calledType];


        $params = (new ReflectionMethod($factoryClass, '__construct'))->getParameters();
        $params_collection = collect($params);
        $constructor_arguments = [];
        $corrupt_arguments = [];

        foreach ($arguments as $key => $value) {
            $param = $params_collection->filter(function ($param) use ($key) {
                return $param->getPosition() === $key;
            })->first();
            if ($param) {
                if (!$param->hasType() || ($param->getType()->allowsNull() || (!$param->getType()->allowsNull() && $param->getType()->getName() === gettype($value)))) {
                    $constructor_arguments[$param->getName()] = $value;
                } else {
                    $corrupt_arguments[] = [
                        'param' => $param,
                        'given_value' => $value,
                        'given_value_is_null' => is_null($value),
                        'given_type' => gettype($value)
                    ];
                }
            }
        }

        $missing_arguments = $params_collection->filter(function ($param) use ($constructor_arguments) {
            return !$param->isOptional() && !in_array($param->getName(), array_keys($constructor_arguments));
        })->toArray();

        if (count($corrupt_arguments)) {
            return static::corruptClassArguments(
                $factoryClass,
                '__construct',
                $corrupt_arguments,
                $params
            );
        }

        if (count($missing_arguments)) {
            return static::missingClassArguments(
                $factoryClass,
                '__construct',
                $missing_arguments,
                $params
            );
        }

        return new $factoryClass(...array_values($constructor_arguments));
    }

    /**
     * @static
     * @param string $type
     * @return mixed
     * @throws \Exception
     */
    public static function missingClassTypeException(string $type): mixed
    {
        return throw new Exception("'$type' doesn't exist in available class types: [ '" . collect(self::$factory_classes)->keys()->implode("', '") . "' ]");
    }

    /**
     * @static
     * @param string $class
     * @param string $called_function
     * @param array $corrupt_arguments
     * @param array $arguments
     * @return mixed
     */
    public static function corruptClassArguments(string $class, string $called_function, array $corrupt_arguments, array $arguments): mixed
    {
        $corrupt_arguments = collect($corrupt_arguments)->implode(function ($item) {
            return $item['param']->hasType()
                ? (
                    ($item['param']->getType()->allowsNull() === $item['given_value_is_null'] ?: "Argument {$item['param']} is required.") .
                    ($item['param']->getType()->getName() === $item['given_type'] ?: "Expected type '{$item['param']->getType()->getName()}', found '{$item['given_type']}' for attribute {$item['param']}.")
                ) : '';
        }, ' ');
        return throw new InvalidArgumentException("$corrupt_arguments The class method requires the following $class::$called_function(" . implode(', ', $arguments) . ")");
    }

    /**
     * @static
     * @param string $class
     * @param string $called_function
     * @param array $missing_arguments
     * @param array $arguments
     * @return mixed
     */
    public static function missingClassArguments(string $class, string $called_function, array $missing_arguments, array $arguments): mixed
    {
        $missing_arguments = collect($missing_arguments)->implode(function ($item) {
            return "Parameter #{$item->getPosition()}";
        }, ', ');
        return throw new InvalidArgumentException("Arguments ($missing_arguments) are missing from the class method $class::$called_function(" . implode(', ', $arguments) . ")");
    }
}