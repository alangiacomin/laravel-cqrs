<?php

namespace Alangiacomin\LaravelCqrs\Bus;

use Alangiacomin\LaravelCqrs\Commands\ICommand;
use Alangiacomin\LaravelCqrs\Events\IEvent;
use Alangiacomin\PhpUtils\DateTime;
use Illuminate\Support\Facades\DB;

class Bus
{
    public static function sendCommand(ICommand $command): void
    {
        $handlerClass = config('cqrs.namespaces.commandHandlers') . '\\' . $command->handlerName();
        $handlerClass::dispatchSync($command);
    }

    public static function sendEvent(IEvent $event): void
    {
        $classNameWithNamespace = $event->class();
        $classNameWithNamespace::dispatch($event->props());
    }

    public static function logNew(IBusObject $object): void
    {
        if (!isset($object)) {
            return;
        }

        DB::table('cqrs_jobs')->insert(
            [
                'object_id' => $object->id,
                'correlation_id' => $object->correlationId,
                'class' => $object->class(),
                'payload' => $object->payload(),
                'created_at' => DateTime::now()
            ]
        );
    }

    public static function logExecuted(IBusObject $object): void
    {
        if (!isset($object)) {
            return;
        }

        DB::table('cqrs_jobs')
            ->where('object_id', $object->id)
            ->update(
                [
                    'done_at' => DateTime::now()
                ]
            );
    }

    public static function logFailed(IBusObject $object, \Throwable $exception): void
    {
        if (!isset($object)) {
            return;
        }

        DB::table('cqrs_jobs')
            ->where('object_id', $object->id)
            ->update(
                [
                    'done_at' => DateTime::now(),
                    'message' => $exception->getMessage(),
                    'trace' => $exception->getTrace()
                ]
            );
    }
}
