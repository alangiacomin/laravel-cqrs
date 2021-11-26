<?php

namespace Alangiacomin\LaravelCqrs\Bus;

use Alangiacomin\LaravelCqrs\Bus\Bus;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

abstract class BusHandler implements ShouldQueue
{
    use InteractsWithQueue;

    protected $busObject;

    protected int $maxRetries = 3;

    protected bool $isActive = true;

    public function apply(): void
    {
        Bus::logNew($this->busObject);
        $this->setTypedObject();
        $this->execute();
        Bus::logExecuted($this->busObject);
    }

    protected abstract function execute(): void;

    protected abstract function setTypedObject(): void;

    /**
     * Handle the job
     *
     * @return void
     */
    protected function handleObject($busObject = null): void
    {
        if (isset($busObject)) {
            $this->busObject = $busObject;
        }

        $retry = 0;

        while ($this->isActive) {
            try {
                if (++$retry > $this->maxRetries) {
                    break;
                }
                $this->apply();
                $this->isActive = false;
            } catch (\Throwable $ex) {
                $this->failed($ex);
            }
        };
    }

    protected function notifyFailures(IBusObject $busObject, \Throwable $ex): void
    {
        $this->isActive = true;
        if (isset($this->busObject)) {
            $this->busObject = $this->busObject->clone();
        }
    }

    protected function failed(\Throwable $ex): void
    {
        $this->isActive = false;
        Bus::logFailed($this->busObject, $ex);
        $this->notifyFailures($this->busObject, $ex);
    }
}
