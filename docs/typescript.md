# Integrazione TypeScript

Il pacchetto integra e preconfigura il pacchetto [`spatie/laravel-typescript-transformer`](https://github.com/spatie/laravel-typescript-transformer) per generare automaticamente definizioni di tipi TypeScript a partire da classi PHP, DTO e definizioni Enum.

---

## Configurazione Predefinita

Il Service Provider fonde automaticamente una configurazione con le impostazioni standard raccomandate:

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

Il file `resources/js/types/generated/index.ts` verrà generato o aggiornato automaticamente, rendendo disponibili tutti i tipi TypeScript per i tuoi componenti Vue, React o Svelte lato frontend.
