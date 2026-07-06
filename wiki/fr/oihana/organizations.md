# `xyz\oihana\schema\organizations` — Entités commerciales

Le namespace `xyz\oihana\schema\organizations` modélise les **entités commerciales** : `Company`, la société de base porteuse des identifiants administratifs français et de l'ingestion adresse + contacts, et ses quatre déclinaisons — `Customer` (client), `Provider` (fournisseur), `Subsidiary` (filiale) et `Affiliate` (enseigne affiliée).

Toutes étendent la `Corporation` de Schema.org à travers `Company`, et exposent le contexte maison `@context = 'https://schema.oihana.xyz'` dans le JSON-LD : le type métier d'un document se lit dans son `additionalType` (`…/Customer`, `…/Provider`, …).

> 🇬🇧 This page is also available in [English](../../en/oihana/organizations.md).

---

## Quand l'utiliser

Choisissez ces classes pour représenter une organisation **dans sa relation commerciale** — pas seulement son existence juridique :

- un `Customer` pour un client avec ses conditions (statut de crédit, conditions de paiement, segmentation tarifaire, rattachements) ;
- un `Provider` pour un fournisseur avec sa logistique (transporteur, franco, minimum de commande) et ses informations d'achat ;
- une `Subsidiary` ou une `Affiliate` pour les structures du groupe.

La `Company` de base suffit lorsque l'organisation n'a pas encore de rôle commercial déterminé.

---

## Exemple express

```php
use xyz\oihana\schema\organizations\Customer;

$customer = new Customer
([
    'name'             => 'South Wood Company' ,
    'taxID'            => '84999999900012' ,        // SIRET
    'naics'            => '4673A' ,                 // code APE
    'creditStatus'     => 'open' ,
    'paymentTerms'     => '30D' ,
]);

// Les lignes plates d'un jeu de données hydratent les objets imbriqués (voir la page ingestion) :
$customer->addressLocality  = 'BORDEAUX' ;          // → $customer->address (PostalAddress)
$customer->defaultTelephone = '05 56 00 00 00' ;    // → $customer->contactPoint (ContactPoint[])
```

---

## La base : `Company`

`Company` étend la `Corporation` de Schema.org et concentre ce que toutes les déclinaisons partagent :

| Propriété | Type | Rôle |
|---|---|---|
| `taxID` (héritée) | `string` | Le SIRET français. |
| `naics` (héritée) | `string` | Le code APE. |
| `additionalProperty` | `PropertyValue[]` | Les propriétés additionnelles normalisées. |
| `category` / `industry` / `invoiceType` | référence de terme | Classifications (résolues vers un thésaurus par le consommateur). |
| `deliveryMethod` | référence de terme | Mode de livraison par défaut. |
| `freeShippingThreshold` | `float` | Le seuil de franco. |
| `status` | `int` | Statut applicatif. |
| `vat` | `TaxRate` ou référence | Le régime de TVA. |
| `website` | `WebSite` ou référence | Le site public. |

`Company` compose les traits d'ingestion `SetPostalAddressTrait` et `SetContactPointTrait` (voir [Ingestion](ingestion.md)) : les clés plates `addressLocality`, `defaultTelephone`, `mobile`, … deviennent des objets `PostalAddress` et `ContactPoint` typés au fil de l'hydratation.

---

## Les déclinaisons

| Classe | Étend | Ce qu'elle ajoute |
|---|---|---|
| `Customer` | `Company` | `assignedCompany` / `assignedPOS` / `assignedSeller` (les rattachements société, dépôt, vendeur), `creditStatus`, `paymentTerms`, `priceSegmentation` (référence vers `PriceSegmentation`), `unloadingMethod`, et sa propre normalisation de propriétés additionnelles (`CustomerAdditionalProperty::normalize()`). |
| `Provider` | `Company` | `carrier` (le transporteur), `amountCarriagePaid` (le franco), `minimumOrderValue`, `hasAcknowledgmentOfReceipt`, `providerType`, `shareCapital`, et `productInfo` (`ProductProviderInfo`) alimenté par `SetProductProviderInfoTrait`. |
| `Subsidiary` | `Company` | La filiale du groupe — le type suffit, les propriétés viennent de la base. |
| `Affiliate` | `Company` | L'enseigne affiliée — même logique. |

Les références (`assignedSeller`, `carrier`, `priceSegmentation`, …) sont **résolues** : chacune accepte un objet hydraté, une référence scalaire (clé), ou un `array` associatif brut — jamais une classe forcée.

---

## Voir aussi

- [Ingestion](ingestion.md) — les ponts `__set` qui hydratent les lignes plates.
- [Personnes Oihana](people.md) — les interlocuteurs de ces organisations.
- [Produits Oihana](products.md) — `PriceSegmentation`, `TaxRate` et la couche commerce.
- [Fonctions d'assistance](helpers.md) — `hydrateCustomer()` et ses sœurs.
