<?php

namespace App\Vo;

use ReflectionClass;
use ReflectionProperty;

abstract class Vo
{
    /**
     * @param mixed $data
     * @return void
     */
    public abstract function bind(mixed $data): void;

    /**
     * @return array
     */
    public function toArray(): array
    {
        return json_decode(json_encode($this), true);
    }

    /**
     * @param mixed $list
     * @param string $class
     * @return Vo[]|array
     */
    protected function bindList(mixed $list, string $class): array
    {
        if (is_null($list)) {
            return [];
        }

        return array_map(function ($item) use ($class) {
            /** @var Vo $class */
            $object = new $class;
            $object->bind($item);

            return $object;
        }, $list);
    }

    public function __get($name) {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
    }

    public function __set($name, $value) {
        if (property_exists($this, $name)) {
            $this->$name = $value;
        }
    }
    
    public function getAllProperties(): array {
        $array = [];
        $reflection = new ReflectionClass($this);
        foreach ($reflection->getProperties(ReflectionProperty::IS_PRIVATE | ReflectionProperty::IS_PROTECTED | ReflectionProperty::IS_PUBLIC) as $property) {
            $property->setAccessible(true);
            $array[$property->getName()] = $property->getValue($this);
        }
        return $array;
    }
}
