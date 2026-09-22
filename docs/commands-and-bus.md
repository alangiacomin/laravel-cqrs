# Comandi e Message Bus

Il pacchetto fornisce un'astrazione pulita e allineata a Laravel per eseguire le operazioni di business tramite comandi, orchestrati da un unico `MessageBus`.

---

## Definizione di un Comando

Tutti i comandi estendono la classe astratta [`AlanGiacomin\LaravelCqrs\App\Application\Commands\Command`](file:///home/alan/Git/laravel-cqrs/src/App/Application/Commands/Command.php).

A differenza dei pattern rigidi a due fasi, **il risultato del comando è semplicemente il valore restituito dal metodo `handle()`**:

```php
namespace App\Areas\Catalog\Application\Commands;

use AlanGiacomin\LaravelCqrs\App\Application\Commands\Command;

class CreateProductCommand extends Command
{
    public function __construct(
        public readonly string $name,
        public readonly float $price,
    ) {}

    public function handle(): string
    {
        // Logica di creazione del prodotto...
        return 'prod_123';
    }
}
```

---

## Comandi Asincroni e Code (`ShouldQueue`)

Se un comando deve essere elaborato in background sulle code di Laravel, basta implementare l'interfaccia standard `ShouldQueue` (oppure estendere [`AsyncCommand`](file:///home/alan/Git/laravel-cqrs/src/App/Application/Commands/AsyncCommand.php)):

```php
namespace App\Areas\Reports\Application\Commands;

use AlanGiacomin\LaravelCqrs\App\Application\Commands\Command;
use Illuminate\Contracts\Queue\ShouldQueue;

class GenerateMonthlyReportCommand extends Command implements ShouldQueue
{
    public function __construct(
        public readonly int $year,
        public readonly int $month,
    ) {}

    public function handle(): void
    {
        // Generazione del report in background
    }
}
```

---

## Gestione Eventi e Rilevamento Asincrono

Un'esigenza comune è distinguere tra:
1. **Domain Events (Fatti di business):** devono essere sempre emessi (es. `OrderCreated`), sia in esecuzione sincrona sia asincrona.
2. **Notification / Completion Events:** notifiche (es. via WebSocket o broadcast per Inertia/SPA) che devono essere emesse **solo se il comando viene eseguito in background**, evitando eventi superflui o doppi quando il comando è sincrono e l'utente riceve già il valore di ritorno nella risposta HTTP.

La classe base `Command` mette a disposizione helper dedicati:

- **`$this->isRunningOnQueue(): bool`**: ritorna `true` se il comando sta girando in un worker asincrono, `false` se è eseguito in sincrono nella richiesta HTTP. Utile per qualsiasi blocco condizionale `if ($this->isRunningOnQueue()) { ... }`.
- **`$this->emitIfAsync(object|string $event, ...$payload): void`**: scorciatoia espressiva che lancia l'evento **solo** se il comando è in esecuzione su coda in background.

```php
namespace App\Areas\Orders\Application\Commands;

use AlanGiacomin\LaravelCqrs\App\Application\Commands\Command;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProcessOrderCommand extends Command implements ShouldQueue
{
    public function __construct(public readonly string $orderId) {}

    public function handle(): string
    {
        // 1. Business logic
        $status = 'processed';

        // 2. Domain event: lanciato SEMPRE
        event(new OrderStatusChanged($this->orderId, $status));

        // 3. Completion event: lanciato SOLO se siamo asincroni in coda
        $this->emitIfAsync(new OrderProcessingCompletedNotification($this->orderId));

        return $status;
    }
}
```

---

## Utilizzo del `MessageBus`

La classe [`MessageBus`](file:///home/alan/Git/laravel-cqrs/src/Infrastructure/Bus/MessageBus.php) unifica l'invio dei comandi:

### 1. `dispatch(Command $command): mixed`
- Se il comando implementa `ShouldQueue`, viene accodato in background (`Bus::dispatch`) e restituisce `null`.
- Altrimenti, viene eseguito immediatamente in sincrono e restituisce il valore di `handle()`.

### 2. `dispatchSync(Command $command): mixed`
- Esegue il comando **immediatamente nel processo corrente**, anche se implementa `ShouldQueue`, e ne restituisce il valore di ritorno.

### 3. `dispatchAsync(Command $command): mixed`
- Accoda esplicitamente il comando sulla coda di Laravel e restituisce `null`.

```php
use AlanGiacomin\LaravelCqrs\Infrastructure\Bus\MessageBus;

// Esecuzione manuale:
$result = app(MessageBus::class)->dispatch(new CreateProductCommand('Scarpe', 49.99));

// Forzare esecuzione sincrona e ottenere il valore:
$result = app(MessageBus::class)->dispatchSync(new ProcessOrderCommand('ord_123'));
```

---

## Esecuzione da Controller

Se il tuo controller estende [`AlanGiacomin\LaravelCqrs\App\Presentation\Http\Controllers\Controller`](file:///home/alan/Git/laravel-cqrs/src/App/Presentation/Http/Controllers/Controller.php):

```php
// Esecuzione sincrona con valore di ritorno:
$result = $this->execute(new CreateProductCommand($request->name, $request->price));

// Esecuzione asincrona in background:
$this->executeAsync(new GenerateMonthlyReportCommand(2026, 9));
```

---

## Retrocompatibilità (`SyncCommand`)

La classe astratta `SyncCommand` e il metodo `getResponse()` sono mantenuti per piena retrocompatibilità con il codice legacy. Se `handle()` ritorna `null`, il `MessageBus` interroga automaticamente `getResponse()`. Nei nuovi comandi è sufficiente estendere `Command` e restituire il valore direttamente da `handle()`.
