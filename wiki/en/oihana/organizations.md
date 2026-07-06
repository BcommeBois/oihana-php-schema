# `xyz\oihana\schema\organizations` — Business entities

The `xyz\oihana\schema\organizations` namespace models the **business entities**: `Company`, the base corporation carrying the French administrative identifiers and the address + contact ingestion, and its four flavors — `Customer`, `Provider`, `Subsidiary` and `Affiliate`.

All of them extend the Schema.org `Corporation` through `Company`, and expose the house context `@context = 'https://schema.oihana.xyz'` in JSON-LD: the business type of a document is read from its `additionalType` (`…/Customer`, `…/Provider`, …).

> 🇫🇷 Cette page est aussi disponible en [français](../../fr/oihana/organizations.md).

---

## When to use it

Reach for these classes to represent an organization **in its commercial relationship** — not only its legal existence:

- a `Customer` for a client with its terms (credit status, payment terms, price segmentation, assignments);
- a `Provider` for a supplier with its logistics (carrier, carriage-paid threshold, minimum order value) and buying information;
- a `Subsidiary` or an `Affiliate` for the group's structures.

The base `Company` is enough when the organization has no determined commercial role yet.

---

## Quick example

```php
use xyz\oihana\schema\organizations\Customer;

$customer = new Customer
([
    'name'         => 'South Wood Company' ,
    'taxID'        => '84999999900012' ,        // French SIRET
    'naics'        => '4673A' ,                 // French APE code
    'creditStatus' => 'open' ,
    'paymentTerms' => '30D' ,
]);

// Flat dataset keys hydrate the nested objects (see the ingestion page):
$customer->addressLocality  = 'BORDEAUX' ;          // → $customer->address (PostalAddress)
$customer->defaultTelephone = '05 56 00 00 00' ;    // → $customer->contactPoint (ContactPoint[])
```

---

## The base: `Company`

`Company` extends the Schema.org `Corporation` and concentrates what every flavor shares:

| Property | Type | Role |
|---|---|---|
| `taxID` (inherited) | `string` | The French SIRET. |
| `naics` (inherited) | `string` | The French APE code. |
| `additionalProperty` | `PropertyValue[]` | The normalized additional properties. |
| `category` / `industry` / `invoiceType` | term reference | Classifications (resolved against a thesaurus by the consumer). |
| `deliveryMethod` | term reference | The default delivery method. |
| `freeShippingThreshold` | `float` | The carriage-paid threshold. |
| `status` | `int` | The applicative status. |
| `vat` | `TaxRate` or reference | The VAT regime. |
| `website` | `WebSite` or reference | The public website. |

`Company` composes the `SetPostalAddressTrait` and `SetContactPointTrait` ingestion traits (see [Ingestion](ingestion.md)): the flat keys `addressLocality`, `defaultTelephone`, `mobile`, … become typed `PostalAddress` and `ContactPoint` objects during hydration.

---

## The flavors

| Class | Extends | What it adds |
|---|---|---|
| `Customer` | `Company` | `assignedCompany` / `assignedPOS` / `assignedSeller` (the company, warehouse and seller assignments), `creditStatus`, `paymentTerms`, `priceSegmentation` (a `PriceSegmentation` reference), `unloadingMethod`, and its own additional-property normalization (`CustomerAdditionalProperty::normalize()`). |
| `Provider` | `Company` | `carrier`, `amountCarriagePaid`, `minimumOrderValue`, `hasAcknowledgmentOfReceipt`, `providerType`, `shareCapital`, and `productInfo` (`ProductProviderInfo`) fed by `SetProductProviderInfoTrait`. |
| `Subsidiary` | `Company` | The group subsidiary — the type is enough, the properties come from the base. |
| `Affiliate` | `Company` | The affiliated brand — same logic. |

The references (`assignedSeller`, `carrier`, `priceSegmentation`, …) are **resolved**: each accepts a hydrated object, a scalar reference (key), or a raw associative `array` — never a forced class.

---

## See also

- [Ingestion](ingestion.md) — the `__set` bridges hydrating the flat rows.
- [Oihana people](people.md) — the contacts of these organizations.
- [Oihana products](products.md) — `PriceSegmentation`, `TaxRate` and the commerce layer.
- [Helper functions](helpers.md) — `hydrateCustomer()` and its siblings.
