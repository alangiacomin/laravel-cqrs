# Controller e redirect

Il controller base del package raccoglie gli helper più comuni per applicazioni Laravel con Inertia. Il routing, inclusa l'eventuale localizzazione, resta quello standard di Laravel.

## Estendere il controller

Estendi `AlanGiacomin\LaravelCqrs\App\Presentation\Http\Controllers\Controller` quando vuoi usare gli helper per messaggi flash e redirect:

```php
namespace App\Areas\Catalog\Presentation\Http\Controllers;

use AlanGiacomin\LaravelCqrs\App\Presentation\Http\Controllers\Controller;
use App\Areas\Catalog\Application\Commands\CreateProductCommand;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class ProductController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $productId = dispatch_sync(
            new CreateProductCommand(
                name: (string) $request->string('name'),
                price: (float) $request->float('price'),
            )
        );

        return $this->flashSuccess("Prodotto {$productId} creato con successo!");
    }
}
```

Il controller include anche `AuthorizesRequests`, quindi puoi usare i metodi di autorizzazione Laravel come `$this->authorize()`.

## Helper disponibili

- **`flashSuccess(mixed $returnValue): RedirectResponse`**: torna alla pagina precedente e salva il valore nella sessione flash con chiave `success`.
- **`spaRedirect(string $route): RedirectResponse`**: esegue `redirect()->intended($route)`, adatto ai redirect gestiti da Inertia.
- **`hardRedirect(string $route): Response`**: usa `Inertia::location(...)` per chiedere al browser un caricamento completo, utile anche per URL esterni.

Gli helper non eseguono automaticamente i comandi: per questo usa `dispatch()` o `dispatch_sync()` di Laravel.

## Routing e localizzazione

Per generare URL e redirect usa gli strumenti Laravel:

```php
return to_route('products.show', ['product' => $product]);

$url = route('products.show', ['product' => $product]);
```

Se l'applicazione usa più lingue, configura prefissi, middleware e nomi delle rotte nell'applicazione. Il package non aggiunge un generatore di rotte localizzate né modifica la risoluzione di `route()`.
