<?php

namespace Tests\Unit\Infrastructure\Exceptions;

use AlanGiacomin\LaravelCqrs\Infrastructure\Exceptions\BadRequestException;
use AlanGiacomin\LaravelCqrs\Infrastructure\Exceptions\CommandException;
use AlanGiacomin\LaravelCqrs\Infrastructure\Exceptions\ForbiddenException;
use AlanGiacomin\LaravelCqrs\Infrastructure\Exceptions\NotFoundException;
use AlanGiacomin\LaravelCqrs\Infrastructure\Exceptions\UnauthorizedException;
use AlanGiacomin\LaravelCqrs\Infrastructure\Exceptions\ValidationException;
use PHPUnit\Framework\TestCase;

class ExceptionStatusCodesTest extends TestCase
{
    public function test_all_application_exceptions_expose_the_expected_http_status_code(): void
    {
        $this->assertSame(400, (new BadRequestException())->getStatusCode());
        $this->assertSame(500, (new CommandException())->getStatusCode());
        $this->assertSame(403, (new ForbiddenException())->getStatusCode());
        $this->assertSame(404, (new NotFoundException())->getStatusCode());
        $this->assertSame(401, (new UnauthorizedException())->getStatusCode());

        $validation = new ValidationException('Validation failed', ['email' => ['invalid']]);

        $this->assertSame(422, $validation->getStatusCode());
        $this->assertSame(['email' => ['invalid']], $validation->errors);
    }
}
