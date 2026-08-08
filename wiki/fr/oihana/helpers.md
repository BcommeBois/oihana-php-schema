# Fonctions d'assistance — hydratation et pivots

La bibliothèque expose une couche de **fonctions d'assistance** (*helpers*) : des fonctions libres, chargées automatiquement par Composer (section `autoload.files`), qui accompagnent les classes de schéma sans leur ajouter de méthodes. Une fonction d'assistance se consomme par un simple `use function` — pas d'instance, pas d'état, une entrée, une sortie.

Deux familles vivent dans cette couche :

- les **hydrateurs** — transformer une donnée brute (un tableau associatif sorti d'une base ou d'une API) en objet de schéma typé, références imbriquées comprises ;
- les **pivots de compte** — résoudre les identités métier d'un compte authentifié (`User`) en clés d'organisation ou de vendeur, celles qui délimitent le périmètre de ses ressources.

> 🇬🇧 This page is also available in [English](../../en/oihana/helpers.md).

---

## Quand l'utiliser

Choisissez un hydrateur lorsqu'une donnée arrive **déjà structurée mais non typée** — un document projeté par une requête, une réponse JSON décodée — et que vous voulez la manipuler comme un objet de schéma sans écrire le câblage des références imbriquées à la main :

- `hydrateCustomer( $document )` rend un `Customer` dont les `contactPoint` sont des `ContactPoint` et l'`address` une `PostalAddress` ;
- chaque hydrateur accepte indifféremment **une définition simple**, **une liste indexée** de définitions (les entrées invalides sont filtrées), ou **toute autre valeur**, rendue telle quelle (passage à travers).

Choisissez un pivot lorsqu'un compte authentifié doit être ramené à **la clé qui délimite son périmètre** : le client pour lequel travaille un contact, le ou les vendeurs qu'incarne un commercial.

---

## Le chargement

Les fonctions sont enregistrées dans la section `autoload.files` du `composer.json` de la bibliothèque : elles sont disponibles partout, sans instanciation. L'import suit le namespace de la fonction :

```php
use function org\schema\helpers\hydrate\hydratePostalAddress;
use function xyz\oihana\schema\helpers\hydrate\hydrateCustomer;
use function xyz\oihana\schema\helpers\pivots\sellerKeys;
```

---

## L'étagement des namespaces

La couche respecte la règle de la bibliothèque : `org\schema` est le miroir pur du vocabulaire Schema.org, `xyz\oihana\schema` est l'extension maison qui s'appuie dessus — **jamais l'inverse**.

| Namespace                                     | Contenu                             | Dépend de              |
|-----------------------------------------------|-------------------------------------|------------------------|
| `org\schema\helpers\hydrate`                  | Les 7 hydrateurs Schema.org purs    | `org\schema` seulement |
| `xyz\oihana\schema\helpers\hydrate`           | Les 6 hydrateurs métier             | `xyz` + `org`          |
| `xyz\oihana\schema\helpers\hydrate\documents` | Les 3 hydrateurs de documents       | `xyz` + `org`          |
| `xyz\oihana\schema\helpers\pivots`            | Les 3 pivots de compte              | `xyz` + `org`          |

Les hydrateurs métier appellent les hydrateurs purs pour leurs références imbriquées (`hydrateCustomer` délègue à `hydrateContactPoint` et `hydratePostalAddress`) — le sens de la flèche est toujours `xyz` → `org`.

Le sous-dossier `hydrate/documents` regroupe les hydrateurs des [documents commerciaux](business-documents.md). Ils se distinguent des autres sur un point : au lieu d'appeler le constructeur puis de recâbler chaque référence imbriquée à la main, ils passent par `Reflection::hydrate()` — le seul chemin qui honore les attributs `#[HydrateAs]` / `#[HydrateWith]` déjà portés par `BusinessDocumentLine`/`BusinessDocument`. La correspondance reste donc déclarée une seule fois, sur la classe.

`hydrateOrganizationOrPerson`, lui, vit dans `org\schema\helpers\hydrate` : n'ayant besoin que des classes `org\schema\Organization`/`org\schema\Person`, il reste un hydrateur pur. Il résout une union `Organization|Person` d'après le `@type` du contenu, et accepte deux classes cibles personnalisées (`$organizationClass`/`$personClass`) pour viser un sous-type métier.

