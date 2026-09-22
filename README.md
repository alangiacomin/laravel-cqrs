# Laravel CQRS

Pacchetto per strutturare applicazioni Laravel secondo il pattern **CQRS** (Command Query Responsibility Segregation), con supporto nativo per **Inertia.js** e generazione automatica di tipi **TypeScript**.

---

## Progetto Template Pronto all'Uso

Se cerchi un'applicazione completa, preconfigurata e immediatamente funzionante basata su questa architettura, puoi fare riferimento al template ufficiale:

👉 **[laravel-template](https://github.com/alangiacomin/laravel-template)**

Tutti i dettagli di avvio rapido e configurazione dello stack completo (backend e frontend) sono disponibili nella documentazione del template.

---

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

## Panoramica delle Funzionalità

`laravel-cqrs` mette a disposizione un set coordinato di strumenti per organizzare il codice applicativo:

### 1. Comandi e Message Bus
- **`Command`**: Classe base per tutti i comandi. Il valore di ritorno è direttamente ciò che restituisce `handle(): mixed`.
- **Rilevamento Asincrono**: Metodi `$this->isRunningOnQueue()` ed `$this->emitIfAsync($event)` per evitare l'invio di eventi o notifiche broadcast quando il comando viene eseguito in sincrono.
- **`ShouldQueue` / `AsyncCommand`**: Comandi pronti per le code di Laravel (`ShouldQueue`), inviati in background o eseguibili in sincrono all'occorrenza.
- **`MessageBus`**: Bus unificato che instrada automaticamente comandi sincroni e asincroni verso il bus di Laravel (`dispatch`, `dispatchSync`, `dispatchAsync`).

### 2. Controller Base
- **`Controller`**: Classe base per i tuoi controller HTTP, dotata di metodi helper pensati per flussi CQRS e Inertia:
  - `execute($command)`: esegue un comando sincrono tramite il bus e ne ritorna il risultato.
  - `executeAsync($command)`: accoda un comando asincrono in background.
  - `flashSuccess($message)`: redirect `back()` con messaggio di successo in sessione flash.
  - `spaRedirect($route)`: redirect SPA compatibile con Inertia (`redirect()->intended(...)`).
  - `hardRedirect($route)`: redirect a pagina intera o verso URL esterni via `Inertia::location(...)`.

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

### 5. Eventi e architetture modulari
- La discovery degli eventi è delegata ai meccanismi nativi di Laravel.
- Le Aree sono una convenzione organizzativa dell'applicazione, senza repository o binding automatici del package.

### 6. Rotte Localizzate
- **`LocalizedRouteGenerator`**: Generatore di rotte che gestisce automaticamente prefissi di lingua (es. `localized.posts.show`), iniettando il parametro `locale` corrente e pulendo i parametri non dichiarati nella rotta. Supporta anche URL firmati temporanei (`signedRoute`).

### 7. Integrazione TypeScript
- Preconfigurazione automatica per `spatie/laravel-typescript-transformer`.
- Trasforma DTO, Spatie Data e classi Enum PHP direttamente in tipi TypeScript in `resources/js/types/generated/index.ts`..

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
        $productId = $this->execute(
            new CreateProductCommand(
                name: $request->string('name'),
                price: $request->float('price'),
            )
        );

        return $this->flashSuccess("Prodotto {$productId} creato!");
    }
}
```

---

## Documentazione Dettagliata

Per approfondire ciascuna funzionalità e consultare esempi avanzati di utilizzo, consulta le guide dedicate nella cartella `docs/`:

- [Comandi e Message Bus](docs/commands-and-bus.md)
- [Controller e Routing Localizzato](docs/controllers-and-routing.md)
- [Dominio e persistenza](docs/domain-and-persistence.md)
- [Pragmatic DDD](docs/pragmatic-ddd.md)
- [Autorizzazione ed Eccezioni](docs/authorization-and-exceptions.md)
- [Configurazione e discovery](docs/configuration-and-discovery.md)
- [Integrazione TypeScript](docs/typescript.md)

---

## Licenza

Questo pacchetto è distribuito con licenza [MIT](LICENSE).
