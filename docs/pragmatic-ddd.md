# Organizzare un'applicazione Laravel

## Obiettivo

L'obiettivo è usare DDD come guida organizzativa, senza trasformare Laravel in un'architettura artigianale e più complessa del necessario. Queste sono linee guida per l'applicazione: il package non richiede la creazione di cartelle o layer specifici.

Il principio è semplice:

- le `Areas` definiscono confini funzionali;
- il business logic resta in `Action` e `Command`;
- la persistenza resta naturale con `Eloquent`;
- le query di lettura sono dirette e native;
- mapper, repository e DTO ridondanti sono evitati.

## Regole del progetto

### 1) Mantieni le Aree, evita la burocrazia

Le `Areas` devono separare domini e responsabilità, ma non devono introdurre una catalogazione di classi fittizie o adattatori per ogni query.

Buona pratica:

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

### 2) Comandi e action per la scrittura

La scrittura del sistema può passare attraverso un comando o una action dedicata. La logica di business appartiene a quella area, non a un repository astratto o a un mapper che duplica il modello.

```php
final class UpdateProductCommand extends Command
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
    ) {}

    public function handle(): Product
    {
        $product = Product::query()->findOrFail($this->id);

        $product->update([
            'name' => $this->name,
        ]);

        return $product->fresh();
    }
}
```

### 3) Eloquent diretto per la lettura

Le query di lettura devono usare `Eloquent`, `Query Builder` e le relation native. Non si costruisce una "entità di dominio" per una pagina, un form o una tabella.

```php
$product = Product::query()
    ->with('category')
    ->withCount('variants')
    ->findOrFail($id);
```

Inertia e i payload frontend ricevono una struttura già pronta per essere mostrata.

### 4) DTO solo quando hanno senso

DTO e `Spatie Data` sono utili per contratti esterni, payload stabili, o trasformazioni che non sono semplicemente “read from Eloquent and render”.

Non sono necessari quando:

- stai solo leggendo un modello e restituendo dati al frontend;
- il dato non ha una trasformazione di dominio;
- la classe aggiunge complessità ma non semplifica il codice.

### 5) Regola pratica

Se la cosa è solo per mostrare dati a schermo, non inventare un layer di dominio solo per "essere più puliti".

Laravel è già un framework con convenzioni forti: usare la sua persistenza, le sue query e i suoi componenti è la scelta più pragmatica e sostenibile.

## Criterio di successo

L'architettura è corretta quando:

- mantiene le `Areas` come organizzazione logica;
- usa `Eloquent` nativamente in lettura;
- usa `Command` e `Action` in scrittura;
- evita repository e mapper superflui;
- mantiene Laravel coerente con il proprio ecosistema e con la velocità di sviluppo del team.