> **Note.** Les unions ambiguës des documents (`customer`/`seller`/`author`, `broker`/`provider`, `item`, `ownedBy`) sont désormais tranchées **déclarativement**, par un `#[HydrateWith(A::class, B::class)]` posé sur la propriété : `Reflection::hydrate()` choisit alors la bonne classe d'après le discriminateur (`@type`, `atType` ou `type`) et, à défaut, d'après les propriétés présentes. La résolution est donc correcte **même sans passer par un helper**. Les hydrateurs de cette couche restent utiles pour ce que la réflexion ne fait pas : accepter indifféremment une définition simple, une liste indexée ou une valeur quelconque rendue telle quelle.

---

## Exemple express — hydrater un client

```php
use function xyz\oihana\schema\helpers\hydrate\hydrateCustomer;

$customer = hydrateCustomer
([
    'name'         => 'South Wood Company' ,
    'contactPoint' => [ [ 'telephone' => '05 59 00 00 00' ] ] ,
    'address'      => [ 'streetAddress' => '20 Rue Mably' , 'postalCode' => '33000' ] ,
]);

$customer->name ;                        // 'South Wood Company'
$customer->contactPoint[0]->telephone ;  // '05 59 00 00 00'  (ContactPoint)
$customer->address->streetAddress ;      // '20 Rue Mably'    (PostalAddress)
```

Les trois formes acceptées par les hydrateurs de choses (`hydrateCustomer`, `hydrateWarehouse`, `hydrateDefinedTerm`, …) :

```php
hydrateCustomer( [ 'name' => 'A' ] ) ;                      // une définition  → Customer
hydrateCustomer( [ [ 'name' => 'A' ] , [ 'name' => 'B' ] ] ); // une liste       → Customer[]
hydrateCustomer( 'brut' ) ;                                 // autre valeur    → rendue telle quelle
```

### La règle des références imbriquées

Un hydrateur qui résout des références imbriquées (`hydrateCustomer`, `hydrateCustomerSite`,
`hydrateBusinessDocument`, …) applique à chacune la même règle :

- **il n'hydrate que ce qui est un tableau** — là où il y a quelque chose à hydrater. Une
  référence textuelle non résolue ou une instance déjà typée est laissée intacte, jamais
  réécrite ;
- **la réponse de l'hydrateur imbriqué est ensuite écrite telle quelle, `null` compris.**
  Un tableau qui ne résout rien — liste vide, liste d'entrées non hydratables — devient
  `null`, jamais un tableau brut résiduel ;
- **une propriété que la charge utile n'a jamais portée n'est pas inventée.** L'hydratation
  la laisse dans l'état que lui donne sa déclaration : déclarée sans valeur par défaut,
  elle reste non initialisée, donc absente de la forme sérialisée ; déclarée `= null`, elle
  reste à `null` et est sérialisée comme telle. Dans les deux cas, l'hydratation n'y touche
  pas.

C'est le deuxième point qui met les deux chemins d'accord : une référence imbriquée répond
la même chose à travers son parent qu'à travers l'hydrateur imbriqué appelé seul.

```php
hydrateDeliveryRouteAssignment( [] ) ;                                // null

$site = hydrateCustomerSite( [ 'name' => 'A' , 'deliveryRoute' => [] ] ) ;
$site->deliveryRoute ;                                                // null — la même réponse
```

---

## Exemple express — hydrater les lignes d'un document

```php
use function xyz\oihana\schema\helpers\hydrate\documents\hydrateDocumentLine;

// $document : un BusinessDocument sorti du serveur — ses lignes sont des tableaux bruts.

$document->documentLines = hydrateDocumentLine( $document->documentLines ) ;

$line = $document->documentLines[0] ;

$line->quantity->value ;                // 5       (QuantitativeValue)
$line->price->price ;                   // 22.5    (CompoundPriceSpecification)
$line->price->priceComponent[0]->name ; // 'base'  (UnitPriceSpecification)
$line->taxes[0]->rate ;                 // 20.0    (TaxDetail)
$line->total->value ;                   // 135.0   (MonetaryAmount)
$line->item->name ;                     // 'Peinture blanche' (Product)
```

