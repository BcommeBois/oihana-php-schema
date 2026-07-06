# L'ingestion — hydrater une ligne plate en objet imbriqué

L'**ingestion** est le mécanisme qui permet à une ligne plate de jeu de données — une ligne SQL, un export tableur, un document projeté — d'hydrater directement un objet de schéma **imbriqué**, sans câblage manuel. La clé plate `addressLocality` devient le champ `address.addressLocality` d'une `PostalAddress` ; la clé `mobile` devient un `ContactPoint` typé dans la collection `contactPoint`.

Le pont est le `__set` magique de PHP : quand l'hydratation affecte une propriété **non déclarée** sur la classe, `__set` la présente à une chaîne de petits traits spécialisés — chacun reconnaît sa famille de clés plates et construit l'objet imbriqué correspondant.

> 🇬🇧 This page is also available in [English](../../en/oihana/ingestion.md).

---

## Le mécanisme en une image

```php
public function __set( string $property , mixed $value ) :void
{
    $this->setAdditionalProperties     ( $property , $value ) ||
    $this->setPostalAddressProperties  ( $property , $value ) ||
    $this->setContactPointProperty     ( $property , $value ) ;
}
```

Chaque maillon rend `true` s'il a reconnu et absorbé la clé — la chaîne s'arrête au premier preneur. Une clé qu'aucun maillon ne reconnaît est **silencieusement ignorée** : une colonne excédentaire dans le jeu de données ne casse rien.

---

## Exemple express

```php
use xyz\oihana\schema\organizations\Customer;

// Une ligne plate, telle qu'elle sort d'une base ou d'un export :
$customer = new Customer
([
    'name'             => 'South Wood Company' ,
    'addressLocality'  => 'BORDEAUX' ,
    'postalCode'       => '33000' ,
    'defaultTelephone' => '05 56 00 00 00' ,
    'mobile'           => '06 00 00 00 00' ,
]);

$customer->address->addressLocality ;        // 'BORDEAUX'      (PostalAddress)
$customer->contactPoint[0]->contactType ;    // '…/ContactType#Default'
$customer->contactPoint[1]->contactType ;    // '…/ContactType#mobile'
```

---

## Le catalogue des traits

| Trait | Clés plates reconnues | Construit |
|---|---|---|
| `SetPostalAddressTrait` | `streetAddress`, `addressLocality`, `postalCode`, `addressCountry`, `addressEmail`, `addressTelephone`, … | La `PostalAddress` du champ `address`. Le compagnon statique `normalizePostalAddress()` éclate une adresse `"rue;complément;boîte postale"` en trois champs. |
| `SetContactPointTrait` | `defaultTelephone`, `defaultEmail`, `defaultFaxNumber`, `homeTelephone`, `mobile`, `mobileProfessional`, … | La collection `contactPoint` : un `ContactPoint` par usage (`ContactType` : par défaut, domicile, mobile…), fusionné si l'usage existe déjà. Les numéros invalides et les courriels malformés sont écartés. |
| `SetGeoCoordinatesTrait` | `geoLatitude`, `geoLongitude`, `geoElevation`, `geoDistance` | Les `GeoCoordinates` du champ `geo`. |
| `SetProductProviderInfoTrait` | `buyingPrice`, `buyingPriceMargin`, `buyingPriceReferenceQuantity`, `nextBuyingPrice`, … | Le `ProductProviderInfo` d'un fournisseur (champ `productInfo`). |
| `SetAdditionalPropertyTrait` (org) | — | L'injecteur bas niveau : ajoute une `PropertyValue` à `additionalProperty`. Chaque classe le spécialise avec sa liste de clés admises et sa normalisation (`CustomerAdditionalProperty`, `PersonAdditionalProperty`, `ProductAdditionalProperty`, `SiteAdditionalProperty` — coercitions booléennes et entières). |
| `SiteTrait` | l'agrégat | Compose adresse + contacts + géolocalisation + propriétés additionnelles pour les lieux (`Place`, `CustomerSite`, `ProviderSite`). |
| `UnitPriceSpecificationTrait` | — | `getLastUnitPriceSpecification()` — la dernière spécification de prix unitaire d'une collection. |
| `ProductProperty` | — | Les attributs descriptifs d'un produit (essence, apparence, certification, couleurs, …), portés tels quels. |

## Qui compose quoi

| Classe | Traits d'ingestion composés |
|---|---|
| `Company` (et ses déclinaisons) | `SetPostalAddressTrait`, `SetContactPointTrait`, propriétés additionnelles |
| `Person` (et ses déclinaisons) | `SetContactPointTrait`, propriétés additionnelles |
| `Place` / `CustomerSite` / `ProviderSite` | `SiteTrait` (l'agrégat complet) |
| `Provider` | + `SetProductProviderInfoTrait` |
| `Product` | propriétés additionnelles + l'arbre `eligibleQuantity` (voir [Produits](products.md)) |

---

## Ingestion et hydrateurs : deux moments différents

Les traits d'ingestion travaillent **à l'entrée**, quand la donnée arrive plate (clé à clé, via `__set`). Les [fonctions d'assistance `hydrate*`](helpers.md) travaillent **à la relecture**, quand la donnée revient déjà structurée mais non typée (un document complet à retyper d'un bloc). Les deux se complètent : l'ingestion écrit, l'hydratation relit.

---

## Voir aussi

- [Organisations](organizations.md), [Personnes](people.md), [Produits](products.md), [Lieux](places.md) — les classes qui composent ces traits.
- [Fonctions d'assistance](helpers.md) — la relecture typée des documents.
