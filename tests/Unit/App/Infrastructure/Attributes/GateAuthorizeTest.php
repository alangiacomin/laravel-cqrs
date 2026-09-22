<?php

namespace Tests\Unit\App\Infrastructure\Attributes;

use AlanGiacomin\LaravelCqrs\App\Infrastructure\Attributes\GateAuthorize;
use PHPUnit\Framework\TestCase;

class GateAuthorizeTest extends TestCase
{
    public function test_attribute_exposes_ability_and_arguments(): void
    {
        $attribute = new GateAuthorize('edit-post', ['post' => 42]);

        $this->assertSame('edit-post', $attribute->ability);
        $this->assertSame(['post' => 42], $attribute->arguments);
    }
}