Le constructeur de `BusinessDocument` ne fait qu'une affectation superficielle : sans cet appel, `documentLines` reste un tableau de tableaux, et le prix, la quantité, les taxes et les totaux d'une ligne ne deviennent jamais des objets.

L'`item` d'une ligne est une union `Product|Service` : c'est le `@type` du contenu qui décide de sa classe — `Service` pour une prestation, le `Product` enrichi commerce sinon — grâce au `#[HydrateWith(Product::class, Service::class)]` porté par la propriété.

---

## Exemple express — hydrater un document entier

```php
use function xyz\oihana\schema\helpers\hydrate\documents\hydrateBusinessDocument;
use xyz\oihana\schema\business\documents\Invoice;

// $raw : la réponse brute du serveur pour une facture.

$invoice = hydrateBusinessDocument( $raw , Invoice::class ) ;

$invoice->customer->name ; // 'Jean Dupont' — Person, même si Organization vient en premier dans l'union
$invoice->seller->name   ; // 'ACME'        — Organization
$invoice->provider->name ; // 'Sous-traitant SA' — Organization, même sans @type explicite
```

`customer`, `seller` et `author` sont typés `Organization|Person` sur tout `BusinessDocument` (`broker`/`provider` en plus sur `Invoice`) — la même ambiguïté que l'`item` d'une ligne, transposée à l'en-tête, et tranchée de la même façon par un `#[HydrateWith(Organization::class, Person::class)]`. `hydrateBusinessDocument()` hydrate le document via `Reflection::hydrate()` — donc tout, en-tête comme lignes, sort typé — puis repasse sur ces propriétés via `hydrateOrganizationOrPerson()`. Son second paramètre (`$class`, par défaut `BusinessDocument::class`) sert n'importe quel maillon du cycle — `Quote::class`, `PurchaseOrder::class`, `CreditNote::class`… — puisqu'aucun d'eux ne redéfinit `customer`/`seller`/`author`.

L'intérêt du helper n'est donc pas la résolution de l'union — l'attribut s'en charge, même pour un appel direct à `Reflection::hydrate()` — mais les trois formes d'entrée acceptées : un document, une liste de documents, ou toute autre valeur rendue telle quelle.

---

## Exemple express — les pivots de compte

```php
use function xyz\oihana\schema\helpers\pivots\customerKey;
use function xyz\oihana\schema\helpers\pivots\customerKeys;
use function xyz\oihana\schema\helpers\pivots\sellerKeys;

// $user : un xyz\oihana\schema\auth\User dont les `identities` sont hydratées.

$key     = customerKey( $user )  ; // '137285125' — le client pour lequel le contact travaille, ou null
$clients = customerKeys( $user ) ; // [ '137285125' , '137285130' ] — tous ses clients, dédupliqués
$keys    = sellerKeys( $user )   ; // [ '147737218' , '147737209' ] — les casquettes vendeur, dédupliquées
```

Un compte porte zéro, une ou plusieurs identités métier (voir [`BusinessIdentity`](business.md)) : `customerKey()` et `sellerKey()` résolvent la première du type attendu ; `customerKeys()` et `sellerKeys()` les résolvent toutes.

---

## Catalogue des fonctions

### `org\schema\helpers\hydrate` — les hydrateurs purs

| Fonction                    | Produit                          | Formes acceptées                        |
|-----------------------------|----------------------------------|-----------------------------------------|
| `hydrateAdditionalProperty` | `PropertyValue[]`                | liste indexée seulement, sinon `null`   |
| `hydrateContactPoint`       | `ContactPoint[]`                 | liste indexée seulement, sinon `null`   |
| `hydrateDefinedTerm`        | `DefinedTerm` ou `DefinedTerm[]` | simple, liste, passage à travers — classe cible personnalisable via `$class` (ex. `DeliveryMethodTerm::class`) |
| `hydrateGeoCoordinates`     | `GeoCoordinates` ou liste        | simple, liste, passage à travers        |
| `hydrateOfferPurchase`      | `OfferForPurchase`               | tableau ou instance, sinon `null` — type le `eligibleCustomerType` en `BusinessEntityType` |
| `hydrateOrganizationOrPerson` | `Organization` ou `Person`, ou liste | Résout l'union d'après le `@type` : `Person` → `Person`, sinon `Organization` (défaut sûr) — classes cibles personnalisables via `$organizationClass`/`$personClass` |
| `hydratePostalAddress`      | `PostalAddress` ou liste         | simple (valeurs vides nettoyées), liste, passage à travers |

