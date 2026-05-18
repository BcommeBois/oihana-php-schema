# `org\schema` — Vocabulaire Schema.org

Le namespace `org\schema` est le cœur de la bibliothèque. Il fournit une implémentation PHP fortement typée du vocabulaire [Schema.org](https://schema.org) — environ **400 objets-valeur** organisés par thème.

> 🇬🇧 This page is also available in [English](../../en/schema-org/README.md).

---

## Quand l'utiliser

Utilisez `org\schema` chaque fois que vous voulez décrire une *chose* avec une sémantique web standard, typiquement pour :

- produire des charges utiles JSON-LD consommables par les moteurs de recherche ou les outils de données liées,
- stocker des documents structurés en base (les métadonnées ArangoDB sont intégrées),
- exposer des ressources REST/GraphQL avec des noms de propriétés cohérents,
- migrer depuis des DTO ad-hoc vers un vocabulaire partagé.

---

## Exemple express

```php
use org\schema\Person;
use org\schema\PostalAddress;
use org\schema\constants\Schema;

$alice = new Person
([
    Schema::ID      => '2555' ,
    Schema::NAME    => 'Alice' ,
    Schema::EMAIL   => 'alice@example.com' ,
    Schema::ADDRESS => new PostalAddress
    ([
        Schema::STREET_ADDRESS => '2 chemin des Vergers' ,
        Schema::POSTAL_CODE    => '49170' ,
    ]),
]);

echo json_encode( $alice , JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
```

Chaque entité expose dans le JSON-LD son propre `@type` et le contexte `@context = 'https://schema.org'`, et les propriétés à `null` sont retirées automatiquement.

---

## Parcourir par thème

Le vocabulaire est éclaté en sous-namespaces logiques. Chaque lien ci-dessous mène vers un guide dédié avec catalogue de classes, exemple de code et pointeurs vers le code source.

| Guide                                  | Sous-namespace               | Classes | Points clés                                                |
|----------------------------------------|------------------------------|---------|------------------------------------------------------------|
| [Types de base](core.md)               | `org\schema`                 | ~100    | `Thing`, `Person`, `Organization`, `Place`, `Event`, `Product`, `Offer`, `Order`, `Service`, `Brand`. |
| [Actions](actions.md)                  | `org\schema\actions`         | ~115    | Hiérarchie `Action` complète de Schema.org (BuyAction, LikeAction, RegisterAction, ShareAction, …). |
| [Œuvres créatives](creative-work.md)   | `org\schema\creativeWork`    | ~60     | `Article`, `Book`, `ImageObject`, `VideoObject`, `Certification`, `Comment`. |
| [Événements](events.md)                | `org\schema\events`          | ~2      | Types d'événements spécialisés au-delà de l'`Event` de premier niveau. |
| [Lieux](places.md)                     | `org\schema\places`          | ~26     | `Country`, `City`, `Restaurant`, `Accommodation`, monuments. |
| [Organisations](organizations.md)      | `org\schema\organizations`   | ~18     | `EducationalOrganization`, `LocalBusiness`, `MedicalOrganization`, ONG. |
| [Services](services.md)                | `org\schema\services`        | ~7      | Produits financiers, services de paiement, conversion de devises. |
| [Items / listes](items.md)             | `org\schema\items`           | ~5      | `HowToStep`, `HowToItem`, `HowToTool`, `HowToSupply`.     |
| [Énumérations](enumerations.md)        | `org\schema\enumerations`    | ~86     | `DayOfWeek`, `EventStatusType`, `ItemAvailability`, `DeliveryMethod`. |

Pour la liste exhaustive des classes, parcourez la [référence d'API](../../../docs) générée ou le code source sous [`src/org/schema/`](../../../src/org/schema).

---

## Constantes de propriétés

Coder en dur les noms de clés en chaîne est risqué. Utilisez plutôt la classe agrégatrice [`org\schema\constants\Schema`](../../../src/org/schema/constants/Schema.php) — elle expose chaque nom de propriété comme une `public const string` typée, regroupée par thème via les traits sous [`org\schema\constants\traits/`](../../../src/org/schema/constants/traits).

```php
use org\schema\constants\Schema;

// Clés de propriétés sûres pour l'IDE et le refactoring :
Schema::NAME            // 'name'
Schema::AT_TYPE         // '@type'
Schema::AT_CONTEXT      // '@context'
Schema::ADDRESS         // 'address'
Schema::STREET_ADDRESS  // 'streetAddress'
```

L'alias `Prop` est également disponible pour les extraits plus courts :

```php
use org\schema\constants\Prop;

Prop::NAME; // 'name'
```

---

## Pour aller plus loin

- [Démarrage rapide](../demarrage.md) — installation, hydratation et bases du JSON-LD.
- [`xyz\oihana\schema`](../oihana-core.md) — utilitaires transverses Oihana (Pagination, Log, AuditAction).
- [Site officiel Schema.org](https://schema.org) — spécification d'origine.
- [Référence d'API](../../../docs) — toutes les classes, propriétés et méthodes.
