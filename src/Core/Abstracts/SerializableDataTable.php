<?php

namespace Modules\DataTable\Core\Abstracts;

use Throwable;
use ReflectionClass;
use Livewire\Wireable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Laravel\SerializableClosure\SerializableClosure;
use Modules\DataTable\Core\Collections\ColumnsCollection;
use Modules\DataTable\Core\Collections\ActionsCollection;
use Modules\DataTable\Core\Collections\FiltersCollection;
use Modules\DataTable\Core\Collections\ConstraintsCollection;

/**
 * Class SerializableDataTable
 *
 * @package Modules\DataTable\Core\Abstracts
 */
abstract class SerializableDataTable implements Wireable
{
    /** @var string */
    public const SERIALIZATION_SECRET_KEY = 'datatables';

    /** @var array|string[] */
    public const CUSTOM_COLLECTIONS = [
        ColumnsCollection::class,
        ActionsCollection::class,
        FiltersCollection::class,
        ConstraintsCollection::class,
    ];

    /** @var string */
    public string $initiatedClass;

    /**
     * @return void
     */
    public function mountSerialization()
    {
        $this->initiatedClass = static::class;
    }

    /**
     *
     * @throws \Laravel\SerializableClosure\Exceptions\PhpVersionNotSupportedException
     */
    public function toLivewire()
    {
        if (config('datatable.debug')) {
            Log::channel('datatable')->debug('core.datatable.serialization.to_livewire', [
                'datatable' => $this,
//                'stackTrace' => debug_backtrace(),
            ]);
        }

        return serialize($this->__serialize());
    }

    /**
     * @param $value
     * @return static
     * @static
     * @throws \ReflectionException
     */
    public static function fromLivewire($value): static
    {
        try {
            $data = unserialize($value, [
                'allowed_classes' => true,
            ]);

            $reflectionClass = new \ReflectionClass($data['initiatedClass'] ?? static::class);

            $newInstance = $reflectionClass->newInstanceWithoutConstructor();

            if (isset($data)) {
                $newInstance->__unserialize($data);
            }

            if (config('datatable.debug')) {
                Log::channel('datatable')->debug('core.datatable.serialization.from_livewire', [
                    'datatable' => $newInstance,
//                    'stackTrace' => debug_backtrace(),
                ]);
            }

            return $newInstance;
        } catch (Throwable $exception) {
            Log::channel('datatable')->emergency('core.datatable.serialization.from_livewire', [
                'datatable' => $data ?? $value,
                'exception' => $exception
            ]);

            return new static();
        }
    }

    /**
     * @throws \Laravel\SerializableClosure\Exceptions\PhpVersionNotSupportedException
     */
    public function __serialize(): array
    {
        SerializableClosure::setSecretKey(static::SERIALIZATION_SECRET_KEY);
        $reflect = new ReflectionClass($this);
        $props = $reflect->getProperties();

        $serialized = collect();

        try {
            foreach ($props as $prop) {
                if ((static::$serializationIgnoreProperties ?? false) && in_array($prop->getName(), static::$serializationIgnoreProperties)) {
                    continue;
                }

                $propTypes = method_exists($prop->getType(), 'getName')
                    ? collect([['type' => $prop->getType()->getName()]])
                    : collect($prop->getType()->getTypes())->mapWithKeys(fn ($item) => ['type' => $item->getName()]);

                    if (
                        $propTypes->isNotEmpty() &&
                        $propTypes->where('type', 'Closure')->isNotEmpty() &&
                        isset($this->{$prop->getName()})
                    ) {
                        $property = 'Closure_' . $prop->getName();
                        $data = serialize(new SerializableClosure($this->{$prop->getName()}));
                    } elseif (
                        $propTypes->isNotEmpty() &&
                        isset($this->{$prop->getName()}) &&
                        $propTypes->whereIn('type', [Collection::class, ...static::CUSTOM_COLLECTIONS])->isNotEmpty()
                    ) {
                        if ($propTypes->where('type', Collection::class)->isNotEmpty()) {
                            $property = $prop->getName();
                            $data = $this->{$prop->getName()}->jsonSerialize();
                        }

                        if ($propTypes->whereIn('type', static::CUSTOM_COLLECTIONS)->isNotEmpty()) {
                            $property = $prop->getName();
                            $data = $this->{$prop->getName()}->serialize();
                        }
                    } elseif (isset($this->{$prop->getName()})) {
                        $property = $prop->getName();
                        $data = $this->{$prop->getName()};
                    }

                    if (isset($property, $data)) {
                        $serialized->put($property, $data);
                    }
            }
        } catch (Throwable $exception) {
            Log::channel('datatable')->emergency('core.datatable.serialization.__serialize', [
                'datatable' => $this,
                'exception' => $exception
            ]);
        }

        return $serialized->toArray();
    }