### `xyz\oihana\schema\helpers\hydrate` — les hydrateurs métier

| Fonction                  | Produit             | Références imbriquées hydratées                          |
|---------------------------|---------------------|----------------------------------------------------------|
| `hydrateAggregateOffer`   | `AggregateOffer`    | `availableAtOrFrom` (Warehouse), `eligibleQuantity`, `offers` (OfferForPurchase[]), `provider` |
| `hydrateCustomer`         | `Customer` ou liste | `contactPoint`, `address`                                |
| `hydrateCustomerEmployee` | `CustomerEmployee` ou liste | `additionalProperty`, `contactPoint`, `workLocation` (CustomerSite) |
| `hydrateCustomerSite`     | `CustomerSite` ou liste | `additionalProperty`, `address`, `geo`, `deliveryMethod` (DeliveryMethodTerm), `deliveryRoute` (DeliveryRouteAssignment[]) |
| `hydrateDeliveryRouteAssignment` | `DeliveryRouteAssignment` ou liste | `route` (DeliveryRouteTerm, lorsque la ligne de référence jointe est présente — un code nu est laissé tel quel) |
| `hydrateStockLevel`       | `StockLevel`        | `assignedPOS` (Warehouse)                                |
| `hydrateWarehouse`        | `Warehouse` ou liste | `ownedBy` (Subsidiary)                                  |

### `xyz\oihana\schema\helpers\hydrate\documents` — les hydrateurs de documents

| Fonction                    | Produit                          | Références imbriquées hydratées                          |
|-----------------------------|-----------------------------------|----------------------------------------------------------|
| `hydrateBusinessDocument` | `BusinessDocument` (ou sous-classe via `$class`), ou liste | `customer`, `seller`, `author` (et `broker`/`provider` sur `Invoice`), résolus par `hydrateOrganizationOrPerson` — le reste (`documentLines`, `taxes`, `totals`…) vient de `Reflection::hydrate()` |
| `hydrateDocumentLine`     | `BusinessDocumentLine` ou liste  | `adjustments` (Adjustment[]), `price` (CompoundPriceSpecification + son `priceComponent`), `quantity`, `subtotal`, `taxes` (TaxDetail[]), `total`, `item` (délégué ci-dessous) |
| `hydrateDocumentLineItem` | `Product` ou `Service`, ou liste | Résout l'union d'après le `@type` : suffixe `Service` → `Service`, sinon `Product` (avec ses `eligibleQuantity` et `inventoryLevel`) |

### `xyz\oihana\schema\helpers\pivots` — les pivots de compte

| Fonction      | Rend                | Rôle                                                                 |
|---------------|---------------------|----------------------------------------------------------------------|
| `customerKey` | `_key` ou `null`    | L'organisation cliente pour laquelle travaille le premier contact du compte (`worksFor`). |
| `customerKeys`| liste de `_key`     | Toutes les organisations clientes dont le compte est contact, dédupliquées, jamais de `null`. |
| `sellerKey`   | `_key` ou `null`    | La clé de la première identité vendeur du compte.                    |
| `sellerKeys`  | liste de `_key`     | Toutes les clés vendeur du compte, dédupliquées, jamais de `null`.   |

---

## Voir aussi

- [Métier Oihana](business.md) — `BusinessIdentity`, le lien compte ↔ entité que les pivots parcourent.
- [Documents commerciaux](business-documents.md) — `BusinessDocument`, `BusinessDocumentLine` et les objets de valeur que les hydrateurs de documents produisent.
- [Vocabulaire Schema.org](../schema-org/README.md) — les classes produites par les hydrateurs purs.
