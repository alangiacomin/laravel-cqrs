# Integrazione TypeScript

Il package integra e preconfigura [`spatie/laravel-typescript-transformer`](https://github.com/spatie/laravel-typescript-transformer) per generare definizioni TypeScript da classi PHP, DTO, Spatie Data ed enum. La generazione è eseguita dal comando Artisan fornito dalla dipendenza.

---

## Configurazione Predefinita

Il service provider aggiunge automaticamente una configurazione con queste impostazioni:

- **Directory scansionata**: Tutto il percorso `app/`.
- **Destinazione del file generato**: `resources/js/types/generated/index.ts`.
- **Formattazione**: Prettier out-of-the-box (`PrettierFormatter`).
- **Supporto Enums**: Conversione nativa degli enum PHP in tipi TypeScript (`transform_to_native_enums => true`).
- **Date e Carbon**: Mappatura automatica di `DateTime`, `Carbon`, `CarbonImmutable` verso `string`.
- **Supporto DTO e Spatie Data**: Integrazione diretta con `spatie/laravel-data`.

---

## Come Utilizzarlo

### 1. Aggiungere l'attributo `#[TypeScript]`

Aggiungi l'attributo `#[TypeScript]` (o l'annotazione PHPDoc `@typescript`) su qualsiasi classe PHP o Enum che desideri esporre al frontend:

```php
namespace App\Areas\Catalog\Application\Data;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ProductData extends Data
{
    public function __construct(
        public string $id,
        public string $name,
        public float $price,
        public bool $isActive,
    ) {}
}
```

Puoi applicarlo anche agli Enum nativi di PHP:

```php
namespace App\Enums;

use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
enum OrderStatus: string
{
    case Pending = 'pending';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
}
```

### 2. Generare i tipi TypeScript

Esegui il comando Artisan:

```bash
php artisan typescript:transform
```

Il file `resources/js/types/generated/index.ts` verrà generato o aggiornato. Importalo nei componenti Vue, React o Svelte del frontend come un normale modulo TypeScript.

## Personalizzare la configurazione

Puoi sovrascrivere la configurazione `typescript-transformer` nell'applicazione Laravel quando devi cambiare percorsi, file di output o formattazione, creando `config/typescript-transformer.php`:

```php
return [
    'output_file' => resource_path('js/types/generated/frontend.ts'),
];
```

La configurazione del package include `app_path()` come directory da scansionare e può essere estesa con le regole previste dalla documentazione di Spatie.
