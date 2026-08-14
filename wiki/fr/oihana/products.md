# `xyz\oihana\schema\products` — La couche commerce

Le namespace `xyz\oihana\schema\products` porte la **couche commerce** de la bibliothèque : `Product`, le produit enrichi de ses métadonnées de vente (unité de vente, quantités éligibles, catégories tarifaires, TVA, stock), et sa constellation de classes satellites — niveaux de stock, spécifications de prix, conditions et moyens de paiement, informations fournisseur et dépôt.

Le cœur du modèle est **l'arbre des quantités éligibles** : la chaîne unité → colis → palette qui dit comment un produit se conditionne, et dont découlent toutes les conversions d'unité de vente.

> 🇬🇧 This page is also available in [English](../../en/oihana/products.md).

---

## Quand l'utiliser

Choisissez `Product` dès qu'un article porte une dimension commerciale : une unité de vente, un conditionnement, un prix de référence, un niveau de stock. La classe étend `SomeProducts` de Schema.org — un document produit reste du JSON-LD standard, enrichi du contexte maison.

---

## L'arbre des quantités éligibles

Un produit se vend à l'unité, au colis ou à la palette (`unitOfSale`, valeurs de l'énumération `UnitOfSaleType`). L'arbre `eligibleQuantity` décrit la chaîne complète : chaque niveau est une `PhysicalQuantity` (quantité, code d'unité UN/CEFACT, libellé, plus `weight` et `volume`) dont le `valueReference` pointe le niveau supérieur. `PhysicalQuantity` étend `QuantitativeValue` : tout ce qui est typé sur la classe miroir — `Offer::$eligibleQuantity`, par exemple — l'accepte sans changement, et un consommateur qui ne lit que `value` et `unitCode` ne voit pas la différence.

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
| `findEligibleQuantityByType( $type )` | `PhysicalQuantity` ou `null` | Le niveau de l'arbre correspondant à un `UnitOfSaleType`. |

### Le poids et le volume de chaque niveau

Un produit vendu au colis ne pèse pas ce qu'il pèse à la pièce. Chaque niveau de l'arbre porte donc **sa propre masse et son propre encombrement**, sur le nœud qu'ils décrivent et jamais à côté :

```php
$package = $product->findEligibleQuantityByType( UnitOfSaleType::PACKAGE ) ;

$package->value  ;  // 1.403 — la quantité du niveau
$package->weight ;  // 15.419
$package->volume ;  // 0.0312
```

Les deux propriétés acceptent un nombre nu quand l'unité est implicite, ou une `QuantitativeValue` quand elle est déclarée (`{ "value": 15.419, "unitCode": "KGM" }`) — un tableau s'hydrate en `QuantitativeValue`.

### Les renseigner à l'hydratation

Chaque étage a ses **clés plates**, sur le patron des codes et des quantités — il suffit qu'un jeu de données les porte pour que l'arbre se construise avec ses mesures :

| Étage | Code | Quantité | Poids | Volume |
| :--- | :--- | :--- | :--- | :--- |
| unité | `eligibleUnitQuantityCode` | — | `eligibleUnitQuantityWeight` | `eligibleUnitQuantityVolume` |
| colis | `eligiblePackageQuantityCode` | `eligiblePackageQuantityValue` | `eligiblePackageQuantityWeight` | `eligiblePackageQuantityVolume` |
| palette | `eligiblePalletQuantityCode` | `eligiblePalletQuantityValue` | `eligiblePalletQuantityWeight` | `eligiblePalletQuantityVolume` |

```php
$product->eligibleUnitQuantityCode      = 'MTK' ;
$product->eligibleUnitQuantityWeight    = 6.4 ;

$product->eligiblePackageQuantityCode   = 'PK' ;
$product->eligiblePackageQuantityValue  = 0.456 ;
$product->eligiblePackageQuantityWeight = 2.9184 ;

$product->eligiblePalletQuantityCode    = 'PX' ;
$product->eligiblePalletQuantityValue   = 38.304 ;
$product->eligiblePalletQuantityWeight  = 245.1456 ;

$product->findEligibleQuantityByType( UnitOfSaleType::PARCEL )->weight ;  // 245.1456
```

