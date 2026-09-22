# Configurazione e discovery

Il service provider di `laravel-cqrs` registra la configurazione del trasformatore TypeScript e il comando `about` del pacchetto. Non esegue scansioni custom del progetto, non registra repository e non associa automaticamente listener tramite reflection.

## Eventi e listener

La registrazione degli eventi deve usare i meccanismi nativi di Laravel. In una nuova applicazione Laravel è possibile configurare le directory da scoprire nel bootstrap dell'applicazione:

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

## Nessuna configurazione Repository

Non esistono più le chiavi `architecture.repositories`, `CQRS_DISCOVER_REPOSITORIES` o `CQRS_CACHE_REPOSITORY_BINDINGS`. La persistenza usa direttamente Eloquent e gli eventuali binding applicativi devono essere registrati esplicitamente nel service provider dell'applicazione.
