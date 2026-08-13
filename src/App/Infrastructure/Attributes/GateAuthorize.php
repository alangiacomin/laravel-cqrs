<?php

namespace AlanGiacomin\LaravelCqrs\App\Infrastructure\Attributes;

use Attribute;
use UnitEnum;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
class GateAuthorize
{
    public function __construct(
        public UnitEnum|string $ability,
        public array $arguments = []
    ) {}
}