🔑 **Rien n'est calculé.** Un étage qui ne déclare pas son poids n'en reçoit pas — jamais un zéro, qui se lirait « sans poids » là où la vérité est « inconnu ». Déduire une mesure absente d'un volume et d'une densité produirait un nombre impossible à distinguer d'un nombre déclaré ; c'est l'affaire de qui l'affiche, pas de la classe qui le porte.

⚠️ **Un étage sans code d'unité n'assemble rien**, poids ou pas : une mesure sans étage pour la nommer n'a nulle part où aller.

🔑 **Le rapport entre deux niveaux redonne la chaîne de conditionnement** — combien de pièces dans un colis, combien de colis sur une palette — sans qu'aucune de ces valeurs soit stockée deux fois.

⚠️ **À distinguer de `Product::$weight`**, hérité du miroir Schema.org : celui-là est le poids de l'unité facturée, sans unité déclarée ni niveau rattaché. Il ne change pas.

🔑 **La chaîne se type sur toute sa hauteur.** `PhysicalQuantity::$valueReference` porte l'attribut d'hydratation, donc `Reflection::hydrate()` descend jusqu'au dernier étage : un poids se lit `->weight` partout, jamais `['weight']` un cran plus bas. `findEligibleQuantityByType()` reste le chemin d'accès direct à un étage donné — **et l'étage rendu type sa descendance**, de sorte que la syntaxe de lecture ne change pas avec la profondeur.

### Parcourir un arbre qu'on n'a pas construit

`findEligibleQuantityByType()` ne sait parcourir qu'un arbre : celui du produit. Or **cet arbre est `null` sur tout produit relu depuis une base** — il est construit par le setter magique au moment de l'import, recopié sur chaque offre, et c'est cette copie qui est stockée. Le consommateur qui tient l'arbre tient donc celui de l'offre, pas celui du produit.

Le parcours se prend alors comme fonction, sur n'importe quel arbre :

```php
use function xyz\oihana\schema\helpers\hydrate\findPhysicalQuantityByType;

$parcel = findPhysicalQuantityByType( UnitOfSaleType::PARCEL , $offer->eligibleQuantity ) ;

$parcel?->weight ;  // 245.1456
$parcel?->volume ;  // 2.6208
```

L'arbre passé peut être **typé ou brut** — les lignes telles qu'une lecture de base les laisse — et l'étage rendu est toujours une `PhysicalQuantity`, **sa descendance comprise** : `$parcel->valueReference->weight` se lit comme `$parcel->weight`, la syntaxe ne change pas avec la profondeur.

🚨 **C'est ce qui rend la perte du poids impossible plutôt que réparable.** Sans cette entrée, qui tient un arbre réécrit le parcours à la main, et un parcours écrit à la main reconstruit les étages en `QuantitativeValue` : cette classe ne déclare ni poids ni volume, et une classe écarte les clés qu'elle ne déclare pas. Les deux disparaissent **sans erreur et sans trace**.

`Product::findEligibleQuantityByType()` n'est plus que cet appel sur son propre arbre — même signature, même résultat.

⚠️ **Le constructeur, lui, assigne brut** — aucun attribut n'y agit. Sur ce chemin, passez par [`hydratePhysicalQuantity()`](helpers.md), qui descend la chaîne explicitement ; c'est ce que fait `hydrateAggregateOffer()`.

⚠️ Chez Schema.org, `valueReference` accepte autre chose qu'une quantité — une énumération, une valeur qualitative. **Sur `PhysicalQuantity`, c'est le niveau de conditionnement suivant, et rien d'autre** : c'est la raison d'être de la classe.

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
| `fees` | `FeeSpecification[]` | Les frais dus **en plus du prix** — contribution environnementale, consigne, emballage, port (voir ci-dessous). |
| `status` | `int` | Le statut applicatif. |

Le trait descriptif `ProductProperty` (essence, apparence, certification, couleurs, …) et les propriétés additionnelles normalisées (`ProductAdditionalProperty::normalize()`) complètent la fiche — voir [Ingestion](ingestion.md).

### Les frais — `fees`

