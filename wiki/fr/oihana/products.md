# `xyz\oihana\schema\products` — La couche commerce

Le namespace `xyz\oihana\schema\products` porte la **couche commerce** de la bibliothèque : `Product`, le produit enrichi de ses métadonnées de vente (unité de vente, quantités éligibles, catégories tarifaires, TVA, stock), et sa constellation de classes satellites — niveaux de stock, spécifications de prix, conditions et moyens de paiement, informations fournisseur et dépôt.

Le cœur du modèle est **l'arbre des quantités éligibles** : la chaîne unité → colis → palette qui dit comment un produit se conditionne, et dont découlent toutes les conversions d'unité de vente.

> 🇬🇧 This page is also available in [English](../../en/oihana/products.md).

---

## Quand l'utiliser

Choisissez `Product` dès qu'un article porte une dimension commerciale : une unité de vente, un conditionnement, un prix de référence, un niveau de stock. La classe étend `SomeProducts` de Schema.org — un document produit reste du JSON-LD standard, enrichi du contexte maison.

---

## L'arbre des quantités éligibles

Un produit se vend à l'unité, au colis ou à la palette (`unitOfSale`, valeurs de l'énumération `UnitOfSaleType`). L'arbre `eligibleQuantity` décrit la chaîne complète : chaque niveau est une `QuantitativeValue` (quantité, code d'unité UN/CEFACT, libellé) dont le `valueReference` pointe le niveau supérieur.

L'arbre se construit **tout seul à l'hydratation** : les clés plates d'un jeu de données (`eligibleUnitQuantityCode`, `eligiblePackageQuantityCode`, `eligiblePackageQuantityValue`, …) passent par le `__set` magique de la classe et assemblent la chaîne.

```php
use xyz\oihana\schema\products\Product;
use xyz\oihana\schema\enumerations\UnitOfSaleType;

$product = new Product() ;

$product->eligibleUnitQuantityCode     = 'C62' ;  // l'unité
$product->eligiblePackageQuantityCode  = 'PA'  ;  // le colis
$product->eligiblePackageQuantityValue = 12    ;  // 12 unités par colis

$product->unitOfSale = UnitOfSaleType::PACKAGE ;

$product->getUnitOfSaleConversionFactor() ;       // 12.0
```

### Les conversions

| Méthode | Rend | Rôle |
|---|---|---|
| `getUnitOfSaleConversionFactor()` | `float` | Le facteur multiplicatif entre l'unité de base et l'unité de vente (1.0 pour l'unité). |
| `getInventoryLevelInUnitOfSale( $level )` | `float` ou `null` | Le stock converti dans l'unité de vente. |
| `findEligibleQuantityByType( $type )` | `QuantitativeValue` ou `null` | Le niveau de l'arbre correspondant à un `UnitOfSaleType`. |

### Le point d'extension : `resolveUnitCode()`

Les codes d'unité arrivent parfois dans une **nomenclature propriétaire** (celle d'un progiciel de gestion). Le hook `protected resolveUnitCode( mixed $value ) : ?string` rend la valeur inchangée par défaut ; une sous-classe le surcharge pour traduire sa nomenclature vers UN/CEFACT **avant** la construction de l'arbre :

```php
class MyProduct extends Product
{
    protected function resolveUnitCode( mixed $value ) :?string
    {
        return is_scalar( $value ) ? MyUnitTable::toUNCEFACT( (string) $value ) : null ;
    }
}
```

---

## Les autres propriétés du produit

| Propriété | Type | Rôle |
|---|---|---|
| `unitOfSale` | `UnitOfSaleType` | Unité, colis ou palette. |
| `inStock` / `inventoryLevel` | `bool` / `StockLevel` | La gestion et le niveau de stock. |
| `priceCategory` / `webCategory` / `productType` | références de termes | Les classifications tarifaire, de navigation et fonctionnelle. |
| `vat` | `TaxRate` ou référence | Le régime de TVA. |
| `density` / `length` / `volume` | numériques | Les caractéristiques physiques. |
| `status` | `int` | Le statut applicatif. |

Le trait descriptif `ProductProperty` (essence, apparence, certification, couleurs, …) et les propriétés additionnelles normalisées (`ProductAdditionalProperty::normalize()`) complètent la fiche — voir [Ingestion](ingestion.md).

---

## Les conditions tarifaires

Une **`PricingCondition`** est une règle de prix conditionnelle : une remise — ou une substitution de tarif — accordée à un ensemble ciblé d'acheteurs sur un ensemble ciblé d'articles, valable sur une période. C'est le pendant, côté vente, d'une condition d'achat fournisseur ; on la résout du plus précis au plus général pour un contexte (client, article, lieu) donné.

