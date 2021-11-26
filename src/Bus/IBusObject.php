<?php

namespace Alangiacomin\LaravelCqrs\Bus;

interface IBusObject
{
    function name(): string;

    function handlerName(): string;

    function class(): string;

    function props(): array;

    function payload(): string;

    function clone(): BusObject;
}
