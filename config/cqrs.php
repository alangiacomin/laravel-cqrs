<?php
return [

  /*
  |--------------------------------------------------------------------------
  | Namespace
  |--------------------------------------------------------------------------
  |
  | The namespaces used for commands and events handling.
  |
  */

  'namespaces' => [
    'commands' => 'App\Commands',
    'commandHandlers' => 'App\CommandHandlers',
    'events' => 'App\Events',
    'eventHandlers' => 'App\EventHandlers',
  ],

  /*
  |--------------------------------------------------------------------------
  | Event listener configuration
  |--------------------------------------------------------------------------
  |
  | Overrides for the default event listeners configuration
  |
  */

  'eventListener' => [
    'shouldDiscoverEvents' => true,
    'directories' => [
      'EventHandlers',
    ],
  ],

];
