# `xyz\oihana\schema\people` — People

The `xyz\oihana\schema\people` namespace models the **contacts** of the commercial world: the base `Person` (extending the Schema.org `Person`) and its five typed flavors — `Seller`, `CustomerEmployee`, `Employee`, `ProviderEmployee` and `SubsidiaryEmployee`.

A flavor carries **no extra data**: it fixes the type. The JSON-LD `@type` and the `additionalType` (`…/Seller`, `…/CustomerEmployee`) derive from the class name and the house context `https://schema.oihana.xyz` — and that type is what feeds the filters ("list the sellers") and the business identities.

> 🇫🇷 Cette page est aussi disponible en [français](../../fr/oihana/people.md).

---

## When to use it

Pick the flavor that states **the person's role towards the organization**:

- a `Seller` for an in-house salesperson;
- a `CustomerEmployee` for a contact working at a client (its `worksFor` points to the customer organization);
- `Employee`, `ProviderEmployee`, `SubsidiaryEmployee` for the other attachments.

The base `Person` serves as the foundation and as the fallback type when the role is undetermined.

---

## Quick example

```php
use xyz\oihana\schema\people\CustomerEmployee;

$contact = new CustomerEmployee
([
    'name'     => 'Jean Dupont' ,
    'worksFor' => [ '_key' => '137285125' ] ,   // the customer organization
]);

// Flat keys hydrate the nested objects (see the ingestion page):
$contact->mobile   = '06 00 00 00 00' ;         // → contactPoint (mobile-typed ContactPoint)
$contact->civility = 'M.' ;                     // → additionalProperty (normalized PropertyValue)

CustomerEmployee::getSchemaType() ;             // 'https://schema.oihana.xyz/CustomerEmployee'
```

---

## The base: `Person`

| Property | Type | Role |
|---|---|---|
| `additionalProperty` | `PropertyValue[]` | The additional properties, normalized by `PersonAdditionalProperty::normalize()` (civility, boolean flags, …). |
| `ownedBy` | reference | The organization or person owning the record. |
| `position` | `int` or `string` | The display rank. |

`Person` composes `SetContactPointTrait` (see [Ingestion](ingestion.md)): the flat keys `mobile`, `homeTelephone`, `defaultEmail`, … become a collection of `ContactPoint` objects typed by usage.

---

## The flavors

| Class | Produced type | Usage |
|---|---|---|
| `Seller` | `…/Seller` | The salesperson — the [`sellerKey()`](helpers.md) pivot resolves its `_key` from an authenticated account. |
| `CustomerEmployee` | `…/CustomerEmployee` | The customer contact — its `worksFor` carries the organization, surfaced by the [`customerKey()`](helpers.md) pivot. |
| `Employee` | `…/Employee` | The generic employee. |
| `ProviderEmployee` | `…/ProviderEmployee` | The supplier contact. |
| `SubsidiaryEmployee` | `…/SubsidiaryEmployee` | The subsidiary employee. |

---

## See also

- [Oihana business](business.md) — `BusinessIdentity`, the authenticated account ↔ person link.
- [Ingestion](ingestion.md) — the `__set` bridges hydrating the flat rows.
- [Oihana organizations](organizations.md) — the entities these people represent.
