<?php

namespace Tests\Unit;

use AlanGiacomin\LaravelCqrs\LaravelCqrsServiceProvider;
use Tests\TestCase;

class LaravelCqrsServiceProviderTest extends TestCase
{
    public function test_register_merges_typescript_transformer_configuration(): void
    {
        $provider = new LaravelCqrsServiceProvider(app());
        $provider->register();

        $this->assertSame(
            [app_path()],
            config('typescript-transformer.auto_discover_types')
        );
    }
}
