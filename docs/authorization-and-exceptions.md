# Autorizzazione ed Eccezioni

Il pacchetto offre strumenti per la sicurezza dichiarativa tramite attributi PHP e un set di eccezioni applicative standard con status code HTTP associati.

---

## Autorizzazione Dichiarativa con `#[GateAuthorize]`

Invece di chiamare manualmente `$this->authorize()` all'interno delle azioni dei controller o definire policy prolisse nei file di rotte, puoi dichiarare le autorizzazioni direttamente tramite l'attributo `#[GateAuthorize]`.

L'attributo può essere applicato sia alla classe del Controller che ai singoli metodi.

```php
namespace App\Areas\Catalog\Presentation\Http\Controllers;

use AlanGiacomin\LaravelCqrs\App\Infrastructure\Attributes\GateAuthorize;
use AlanGiacomin\LaravelCqrs\App\Presentation\Http\Controllers\Controller;
use App\Enums\Permissions;
use Illuminate\Http\Request;

// Applicato all'intero controller
#[GateAuthorize(Permissions::MANAGE_CATALOG)]
class ProductController extends Controller
{
    // Ereditata l'autorizzazione di classe

    // Applicato al singolo metodo con argomenti opzionali
    #[GateAuthorize('update-product', ['product'])]
    public function update(Request $request, string $product)
    {
        // ...
    }
}
```

### Abilitazione del Middleware `ApplyGateAttributes`

Per attivare l'elaborazione degli attributi `#[GateAuthorize]`, registra il middleware `AlanGiacomin\LaravelCqrs\App\Infrastructure\Middleware\ApplyGateAttributes` nella pipeline della tua applicazione (ad esempio nel file `bootstrap/app.php` di Laravel 11+ o in `app/Http/Kernel.php`):

```php
// bootstrap/app.php (Laravel 11+)
use AlanGiacomin\LaravelCqrs\App\Infrastructure\Middleware\ApplyGateAttributes;

return Application::configure(basePath: dirname(__DIR__))
    ->withMiddleware(function ($middleware) {
        $middleware->web(append: [
            ApplyGateAttributes::class,
        ]);
    })
    // ...
```

Quando una richiesta raggiunge il controller, il middleware controlla automaticamente la presenza dell'attributo sulla classe e sul metodo invocato, eseguendo `Gate::authorize(...)`.

---

## Eccezioni Applicative Standard

Il namespace `AlanGiacomin\LaravelCqrs\Infrastructure\Exceptions` mette a disposizione eccezioni semantiche pronte all'uso. Ognuna espone il metodo `getStatusCode()` per una mappatura immediata verso lo standard HTTP:

| Eccezione | Status Code | Descrizione |
|-----------|-------------|-------------|
| `BadRequestException` | `400` | Richiesta non valida o parametri malformati. |
| `UnauthorizedException` | `401` | Utente non autenticato. |
| `ForbiddenException` | `403` | Utente autenticato ma non autorizzato all'azione. |
| `NotFoundException` | `404` | Risorsa richiesta non trovata. |
| `ValidationException` | `422` | Errori di validazione sui dati inviati. |
| `CommandException` | `500` | Errore generico durante l'elaborazione del comando. |

### Esempio con `ValidationException`

La `ValidationException` permette di incapsulare l'elenco dettagliato degli errori:

```php
use AlanGiacomin\LaravelCqrs\Infrastructure\Exceptions\ValidationException;

throw new ValidationException('I dati forniti non sono validi.', [
    'email' => ['Indirizzo email già presente nei nostri sistemi.'],
    'price' => ['Il prezzo deve essere maggiore di zero.'],
]);
```

È possibile accedere all'array degli errori tramite la proprietà pubblica `$e->errors`:

```php
try {
    // elaborazione comando...
} catch (ValidationException $e) {
    return response()->json([
        'message' => $e->getMessage(),
        'errors' => $e->errors,
    ], $e->getStatusCode());
}
```
