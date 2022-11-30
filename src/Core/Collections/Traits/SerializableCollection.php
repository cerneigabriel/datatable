<?php

namespace Modules\DataTable\Core\Collections\Traits;

trait SerializableCollection
{
    public function serialize()
    {
        return $this->map(function ($item) {
            return $item->__serialize();
        })->toArray();
    }

    public function unserialize()
    {
        return $this->map(function ($item) {
            if ($item['initiatedClass'] ?? false) {
                $reflectionClass = new \ReflectionClass($item['initiatedClass']);

                $newInstance = $reflectionClass->newInstanceWithoutConstructor();

                $newInstance->__unserialize($item);

                return $newInstance;
            }

            return $item;
        });
    }
}