Certains articles doivent une somme **en plus de leur prix** : une contribution environnementale, une consigne, un emballage, un port. Chacune est une `FeeSpecification`, c'est-à-dire une `UnitPriceSpecification` à laquelle on ajoute le **barème publié** dont elle dérive.

```json
{
  "@type": "FeeSpecification",
  "priceComponentType": "https://schema.oihana.xyz/EnvironmentalFee",
  "price": 0.0157,
  "priceCurrency": "EUR",
  "unitCode": "C62",
  "identifier": "P4CHANT01",
  "publisher": { "@type": "Organization", "name": "Recycling Body" },
  "rate": { "@type": "UnitPriceSpecification", "price": 215, "priceCurrency": "EUR", "unitCode": "TNE" }
}
```

🔑 **`price` est exprimé dans l'unité où l'article est facturé**, jamais dans celle du barème. Appliquer un frais tient donc en une multiplication — `montant = quantité de la ligne × price` — sans aucune table de correspondance côté client.

`rate` conserve à côté le barème tel qu'il est publié, dans **son** unité : ici 215 € la tonne pour un article facturé à la pièce. Les deux cohabitent volontairement — l'un calcule, l'autre explique — exactement comme une offre expose un `price` final à côté de son `priceComponent[]`. Un montant facturé reste ainsi justifiable sans aller le chercher ailleurs.

**Une liste, et non un champ par nature de frais** : `PriceComponentType` en énumère déjà plusieurs, elles se comportent toutes pareil, et un article peut relever de plusieurs filières à la fois.

🔑 **Le barème se type comme le frais lui-même.** `FeeSpecification::$rate` porte l'attribut d'hydratation, donc `Reflection::hydrate()` donne `->rate->price` là où un tableau brut se lirait `['price']` — sur un couple dont tout l'intérêt est d'être lu des deux côtés à la fois.

⚠️ **Le constructeur, lui, assigne brut** — aucun attribut n'y agit. Sur ce chemin, passez par [`hydrateFeeSpecification()`](helpers.md) : il type le `rate`, résout le `publisher` d'après son `@type`, et laisse tel quel un barème déjà typé.

#### Quand le frais n'est pas chiffrable

Un barème à la tonne réclame un poids ; un barème à la pièce réclame de savoir combien de pièces tient un conditionnement. Quand le catalogue ne le dit pas, **le frais reste dû** — il n'est simplement pas quantifiable.

L'entrée existe alors **sans `price`**, avec son `rate` et un `unresolvedReason` (→ `FeeUnresolvedReason`) :

| Valeur | Ce qu'elle dit |
| :--- | :--- |
| `MISSING_FEE_RATE` | aucun barème n'est rattaché à l'article |
| `MISSING_PRODUCT_MEASURE` | la mesure sur laquelle porte le barème — poids, volume, épaisseur — est absente ou nulle |
| `UNKNOWN_PACKAGE_CONTENT` | l'article est facturé au conditionnement, dont le contenu est inconnu |

🔑 **Se lit avec l'absence de `price`** : « c'est dû, voici le barème, et voici ce qui nous empêche de le chiffrer ». Un zéro dirait « rien n'est dû », ce qui est faux. Un consommateur qui multiplie une quantité par un `price` absent obtient zéro ou une erreur — **jamais un montant faux**.

⚠️ **À ne pas confondre avec `ExtraPriceSpecification`**, qui dérive aussi d'`UnitPriceSpecification` mais sert la segmentation tarifaire et n'a rien à voir avec les frais.

---

## Les conditions tarifaires

Une **`PricingCondition`** est une règle de prix conditionnelle : une remise, une substitution de tarif ou un prix net imposé, accordée à un ensemble ciblé d'acheteurs sur un ensemble ciblé d'articles, valable sur une période. C'est le pendant, côté vente, d'une condition d'achat fournisseur ; on la résout du plus précis au plus général pour un contexte (client, article, lieu) donné.

Ce qu'elle porte se lit en trois temps :

