<?php

namespace Alangiacomin\LaravelCqrs\Commands;

use Alangiacomin\LaravelCqrs\Bus\BusObject;

abstract class Command extends BusObject implements ICommand
{
}
