# Configurazione e integrazione

Il service provider di `laravel-cqrs` rende disponibile la configurazione del trasformatore TypeScript e aggiunge la versione del package al comando `php artisan about`. Non esegue scansioni custom del progetto e non registra automaticamente repository, listener o binding applicativi.

## Eventi, listener e binding

La registrazione degli eventi e dei listener deve usare i meccanismi nativi di Laravel. In una nuova applicazione Laravel è possibile configurare le directory da scoprire nel bootstrap dell'applicazione:

```php
->withEvents(discover: [
    app_path('Listeners'),
])
```

In alternativa, gli eventi possono essere registrati esplicitamente nel service provider dell'applicazione:

```php
use Illuminate\Support\Facades\Event;

public function boot(): void
{
    Event::listen(
        ProductCreated::class,
        SendProductNotification::class,
    );
}
```

La discovery nativa è responsabilità dell'applicazione Laravel. Questo evita glob, conversioni manuali da path a namespace e reflection eseguite dal package.

## Configurazione TypeScript

Il package fonde la propria configurazione in `typescript-transformer`, includendo automaticamente `app_path()` tra i percorsi da scoprire. La configurazione dell'app può sovrascrivere i valori pubblicati secondo le regole di Laravel.

I binding applicativi, se necessari, devono essere registrati esplicitamente nel service provider dell'applicazione. La persistenza può usare direttamente Eloquent senza una configurazione repository del package.
