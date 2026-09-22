# Controller e Routing

Il pacchetto fornisce un Controller base ottimizzato per applicazioni CQRS/Inertia e un generatore dedicato alla gestione delle rotte localizzate.

---

## Controller Base

I tuoi controller possono estendere [`AlanGiacomin\LaravelCqrs\App\Presentation\Http\Controllers\Controller`](file:///home/alan/Git/laravel-cqrs/src/App/Presentation/Http/Controllers/Controller.php). Questo controller include il trait `AuthorizesRequests` e fornisce diversi metodi helper pratici per il flusso applicativo:

```php
namespace App\Areas\Catalog\Presentation\Http\Controllers;

use AlanGiacomin\LaravelCqrs\App\Presentation\Http\Controllers\Controller;
use App\Areas\Catalog\Application\Commands\CreateProductCommand;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

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

        return $this->flashSuccess("Prodotto {$productId} creato con successo!");
    }
}
```

### Metodi Helper Disponibili

- **`execute(Command $command): mixed`**  
  Esegue un comando in modo sincrono tramite il `MessageBus` e restituisce il valore ritornato da `handle()`.

- **`executeAsync(Command $command): mixed`**  
  Accoda un comando in background sulle code di Laravel tramite il `MessageBus` (ritorna `null`).

- **`flashSuccess(mixed $returnValue): RedirectResponse`**  
  Reindirizza alla pagina precedente (`back()`) salvando nella sessione flash il messaggio specificato sotto la chiave `'success'`.

- **`spaRedirect(string $route): RedirectResponse`**  
  Esegue un redirect standard verso l'URL previsto o verso la rotta indicata (`redirect()->intended($route)`), ideale per le transizioni SPA lato client con Inertia.

- **`hardRedirect(string $route): Response`**  
  Esegue un redirect "duro" a livello di pagina o verso un endpoint esterno via Inertia (`Inertia::location(...)`), forzando il browser a un caricamento completo.

- **`bus(): MessageBus`**  
  Restituisce l'istanza di `MessageBus` risolta dal container.

- **`routeGenerator(): LocalizedRouteGenerator`**  
  Restituisce il generatore di rotte localizzate.

---

## Generazione di Rotte Localizzate (`LocalizedRouteGenerator`)

Se la tua applicazione gestisce rotte multi-lingua con prefisso locale (ad esempio `localized.posts.show` corrispondente a `/{locale}/posts/{post}`), la classe `LocalizedRouteGenerator` automatizza la risoluzione del prefisso e il passaggio del parametro `locale`.

### Utilizzo

Puoi risolvere `LocalizedRouteGenerator` tramite dependency injection oppure tramite `$this->routeGenerator()` nei controller:

```php
use AlanGiacomin\LaravelCqrs\Infrastructure\Routing\LocalizedRouteGenerator;

class PostController extends Controller
{
    public function show(int $id)
    {
        // Se la lingua corrente è 'it', risolve automaticamente verso 'localized.posts.show'
        // passando ['locale' => 'it', 'id' => 10]
        $url = $this->routeGenerator()->route('posts.show', ['id' => $id]);

        return inertia('Posts/Show', ['postUrl' => $url]);
    }
}
```

### Rotte Firmate Temporanee (`signedRoute`)

Per generare URL firmati a scadenza con supporto alla localizzazione:

```php
use Illuminate\Support\Carbon;

$url = $this->routeGenerator()->signedRoute(
    'downloads.invoice',
    Carbon::now()->addMinutes(30),
    ['invoiceId' => 42]
);
```

### Come Funziona la Risoluzione del Locale

1. Il generatore controlla prima se esiste il parametro `{locale}` nella rotta HTTP corrente (`request()->route('locale')`).
2. Se assente, utilizza il locale predefinito dell'applicazione (`app()->getLocale()`).
3. Se è presente un locale valido, cerca la rotta con prefisso `localized.<nome>`. Se non è presente alcun locale, usa il nome standard `<nome>`.
4. Filtra automaticamente l'array dei parametri per mantenere solo i parametri effettivamente dichiarati nella rotta, iniettando il `locale` dove richiesto.
