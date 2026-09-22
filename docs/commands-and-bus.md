# Comandi, dispatch e code

Il package fornisce una classe base `Command` per racchiudere un'operazione applicativa. L'esecuzione usa il bus nativo di Laravel: non sono richiesti un bus aggiuntivo o binding speciali.

## Creare un comando

Estendi `AlanGiacomin\LaravelCqrs\App\Application\Commands\Command` e inserisci nel metodo `handle()` l'operazione che vuoi rendere riutilizzabile:

```php
namespace App\Areas\Catalog\Application\Commands;

use AlanGiacomin\LaravelCqrs\App\Application\Commands\Command;

final class CreateProductCommand extends Command
{
    public function __construct(
        public readonly string $name,
        public readonly float $price,
    ) {}

    public function handle(): string
    {
        // Validazione di dominio e persistenza del prodotto...
        return 'prod_123';
    }
}
```

Il valore restituito da `handle()` è il risultato dell'esecuzione sincrona. Il comando può usare normalmente Eloquent, servizi applicativi ed eventi Laravel.

## Eseguire un comando

Usa `dispatch_sync()` quando la richiesta ha bisogno del risultato:

```php
$productId = dispatch_sync(
    new CreateProductCommand('Scarpe', 49.99)
);
```

Usa `dispatch()` quando il comando può essere consegnato al bus di Laravel:

```php
dispatch(new CreateProductCommand('Scarpe', 49.99));
```

Per un comando non accodato, Laravel lo esegue normalmente nel processo corrente. Per un comando che implementa `ShouldQueue`, `dispatch()` lo invia alla coda configurata, mentre `dispatch_sync()` lo esegue subito anche se è queueable.

## Esecuzione in background

Implementa `Illuminate\Contracts\Queue\ShouldQueue` per eseguire il comando con un worker:

```php
namespace App\Areas\Reports\Application\Commands;

use AlanGiacomin\LaravelCqrs\App\Application\Commands\Command;
use Illuminate\Contracts\Queue\ShouldQueue;

final class GenerateMonthlyReportCommand extends Command implements ShouldQueue
{
    public function __construct(
        public readonly int $year,
        public readonly int $month,
    ) {}

    public function handle(): void
    {
        // Generazione del report.
    }
}
```

La configurazione della connessione, della coda e dei worker resta quella standard di Laravel:

```bash
php artisan queue:work
```

## Eventi solo per i comandi asincroni

`isRunningOnQueue()` restituisce `true` quando il comando è eseguito da un worker. `emitIfAsync()` invia un evento solo in quel caso:

```php
public function handle(): void
{
    // Un evento di dominio può essere sempre emesso.
    event(new ReportGenerated($this->year, $this->month));

    // Una notifica di completamento può essere limitata al lavoro in coda.
    $this->emitIfAsync(new ReportGenerationCompleted($this->year, $this->month));
}
```

Usa `event()` per i fatti di business che devono essere pubblicati in ogni modalità di esecuzione; usa `emitIfAsync()` solo quando l'evento ha senso per un'operazione in background.