| Partie | Rôle |
|---|---|
| `selector` (`PricingConditionSelector`) | Le périmètre : **qui** (`customerScope` + `customerId`), **quoi** (`itemScope` + `itemId`, affiné par `categoryLevel`) et **où** (`areaScope` + `areaServed`). |
| `adjustment` (liste d'`Adjustment`) | Le premier effet possible : une liste de remises empilées appliquées dans l'ordre (chacune un pourcentage ou montant signé — négatif = majoration). Toujours une liste, même pour une remise unique. |
| `substitutesSegment` (`PriceSegmentation`) | Le second effet possible : on remplace le segment tarifaire habituel de l'acheteur — appliqué *à la place* d'une remise. |
| `fixedPrice` (`MonetaryAmount`) | Le troisième effet possible : un prix net fixe imposé *à la place* de tout ajustement ou substitution de segment. Les trois effets sont exclusifs. |
| `free` (`bool`) | Indique que l'article est offert (gratuit) sous cette condition. |
| `excludedCustomers` / `excludedProducts` | Les exceptions découpées dans le périmètre. |
| `validFrom` / `validThrough` | La fenêtre de validité. |
| `quantityDiscount` (`PriceQuantityDiscount`) | Un effet optionnel par palier de quantité. |

Le périmètre se résout par granularité décroissante sur trois axes : côté acheteur `INDIVIDUAL` prime sur `GROUP`, qui prime sur `COMPANY`, qui prime sur `ALL` ; côté article `PRODUCT` › `CATEGORY` › `PROVIDER` › `ALL` ; côté lieu `WAREHOUSE` (un point de vente) prime sur `COMPANY`, qui prime sur `GROUP`, qui prime sur `ALL` (partout). `areaScope` indique la nature du lieu porté par `areaServed`. Les trois axes s'appuient sur les énumérations `PricingTargetScope`, `PricingItemScope` et `PricingAreaScope`.

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
  "adjustment": [ { "@type": "Adjustment", "type": "https://schema.oihana.xyz/Discount", "percentage": 10 } ],
  "excludedCustomers": [ "600160" ]
}
```

## L'offre au tarif d'un client

Une **`CustomerOffer`** est une offre de vente destinée à **un client précis** : le prix de son segment tarifaire dans son dépôt, éventuellement obtenu via une `PricingCondition`. Elle spécialise `OfferForPurchase` et réutilise toute la surface tarifaire héritée (`price`, `priceCurrency`, `priceSpecification`, `eligibleCustomerType` pour le segment appliqué, `availableAtOrFrom` pour le dépôt, `validFrom` / `validThrough`, `seller`), en y ajoutant deux propriétés propres :

| Propriété | Rôle |
|---|---|
| `customer` | Une référence légère au client bénéficiaire (`id`, `name`, `url`). |
| `appliedCondition` (`PricingCondition`) | La condition tarifaire qui a produit le prix — `null` quand le tarif de base s'applique tel quel. |

Le `priceSpecification` est typiquement un `CompoundPriceSpecification` dont les composantes décomposent le prix de grille (`ListPrice`), la remise éventuelle (`Discount`) et le prix effectif (`SalePrice`) ; côté vente, une composante `SellingMargin` peut porter la marge.

```json
{
  "@type": "CustomerOffer",
  "customer": { "@type": "Customer", "id": "216303", "name": "Menuiserie Fabre" },
  "eligibleCustomerType": { "@type": "BusinessEntityType", "id": 4, "name": "Pro." },
  "availableAtOrFrom": { "@type": "Warehouse", "id": "1", "name": "Entrepôt principal" },
  "price": 9.20,
  "priceCurrency": "EUR",
  "appliedCondition": {
    "@type": "PricingCondition",
    "adjustment": [ { "@type": "Adjustment", "type": "https://schema.oihana.xyz/Discount", "percentage": 7.15 } ]
  },
  "priceSpecification": {
    "@type": "CompoundPriceSpecification",
    "priceComponent": [
      { "@type": "UnitPriceSpecification", "price": 9.91, "priceType": "https://schema.org/ListPrice" },
      { "@type": "UnitPriceSpecification", "price": 7.15, "priceComponentType": "https://schema.oihana.xyz/Discount", "unitText": "%" },
      { "@type": "UnitPriceSpecification", "price": 9.20, "priceType": "https://schema.org/SalePrice" }
    ]
  }
}
```

## Le catalogue des satellites

| Classe | Rôle |
|---|---|
| `StockLevel` | Le niveau de stock, avec son point de vente (`assignedPOS` hydraté en `Warehouse`). |
| `TaxRate` | Le taux de TVA. |
| `PriceSegmentation` | La segmentation tarifaire d'un client ou d'un produit. |
| `ExtraPriceSpecification` | Une majoration/minoration, convertible en `UnitPriceSpecification` (`toUnitPriceSpecification()`). |
| `FeeSpecification` | Un frais dû en plus du prix, avec le barème publié dont il dérive (`rate`) et, s'il n'est pas chiffrable, la raison (`unresolvedReason`). |
| `PhysicalQuantity` | Un niveau de l'arbre des quantités éligibles : une `QuantitativeValue` qui dit en plus ce qu'elle pèse (`weight`) et ce qu'elle occupe (`volume`). |
| `PriceQuantityDiscount` | La remise par quantité. |
| `PricingCondition` / `PricingConditionSelector` | La condition tarifaire (remise ou substitution) et son périmètre (voir ci-dessus). |
| `CustomerOffer` | L'offre de vente au tarif d'un client précis (segment × dépôt, condition éventuelle) ; spécialise `OfferForPurchase` (voir ci-dessus). |
| `PaymentCondition` / `PaymentMethod` | Les conditions et moyens de paiement acceptés. |
| `ProductProviderInfo` | Les informations d'achat d'un produit chez son fournisseur (prix, marge, quantité de référence). |
| `ProductWarehouseInfo` / `ProviderProductWarehouseInfo` | Les informations produit par dépôt, côté maison et côté fournisseur. |
| `ProductWarehouseAvailability` | La disponibilité d'un produit dans un dépôt. |
| `ProductType` | Le type fonctionnel du produit — `stockable`, `trackable`, ainsi que la couleur d'affichage maison `color` (`#RRGGBB`, via [`HasColor`](thesaurus.md), le même indice que portent les familles du thésaurus). |

