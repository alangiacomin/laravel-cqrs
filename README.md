# Laravel CQRS

Laravel extension for CQRS

## Requirements

- PHP >= 8
- Laravel Application

## Installation

Install package
```
composer require alangiacomin/laravel-cqrs
php artisan vendor:publish --provider=Alangiacomin\LaravelCqrs\LaravelCqrsServiceProvider
php artisan migrate
```

Remove ```app/Http/Controllers/Controller.php```
```
rm app\Http\Controllers\Controller.php
```

If events should be managed by database, make following configurations.\
Edit ```.env``` file:
```
# Set database connection
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=

# Set queue type
QUEUE_CONNECTION=database
```

Migrate database
```
php artisan migrate
```

## Usage

### Controller

```
php artisan make:controller <name>
```

### Command and handler

```
php artisan cqrs:command <name>
```

### Event and handler

```
php artisan cqrs:event <name>
```

## Example

_app\HttpControllers\MyController.php_
```php
<?php

namespace App\Http\Controllers;

use Alangiacomin\LaravelCqrs\Controllers\Controller;
use App\Commands\MyCommand;
use Illuminate\Http\Request;

class MyController extends Controller
{
    public function myAction(Request $request)
    {
        $this->executeCommand(new MyCommand(array(
            'prop' => $request->query('mykey'),
        )));
    }
}
```

_app\Commands\MyCommand.php_
```php
<?php

namespace App\Commands;

use Alangiacomin\LaravelCqrs\Commands\Command;

class MyCommand extends Command
{
    /**
     * Property description
     *
     * @var string
     */
    public string $prop;
}
```

_app\CommandHandlers\MyCommandHandler.php_
```php
<?php

namespace App\CommandHandlers;

use Alangiacomin\LaravelCqrs\CommandHandlers\CommandHandler;
use App\Commands\MyCommand;
use App\Events\MyEvent;

class MyCommandHandler extends CommandHandler
{
    /**
     * The command
     *
     * @var MyCommand
     */
    public MyCommand $command;

    /**
     * Execute the command
     *
     * @return  void
     */
    public function execute(): void
    {
        echo "correlation id: " . $this->command->correlationId . "\n";

        $e = new MyEvent();
        $e->prop = $this->command->prop;
        $e->correlationId = $this->command->correlationId;
        $this->publish($e);

        $this->publish(new MyEvent(array(
            'prop' => 'second prov value',
            'correlationId' => $this->command->correlationId
        )));
    }
}
```

_app\Events\MyEvent.php_
```php
<?php

namespace App\Events;

use Alangiacomin\LaravelCqrs\Events\Event;

class MyEvent extends Event
{
    /**
     * Property description
     *
     * @var string
     */
    public string $prop;
}
```

_app\EventHandlers\MyEventHandler.php_
```php
<?php

namespace App\EventHandlers;

use Alangiacomin\LaravelCqrs\EventHandlers\EventHandler;
use App\Events\MyEvent;

class MyEventHandler extends EventHandler
{
    /**
     * The event
     *
     * @var MyEvent
     */
    public MyEvent $event;

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct(MyEvent $event)
    {
        //
    }

    /**
     * Execute the event
     *
     * @return void
     */
    public function execute(): void
    {
        echo "execute event\n";
        echo print_r($this->event, true) . "\n";
        //
    }
}
```

_routes\web.php_
```php
<?php

use App\Http\Controllers\MyController;

Route::get('/myAction', [MyController::class, 'myAction']);
```
