# Laravel CQRS

Un piccolo set di convenzioni e componenti per organizzare applicazioni Laravel secondo un approccio CQRS pragmatico, mantenendo il codice vicino alle convenzioni del framework. Il pacchetto è pensato per applicazioni che usano Laravel, Inertia.js e tipi TypeScript condivisi tra backend e frontend.

---

## Progetto Template Pronto all'Uso

Se cerchi un'applicazione completa, preconfigurata e immediatamente funzionante basata su questa architettura, puoi fare riferimento al template ufficiale:

👉 **[laravel-template](https://github.com/alangiacomin/laravel-template)**

Tutti i dettagli di avvio rapido e configurazione dello stack completo (backend e frontend) sono disponibili nella documentazione del template.

---

## Quando usarlo

Usa questo pacchetto quando vuoi:

- organizzare le modifiche ai dati in classi `Command`;
- distinguere le operazioni eseguite in una richiesta da quelle messe in coda;
- dichiarare i permessi direttamente su controller e azioni;
- standardizzare redirect Inertia e messaggi flash;
- generare tipi TypeScript da DTO, Spatie Data ed enum PHP.

Il pacchetto non impone repository, entità parallele o una struttura di directory obbligatoria: le query di lettura possono usare direttamente Eloquent e Query Builder.

## Requisiti

- **PHP**: `^8.4`
- **Laravel Framework**: `^13.24`

---

## Installazione

Installa il pacchetto tramite Composer:

```bash
composer require alangiacomin/laravel-cqrs
```

Il Service Provider viene registrato automaticamente tramite il package auto-discovery di Laravel.

---

## Funzionalità

`laravel-cqrs` mette a disposizione un set coordinato di strumenti per organizzare il codice applicativo:

### 1. Comandi e code
- **`Command`**: Classe base queueable per le operazioni di scrittura. Il metodo `handle()` contiene l'operazione e può restituire il risultato utile all'applicazione.
- **`ShouldQueue`**: Interfaccia standard di Laravel per eseguire un comando tramite un worker in background.
- **Rilevamento asincrono**: `isRunningOnQueue()` ed `emitIfAsync()` aiutano a distinguere l'esecuzione in coda da quella sincrona.
- **Bus di Laravel**: il package non sostituisce il bus del framework; usa `dispatch()`, `dispatchSync()` e la configurazione delle code di Laravel.

### 2. Controller base
- **`Controller`**: classe base per controller HTTP con gli helper `flashSuccess()`, `spaRedirect()` e `hardRedirect()`.
- L'autorizzazione standard di Laravel è disponibile tramite il trait `AuthorizesRequests`.

### 3. Autorizzazione Dichiarativa
- **`#[GateAuthorize]`**: Attributo PHP applicabile a classi controller o a singoli metodi per definire permessi e abilità Gate in modo pulito e dichiarativo.
- **`ApplyGateAttributes`**: Middleware pronto all'uso per verificare automaticamente i permessi prima dell'esecuzione dell'azione.

### 4. Dominio e persistenza
- CQRS pragmatico basato su command/action e modelli Eloquent.
- Query dirette con Eloquent e Query Builder, inclusi scope, eager loading e paginazione.
- DTO e Spatie Data per le forme dati esposte a Inertia e TypeScript, ma non come layer di persistenza obbligatorio.
- Regola del progetto: le aree organizzano il codice, ma non giustificano repository, mapper o entità parallele per la lettura di listing e dettaglio.

### 5. Pragmatic DDD per Laravel
- Le `Areas` sono confini funzionali, non una promessa di architettura layerizzata a tutti i costi.
- La scrittura del sistema passa per `Command` / `Action`: la logica di business si concentra lì, mentre Eloquent resta il punto di persistenza naturale.
- La lettura usa `Eloquent` o `Query Builder` direttamente per popolare viste, Inertia e payload frontend.
- Se un dato è usato solo per essere mostrato, non deve essere trasformato in una "domain entity" o in un mapper artificiale.
- DTO/Value Object vanno usati solo dove hanno valore reale: contratti esterni, payload stabili, concetti di dominio non banali.
- L'idea è mantenere DDD come guida di organizzazione, non come burocrazia applicativa che soffoca Laravel.

### 6. Eventi e organizzazione del codice
- Gli eventi e i listener seguono i meccanismi nativi di Laravel.
- Le `Areas` sono una convenzione organizzativa dell'applicazione, senza discovery, repository o binding automatici del package.

### 7. Integrazione TypeScript
- Preconfigurazione automatica per `spatie/laravel-typescript-transformer`.
- Trasforma DTO, Spatie Data e classi Enum PHP direttamente in tipi TypeScript in `resources/js/types/generated/index.ts`.

### 8. Eccezioni Applicative Standard
- Eccezioni semantiche con status code HTTP associato (`BadRequestException`, `UnauthorizedException`, `ForbiddenException`, `NotFoundException`, `ValidationException`, `CommandException`).

---

## Esempio Rapido

### Definizione di un Comando

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
        // Esecuzione della logica di creazione...
        return 'prod_abc123';
    }
}
```

### Utilizzo nel Controller

```php
namespace App\Areas\Catalog\Presentation\Http\Controllers;

use AlanGiacomin\LaravelCqrs\App\Infrastructure\Attributes\GateAuthorize;
use AlanGiacomin\LaravelCqrs\App\Presentation\Http\Controllers\Controller;
use App\Areas\Catalog\Application\Commands\CreateProductCommand;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

#[GateAuthorize('manage-catalog')]
class ProductController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $productId = dispatch_sync(
            new CreateProductCommand(
                name: (string) $request->string('name'),
                price: (float) $request->float('price'),
            )
        );

        return $this->flashSuccess("Prodotto {$productId} creato!");
    }
}
```

---

## Documentazione Dettagliata

Per approfondire ciascuna funzionalità e consultare esempi avanzati di utilizzo, consulta le guide dedicate nella cartella `docs/`:

- [Comandi, dispatch e code](docs/commands-and-bus.md)
- [Controller e redirect](docs/controllers-and-routing.md)
- [Dominio e persistenza](docs/domain-and-persistence.md)
- [Pragmatic DDD](docs/pragmatic-ddd.md)
- [Autorizzazione ed Eccezioni](docs/authorization-and-exceptions.md)
- [Configurazione e integrazione](docs/configuration-and-discovery.md)
- [Integrazione TypeScript](docs/typescript.md)

---

## Licenza

Questo pacchetto è distribuito con licenza [MIT](LICENSE).