    /**
     * @param array $data
     *
     * @return void
     */
    public function __unserialize(array $data): void
    {
        SerializableClosure::setSecretKey(static::SERIALIZATION_SECRET_KEY);
        $reflect = new ReflectionClass($this);
        $props = $reflect->getProperties();

        try {
            foreach ($props as $prop) {
                if ((static::$serializationIgnoreProperties ?? false) && in_array($prop->getName(), static::$serializationIgnoreProperties)) {
                    continue;
                }

                $propTypes = method_exists($prop->getType(), 'getName')
                    ? collect([['type' => $prop->getType()->getName()]])
                    : collect($prop->getType()->getTypes())->mapWithKeys(fn ($item) => ['type' => $item->getName()]);

                $serializedClosure = $data["Closure_{$prop->getName()}"] ?? null;

                if (
                    $propTypes->isNotEmpty() &&
                    $propTypes->where('type', 'Closure')->isNotEmpty() &&
                    $serializedClosure
                ) {
                    $this->{$prop->getName()} = unserialize($serializedClosure, ['allowed_classes' => true])->getClosure();
                } elseif (
                    $propTypes->isNotEmpty() &&
                    ($collections = $propTypes->whereIn('type', [Collection::class, ...static::CUSTOM_COLLECTIONS]))->isNotEmpty()
                ) {
                    $collection = $collections->first()['type']::make($data[$prop->getName()]);

                    if ($propTypes->whereIn('type', static::CUSTOM_COLLECTIONS)->isNotEmpty()) {
                        $collection = $collection->unserialize();
                    }

                    $this->{$prop->getName()} = $collection;
                } elseif (isset($data[$prop->getName()])) {
                    $this->{$prop->getName()} = $data[$prop->getName()];
                }
            }
        } catch (Throwable $exception) {
            Log::channel('datatable')->emergency('core.datatable.serialization.__unserialize', [
                'datatable' => $data,
                'exception' => $exception
            ]);
        }
    }

    /**
     * @param object       $obj
     * @param array|string $classes
     *
     * @return bool
     */
    private function instanceOfAny(object $obj, array|string $classes = []): bool
    {
        if (is_string($classes)) {
            return get_class($obj) === $classes;
        }

        foreach ($classes as $class) {
            if (get_class($obj) === $class) {
                return true;
            }
        }

        return false;
    }

//    /**
//     * Convert the model instance to an array.
//     *
//     * @return array
//     */
//    public function toArray(): array
//    {
//        return $this->__serialize();
//    }
//
//    /**
//     * Convert the object into something JSON serializable.
//     *
//     * @return mixed
//     */
//    public function jsonSerialize(): mixed
//    {
//        return $this->toArray();
//    }
//
//    /**
//     * Convert the model instance to JSON.
//     *
//     * @param  int  $options
//     *
//     * @return string
//     *
//     * @throws \Illuminate\Database\Eloquent\JsonEncodingException|\JsonException
//     */
//    public function toJson($options = 0)
//    {
//        $json = json_encode($this->jsonSerialize(), JSON_THROW_ON_ERROR | $options);
//
//        if (json_last_error() !== JSON_ERROR_NONE) {
//            throw new JsonEncodingException(json_last_error_msg());
//        }
//
//        return $json;
//    }
}