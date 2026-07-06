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

| Namespace                              | Contenu                                                | Dépend de              |
|----------------------------------------|--------------------------------------------------------|------------------------|
| `org\schema\helpers\hydrate`           | Les 6 hydrateurs Schema.org purs                       | `org\schema` seulement |
| `xyz\oihana\schema\helpers\hydrate`    | Les 6 hydrateurs métier                                | `xyz` + `org`          |
| `xyz\oihana\schema\helpers\pivots`     | Les 3 pivots de compte                                 | `xyz` + `org`          |

Les hydrateurs métier appellent les hydrateurs purs pour leurs références imbriquées (`hydrateCustomer` délègue à `hydrateContactPoint` et `hydratePostalAddress`) — le sens de la flèche est toujours `xyz` → `org`.

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

---

## Exemple express — les pivots de compte

```php
use function xyz\oihana\schema\helpers\pivots\customerKey;
use function xyz\oihana\schema\helpers\pivots\sellerKeys;

// $user : un xyz\oihana\schema\auth\User dont les `identities` sont hydratées.

$key  = customerKey( $user ) ; // '137285125' — le client pour lequel le contact travaille, ou null
$keys = sellerKeys( $user ) ;  // [ '147737218' , '147737209' ] — les casquettes vendeur, dédupliquées
```

Un compte porte zéro, une ou plusieurs identités métier (voir [`BusinessIdentity`](business.md)) : `customerKey()` et `sellerKey()` résolvent la première du type attendu, `sellerKeys()` les résout toutes.

---

## Catalogue des fonctions

### `org\schema\helpers\hydrate` — les hydrateurs purs

| Fonction                    | Produit                          | Formes acceptées                        |
|-----------------------------|----------------------------------|-----------------------------------------|
| `hydrateAdditionalProperty` | `PropertyValue[]`                | liste indexée seulement, sinon `null`   |
| `hydrateContactPoint`       | `ContactPoint[]`                 | liste indexée seulement, sinon `null`   |
| `hydrateDefinedTerm`        | `DefinedTerm` ou `DefinedTerm[]` | simple, liste, passage à travers        |
| `hydrateGeoCoordinates`     | `GeoCoordinates` ou liste        | simple, liste, passage à travers        |
| `hydrateOfferPurchase`      | `OfferForPurchase`               | tableau ou instance, sinon `null` — type le `eligibleCustomerType` en `BusinessEntityType` |
| `hydratePostalAddress`      | `PostalAddress` ou liste         | simple (valeurs vides nettoyées), liste, passage à travers |

### `xyz\oihana\schema\helpers\hydrate` — les hydrateurs métier

| Fonction                  | Produit             | Références imbriquées hydratées                          |
|---------------------------|---------------------|----------------------------------------------------------|
| `hydrateAggregateOffer`   | `AggregateOffer`    | `availableAtOrFrom` (Warehouse), `eligibleQuantity`, `offers` (OfferForPurchase[]), `provider` |
| `hydrateCustomer`         | `Customer` ou liste | `contactPoint`, `address`                                |
| `hydrateCustomerEmployee` | `CustomerEmployee` ou liste | `additionalProperty`, `contactPoint`, `workLocation` (CustomerSite) |
| `hydrateCustomerSite`     | `CustomerSite` ou liste | `additionalProperty`, `address`, `geo`, `deliveryMethod` |
| `hydrateStockLevel`       | `StockLevel`        | `assignedPOS` (Warehouse)                                |
| `hydrateWarehouse`        | `Warehouse` ou liste | `ownedBy` (Subsidiary)                                  |

### `xyz\oihana\schema\helpers\pivots` — les pivots de compte

| Fonction      | Rend                | Rôle                                                                 |
|---------------|---------------------|----------------------------------------------------------------------|
| `customerKey` | `_key` ou `null`    | L'organisation cliente pour laquelle travaille le premier contact du compte (`worksFor`). |
| `sellerKey`   | `_key` ou `null`    | La clé de la première identité vendeur du compte.                    |
| `sellerKeys`  | liste de `_key`     | Toutes les clés vendeur du compte, dédupliquées, jamais de `null`.   |

---

## Voir aussi

- [Métier Oihana](business.md) — `BusinessIdentity`, le lien compte ↔ entité que les pivots parcourent.
- [Vocabulaire Schema.org](../schema-org/README.md) — les classes produites par les hydrateurs purs.
