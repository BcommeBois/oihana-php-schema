# `xyz\oihana\schema\people` — Personnes

Le namespace `xyz\oihana\schema\people` modélise les **interlocuteurs** du monde commercial : la `Person` de base (qui étend la `Person` de Schema.org) et ses cinq déclinaisons typées — `Seller` (vendeur), `CustomerEmployee` (contact client), `Employee`, `ProviderEmployee` et `SubsidiaryEmployee`.

Une déclinaison ne porte **aucune donnée en plus** : elle fixe le type. Le `@type` JSON-LD et l'`additionalType` (`…/Seller`, `…/CustomerEmployee`) découlent du nom de la classe et du contexte maison `https://schema.oihana.xyz` — c'est ce type qui alimente les filtres (« lister les vendeurs ») et les identités métier.

> 🇬🇧 This page is also available in [English](../../en/oihana/people.md).

---

## Quand l'utiliser

Choisissez la déclinaison qui dit **le rôle de la personne vis-à-vis de l'organisation** :

- un `Seller` pour un commercial de la maison ;
- un `CustomerEmployee` pour un contact travaillant chez un client (son `worksFor` pointe l'organisation cliente) ;
- `Employee`, `ProviderEmployee`, `SubsidiaryEmployee` pour les autres rattachements.

La `Person` de base sert de socle et de type de repli quand le rôle n'est pas déterminé.

---

## Exemple express

```php
use xyz\oihana\schema\people\CustomerEmployee;

$contact = new CustomerEmployee
([
    'name'     => 'Jean Dupont' ,
    'worksFor' => [ '_key' => '137285125' ] ,   // l'organisation cliente
]);

// Les lignes plates hydratent les objets imbriqués (voir la page ingestion) :
$contact->mobile   = '06 00 00 00 00' ;         // → contactPoint (ContactPoint typé mobile)
$contact->civility = 'M.' ;                     // → additionalProperty (PropertyValue normalisée)

CustomerEmployee::getSchemaType() ;             // 'https://schema.oihana.xyz/CustomerEmployee'
```

---

## La base : `Person`

| Propriété | Type | Rôle |
|---|---|---|
| `additionalProperty` | `PropertyValue[]` | Les propriétés additionnelles, normalisées par `PersonAdditionalProperty::normalize()` (civilité, indicateurs booléens, …). |
| `ownedBy` | référence | L'organisation ou la personne propriétaire de la fiche. |
| `position` | `int` ou `string` | Le rang d'affichage. |

`Person` compose `SetContactPointTrait` (voir [Ingestion](ingestion.md)) : les clés plates `mobile`, `homeTelephone`, `defaultEmail`, … deviennent une collection de `ContactPoint` typés par usage.

---

## Les déclinaisons

| Classe | Type produit | Usage |
|---|---|---|
| `Seller` | `…/Seller` | Le commercial — le pivot [`sellerKey()`](helpers.md) résout son `_key` depuis un compte authentifié. |
| `CustomerEmployee` | `…/CustomerEmployee` | Le contact client — son `worksFor` porte l'organisation, que le pivot [`customerKey()`](helpers.md) remonte. |
| `Employee` | `…/Employee` | L'employé générique. |
| `ProviderEmployee` | `…/ProviderEmployee` | Le contact fournisseur. |
| `SubsidiaryEmployee` | `…/SubsidiaryEmployee` | L'employé de filiale. |

---

## Voir aussi

- [Métier Oihana](business.md) — `BusinessIdentity`, le lien compte authentifié ↔ personne.
- [Ingestion](ingestion.md) — les ponts `__set` qui hydratent les lignes plates.
- [Organisations Oihana](organizations.md) — les entités que ces personnes représentent.