Ce qu'elle porte se lit en trois temps :

| Partie | Rôle |
|---|---|
| `selector` (`PricingConditionSelector`) | Le périmètre : **qui** (`customerScope` + `customerId`), **quoi** (`itemScope` + `itemId`, affiné par `categoryLevel`) et **où** (`areaServed`). |
| `adjustment` (`Adjustment`) | Le premier effet possible : une remise (pourcentage ou montant signé — négatif = majoration). |
| `substitutesSegment` (`PriceSegmentation`) | Le second effet possible : on remplace le segment tarifaire habituel de l'acheteur — appliqué *à la place* d'une remise (les deux effets sont exclusifs). |
| `excludedCustomers` / `excludedProducts` | Les exceptions découpées dans le périmètre. |
| `validFrom` / `validThrough` | La fenêtre de validité. |
| `quantityDiscount` (`PriceQuantityDiscount`) | Un effet optionnel par palier de quantité. |

Le périmètre se résout par granularité décroissante : `INDIVIDUAL` prime sur `GROUP`, qui prime sur `COMPANY`, qui prime sur `ALL` (de même côté article : `PRODUCT` › `CATEGORY` › `PROVIDER` › `ALL`). Les deux axes s'appuient sur les énumérations `PricingTargetScope` et `PricingItemScope`.

```json
{
  "@type": "PricingCondition",
  "selector": {
    "@type": "PricingConditionSelector",
    "customerScope": "https://schema.oihana.xyz/PricingTargetScope#Group",
    "customerId": "600214",
    "itemScope": "https://schema.oihana.xyz/PricingItemScope#Category",
    "itemId": "05",
    "categoryLevel": 1
  },
  "validThrough": "2026-12-31",
  "adjustment": { "@type": "Adjustment", "type": "https://schema.oihana.xyz/Discount", "percentage": 10 },
  "excludedCustomers": [ "600160" ]
}
```

## Le catalogue des satellites

| Classe | Rôle |
|---|---|
| `StockLevel` | Le niveau de stock, avec son point de vente (`assignedPOS` hydraté en `Warehouse`). |
| `TaxRate` | Le taux de TVA. |
| `PriceSegmentation` | La segmentation tarifaire d'un client ou d'un produit. |
| `ExtraPriceSpecification` | Une majoration/minoration, convertible en `UnitPriceSpecification` (`toUnitPriceSpecification()`). |
| `PriceQuantityDiscount` | La remise par quantité. |
| `PricingCondition` / `PricingConditionSelector` | La condition tarifaire (remise ou substitution) et son périmètre (voir ci-dessus). |
| `PaymentCondition` / `PaymentMethod` | Les conditions et moyens de paiement acceptés. |
| `ProductProviderInfo` | Les informations d'achat d'un produit chez son fournisseur (prix, marge, quantité de référence). |
| `ProductWarehouseInfo` / `ProviderProductWarehouseInfo` | Les informations produit par dépôt, côté maison et côté fournisseur. |
| `ProductWarehouseAvailability` | La disponibilité d'un produit dans un dépôt. |
| `ProductType` | Le type fonctionnel du produit (stock, suivi, règles…). |

## Les énumérations

| Énumération | Valeurs | Usage |
|---|---|---|
| `UnitOfSaleType` | `UNIT` , `PACKAGE` , `PARCEL` | Les niveaux de l'arbre des quantités et l'unité de vente (URLs `…#Unit`, `…#Package`, `…#Parcel`). |
| `PriceType` | prix d'achat, de vente, de référence… | Le type d'un prix dans une spécification. |
| `PriceComponentType` | les composantes d'un prix | La décomposition d'un prix (base, majorations, frais) — inclut aussi remise, majoration, éco-participation, consigne et emballage. |
| `BusinessEntityType` | professionnel, particulier… | La segmentation de clientèle d'une offre. |
| `PricingTargetScope` | `INDIVIDUAL` , `GROUP` , `COMPANY` , `ALL` | La granularité de l'acheteur ciblé par une `PricingCondition`. |
| `PricingItemScope` | `PRODUCT` , `CATEGORY` , `PROVIDER` , `ALL` | La granularité de l'article ciblé par une `PricingCondition`. |

---

## Voir aussi

- [Fonctions d'assistance](helpers.md) — `hydrateStockLevel()`, `hydrateAggregateOffer()` et les autres hydrateurs de cette couche.
- [Organisations Oihana](organizations.md) — `Provider` et son `ProductProviderInfo`.
- [Lieux Oihana](places.md) — `Warehouse`, le dépôt que référencent stock et disponibilité.
