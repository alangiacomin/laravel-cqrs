# Dominio, letture e persistenza

Il package segue un approccio pragmatico a CQRS e Laravel: Eloquent resta il modello di persistenza, mentre command/action e query aiutano a organizzare il comportamento applicativo.

## Organizzare il codice senza burocrazia

Le `Areas` sono un modo per organizzare funzionalità e responsabilità, non una giustificazione per introdurre layer duplicati o controller di persistenza fittizi.

Regole applicative:

- le `Areas` definiscono confini funzionali e responsabilità;
- la scrittura passa per `Command` / `Action` e lascia che Eloquent faccia il lavoro di persistenza;
- la lettura usa direttamente `Eloquent` o `Query Builder` per pagine, liste, dettaglio e payload Inertia;
- non si usano repository o mapper per letture semplici o per "presentazione" del dato;
- DTO e [Spatie Laravel Data](https://spatie.be/docs/laravel-data) restano utili per contratti esterni/stabili, ma non sostituiscono la persistenza nativa di Laravel.

Questo principio è esplicito: se un dato viene solo mostrato a schermo, non deve essere trasformato in un'entità di dominio o in un DTO ad hoc per far "conformare" il codice a una teoria architetturale troppo rigida.

## Scrittura: command e action

La logica di modifica dello stato può vivere in un command, eventualmente supportato da una action dedicata. Il modello Eloquent gestisce persistenza, relazioni, cast e scope:

```php
namespace App\Areas\Catalog\Application\Commands;

use AlanGiacomin\LaravelCqrs\App\Application\Commands\Command;
use App\Areas\Catalog\Domain\Models\Product;

final class CreateProductCommand extends Command
{
    public function __construct(
        public readonly string $name,
        public readonly float $price,
    ) {}

    public function handle(): Product
    {
        return Product::query()->create([
            'name' => $this->name,
            'price' => $this->price,
        ]);
    }
}
```

Non è necessario introdurre un repository o un mapper tra il command e il modello. Se una regola appartiene al dominio, può essere espressa con un metodo del modello, una `Action` dedicata o un servizio applicativo semplice. Questa struttura è una scelta dell'applicazione, non un requisito del package.

## Lettura: Eloquent e Query Builder

Le query possono usare direttamente Eloquent e Query Builder, così restano disponibili selezione delle colonne, eager loading, scope e paginazione:

```php
$products = Product::query()
    ->active()
    ->with('category')
    ->select(['id', 'name', 'price'])
    ->paginate();
```

Per una vista Inertia, un payload `props`, o un payload per TypeScript, si preferisce costruire direttamente il risultato richiesto. Se serve una struttura formale esclusivamente a livello di contratto, il DTO ha senso; se il dato è semplicemente una query viva di Laravel, non va trasformato in un layer artificiale.

## Quando usare DTO / Data Object

Usare DTO o `Spatie Data` quando:

- si vuole esporre un contratto stabile a frontend o altre applicazioni;
- si devono normalizzare dati derivati da più relazioni o modelli;
- si vuole un payload esplicito e documentato per un punto di integrazione.

Non usarli quando:

- il dato viene letto direttamente da Eloquent e mostrato in UI senza trasformazioni di business;
- la struttura è già naturale e coerente con il modello di Laravel;
- l'unico scopo è rispettare una teoria architetturale che produce layer ridondanti.

## Organizzazione nelle Aree

Una struttura modulare possibile è:

```text
app/Areas/Catalog/
├── Application/
│   ├── Actions/
│   └── Commands/
├── Domain/
│   └── Models/
├── Infrastructure/
│   └── Queries/
└── Presentation/
    ├── Http/Controllers/
    └── Resources/
```

Questa struttura è una convenzione dell'applicazione, non una gerarchia richiesta dal pacchetto. Laravel non richiede repository, entità parallele o mapper per usare CQRS. Il package supporta un modello di CQRS pragmatico: business logic nelle command/action, dati in Eloquent, presentazione diretta e DTO solo quando hanno senso.
