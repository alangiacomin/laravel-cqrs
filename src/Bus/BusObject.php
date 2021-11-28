<?php

namespace Alangiacomin\LaravelCqrs\Bus;

use Alangiacomin\PhpUtils\Guid;
use Exception;

abstract class BusObject
{
    public string $id;

    public string $correlationId;

    public $connection;

    public function __construct($props = null)
    {
        $this->id = "";
        $this->correlationId = "";

        if (isset($props)) {
            if (is_object($props)) {
                $props = get_object_vars($props);
            }
            foreach ($props as $key => $value) {
                $this->$key = $value;
            }
        }

        $this->assignNewId();

        if (!isset($this->correlationId) || $this->correlationId == "") {
            $this->correlationId = Guid::newGuid();
        }
    }

    public function __get($name)
    {
        throw new Exception("Property not readable");
    }

    public function __set($name, $value)
    {
        throw new Exception("Property not writeable");
    }

    public function clone(): BusObject
    {
        $this->assignNewId();
        return $this;
    }

    public function payload(): string
    {
        return json_encode($this->props());
    }

    public function name(): string
    {
        $classNameWithNamespace = $this->class();
        $className = substr($classNameWithNamespace, strrpos($classNameWithNamespace, "\\") + 1);
        return $className;
    }

    public function class(): string
    {
        return get_class($this);
    }

    public function handlerName(): string
    {
        return $this->name() . "Handler";
    }

    public function props(): array
    {
        return get_object_vars($this);
    }

    private function assignNewId(): void
    {
        $this->id = Guid::newGuid();
    }
}