## Les énumérations

| Énumération | Valeurs | Usage |
|---|---|---|
| `UnitOfSaleType` | `UNIT` , `PACKAGE` , `PARCEL` | Les niveaux de l'arbre des quantités et l'unité de vente (URLs `…#Unit`, `…#Package`, `…#Parcel`). |
| `FeeUnresolvedReason` | `MISSING_FEE_RATE` , `MISSING_PRODUCT_MEASURE` , `UNKNOWN_PACKAGE_CONTENT` | Pourquoi un frais dû n'a pas pu être chiffré — porté par `FeeSpecification::$unresolvedReason`, à lire avec l'absence de `price`. |
| `PriceType` | prix d'achat, de vente, de référence… | Le type d'un prix dans une spécification. |
| `PriceComponentType` | les composantes d'un prix | La décomposition d'un prix (base, majorations, frais) — inclut aussi remise, majoration, marge de vente, éco-participation, consigne et emballage. |
| `BusinessEntityType` | professionnel, particulier… | La segmentation de clientèle d'une offre. |
| `PricingTargetScope` | `INDIVIDUAL` , `GROUP` , `COMPANY` , `ALL` | La granularité de l'acheteur ciblé par une `PricingCondition`. |
| `PricingItemScope` | `PRODUCT` , `CATEGORY` , `PROVIDER` , `ALL` | La granularité de l'article ciblé par une `PricingCondition`. |
| `PricingAreaScope` | `WAREHOUSE` , `COMPANY` , `GROUP` , `ALL` | La granularité du lieu où s'applique une `PricingCondition` (nature du lieu porté par `areaServed`). |

---

## Voir aussi

- [Fonctions d'assistance](helpers.md) — `hydrateStockLevel()`, `hydrateAggregateOffer()` et les autres hydrateurs de cette couche.
- [Organisations Oihana](organizations.md) — `Provider` et son `ProductProviderInfo`.
- [Lieux Oihana](places.md) — `Warehouse`, le dépôt que référencent stock et disponibilité.
