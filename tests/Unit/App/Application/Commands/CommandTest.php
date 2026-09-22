<?php

namespace Tests\Unit\App\Application\Commands;

use AlanGiacomin\LaravelCqrs\App\Application\Commands\Command;
use Illuminate\Contracts\Queue\Job;
use Illuminate\Support\Facades\Event;
use Mockery;
use Tests\TestCase;

class CommandTest extends TestCase
{
    public function test_new_command_is_not_running_on_a_queue(): void
    {
        $command = new TestCommand();

        $this->assertFalse($command->isRunningOnQueue());
    }

    public function test_command_is_running_on_a_queue_when_a_job_is_attached(): void
    {
        $command = new TestCommand();
        $command->job = $this->queueJob();

        $this->assertTrue($command->isRunningOnQueue());
    }

    public function test_emit_if_async_does_not_emit_when_command_is_synchronous(): void
    {
        Event::fake();

        (new TestCommand())->emitIfAsync('command-event');

        Event::assertNothingDispatched();
    }

    public function test_emit_if_async_emits_when_command_is_running_on_a_queue(): void
    {
        Event::fake();

        $command = new TestCommand();
        $command->job = $this->queueJob();
        $command->emitIfAsync('command-event', 'payload');

        Event::assertDispatched('command-event');
    }

    private function queueJob(): Job
    {
        /** @var Job $job */
        $job = Mockery::mock(Job::class);

        return $job;
    }
}

class TestCommand extends Command
{
}
