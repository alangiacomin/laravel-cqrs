<?php

namespace AlanGiacomin\LaravelCqrs\App\Application\Commands;

use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;

abstract class Command
{
    use InteractsWithQueue;
    use Queueable;

    /**
     * Determine if the command is running asynchronously on a queue.
     */
    public function isRunningOnQueue(): bool
    {
        // return isset($this->job) && ($this->job !== null);
        return $this->job !== null;
    }

    /**
     * Dispatch an event only if the command is executing asynchronously on a queue.
     */
    public function emitIfAsync(object|string $event, mixed ...$payload): void
    {
        if ($this->isRunningOnQueue()) {
            event($event, ...$payload);
        }
    }
}
