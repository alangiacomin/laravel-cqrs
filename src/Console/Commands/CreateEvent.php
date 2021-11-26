<?php

namespace Alangiacomin\LaravelCqrs\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

class CreateEvent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cqrs:event {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create new Event and EventHandler classes';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $commandName = $this->argument('name');

        try {
            $this->createEventFile($commandName);
            $this->createEventHandlerFile($commandName);
            $this->info("Done!");
        } catch (\Throwable $th) {
            $this->newline();
            $this->error("Failed");
            $this->newline();
            $this->line($th->getMessage());
        }
    }

    private function createEventFile($name)
    {
        $stubfile = __DIR__ . "\\stubs\\Event.php.stub";
        $newfile = base_path() . "\\" . Config::get('cqrs.namespaces.events') . "\\" . $name . ".php";
        if (!is_dir(dirname($newfile))) {
            mkdir(dirname($newfile), 0777, true);
        }

        $content = file_get_contents($stubfile);
        $content = $this->stubReplace("namespace", Config::get('cqrs.namespaces.events'), $content);
        $content = $this->stubReplace("name", $name, $content);
        file_put_contents($newfile, $content);

        $this->comment($name . ".php created");
    }

    private function createEventHandlerFile($name)
    {
        $stubfile = __DIR__ . "\\stubs\\EventHandler.php.stub";
        $newfile = base_path() . "\\" . Config::get('cqrs.namespaces.eventHandlers') . "\\" . $name . "Handler.php";
        if (!is_dir(dirname($newfile))) {
            mkdir(dirname($newfile), 0777, true);
        }

        $content = file_get_contents($stubfile);
        $content = $this->stubReplace("handlerNamespace", Config::get('cqrs.namespaces.eventHandlers'), $content);
        $content = $this->stubReplace("eventNamespace", Config::get('cqrs.namespaces.events'), $content);
        $content = $this->stubReplace("event", $name, $content);
        file_put_contents($newfile, $content);

        $this->comment($name . "Handler.php created");
    }

    private function stubReplace($key, $value, $content)
    {
        $content_chunks = explode("{{ " . $key . " }}", $content);
        return implode($value, $content_chunks);
    }
}
