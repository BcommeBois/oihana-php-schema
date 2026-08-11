# Oihana PHP Schema library - Change Log

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) and this project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]

### Added

- Adds three hydration helpers, closing the last header slots of a business
  document that came back as raw arrays :
  `xyz\oihana\schema\helpers\hydrate\documents\hydrateDocumentTotals()`,
  `xyz\oihana\schema\helpers\hydrate\documents\hydrateAdjustment()` and
  `xyz\oihana\schema\helpers\hydrate\hydrateParcelDelivery()`.

  They answer one limit, the same one `hydrateDocumentLine()` was written for :
  a constructor **assigns flat**, so a class built that way never types anything
  nested. `DocumentTotals`, `Adjustment` and `TaxDetail` all descend from
  `Thing`, which is precisely what sends them down the constructor path — and
  leaves an amount as `[ 'value' => 62.4 , 'currency' => 'EUR' ]` where the class
  declares a `MonetaryAmount`.

  **`hydrateDocumentTotals()` and `hydrateAdjustment()` go through
  `Reflection::hydrate()`**, which honors the `#[HydrateAs]` / `#[HydrateWith]`
  attributes — and honors them **at every depth**, since it recurses into each
  nested class in its turn. One call on an adjustment therefore types its
  `amount`, its `taxes`, and the `basisAmount` / `taxAmount` each `TaxDetail`
  declares of its own. Nothing had to be added for `TaxDetail` or
  `MonetaryAmount`: the attributes were already there, only the path that reads
  them was missing.

  **`hydrateParcelDelivery()` takes the other route** — the constructor, then its
  references one by one — because reflection would bring nothing here :
  `ParcelDelivery` declares no attribute on `deliveryAddress`,
  `hasDeliveryMethod` or `hasDeliveryRoute`, so the three would stay raw while
  the strictness of reflection dropped whatever a stored delivery carries beyond
  the Schema.org names. It resolves both addresses through
  `hydratePostalAddress()`, the two travel terms through `hydrateDefinedTerm()`,
  and the carrier through `hydrateOrganizationOrPerson()` — the `Organization|Person`
  union a property type cannot settle alone.

  `hydrateAdjustment()` is born with one rule of its own : an **empty list is
  kept as an empty list**, where the rest of the family answers `null`. « This
  document has no adjustment » is an answer worth serving, and a consumer
  mapping over the value deserves the empty list it can map over. The rule stops
  at the top-level list handed to the helper — a non-empty list that hydrates to
  nothing still answers `null`, « nothing here was readable » being a different
  statement from « there is nothing here ».

  🔑 **The two travel terms are parameters, not hard-wired classes**
  (`class-string<DefinedTerm>`, defaulting to `DeliveryMethodTerm` and
  `DeliveryRouteTerm`), and that is why the helper lives under `xyz\oihana`
  while `ParcelDelivery` stays plain `org\schema`. Declaring the thesaurus
  classes as attributes on the Schema.org class would have made the lower layer
  depend on the upper one; a parameter keeps the dependency pointing the right
  way, and lets any `DefinedTerm` subclass take their place.

- Adds `xyz\oihana\schema\business\documents\BusinessDocument::$assignedSeller`
  (`null|int|string|array|Person`), the salesperson a document is assigned to —
  who carries the deal, as opposed to the `seller` who issues it. The two were
  conflated until now, and the conflation had a cost : one organization issues
  every document of a sales cycle, so `seller` cannot answer « whose quotes are
  these ». That question is the one a salesperson's own resources are scoped by,
  through `sellerKey()` — the same pivot `Customer::$assignedSeller` already
  serves on the customer side.

  The name, the shape and the meaning are taken from
  `xyz\oihana\schema\organizations\Customer::$assignedSeller` unchanged, because
  a document commonly gets its own from the customer's — and then keeps it : a
  customer reassigned next quarter must not rewrite who took last quarter's
  order, which is exactly why the value is carried on the document rather than
  read back through the customer.

  No `#[HydrateAs]` is declared, and none is needed : the union names a single
  class, so `Reflection::hydrate()` resolves a joined row into a `Person` on its
  own — and each entry of a joined list, the plural the property name announces
  — while a bare key (string or integer) is left as read. The constructor path
  keeps assigning raw, as everywhere else. `ASSIGNED_SELLER` joins
  `BusinessDocumentTrait`, whose constants are now explicitly `public` like
  every other constants trait, and the suite covers the constant (including its
  agreement with `Oihana::ASSIGNED_SELLER`), the default, the three shapes on
  the constructor path, the single/list/bare-key resolution on the reflection
  path, and the coexistence with the `seller` it must not replace.

- Adds `xyz\oihana\schema\business\documents\BusinessDocumentLine::$freeReason`
  (`null|array|DefinedTerm`, `#[HydrateAs(DefinedTerm::class)]`), why the goods
  on a line leave without being invoiced — a gift, a breakage, a sample, goods
  that were the customer's to begin with. Its presence is what says the line is
  offered : there is deliberately no boolean beside it, since an ERP carrying
  both a flag and a reason has been observed with the two drifting apart, and a
  line claiming to be a gift while still charging money is the one thing the
  property exists to prevent. The term is designated by its code and its label
  (`id` + `name`), never by its storage key — a controlled vocabulary
  re-harvested into a fresh collection is renumbered, and a line pointing at a
  key would silently designate another term. The shape reuses
  `PricingCondition::$category` exactly, so a consumer reads a resolved term the
  same way in both places : a joined row hydrates into a `DefinedTerm` through
  `Reflection::hydrate`, a raw array passed to the constructor stays as read.

- Adds `xyz\oihana\schema\business\documents\BusinessDocumentLine::$technicalNote`
  (`?string`), a note meant for whoever prepares the goods, never for the
  customer. It is the sibling of the inherited `description`, which is what the
  customer reads on the document : « reprendre les 3 colis palette 12, ne pas
  remettre en stock » belongs on the picking slip and nowhere else, and keeping
  the two apart is what lets a document be printed twice, for two audiences,
  from a single line. Note that the property serializes like any other — a
  renderer addressing the customer is what must leave it out, the type does not
  do it on its own.

  `FREE_REASON` and `TECHNICAL_NOTE` join `BusinessDocumentLineTrait`, and the
  suite covers the constants, the defaults, the constructor path of both, the
  Reflection hydration of the reason into a `DefinedTerm`, and the coexistence
  of the technical note with the `description` it must not replace.

- Adds delivery routes — the recurring circuits an own-fleet vehicle runs — as a
  new `xyz\oihana\schema\shipping` namespace plus one thesaurus term, filling a
  gap the library only half covered : `DeliveryMethodTerm` said *how* an order
  travels and what that costs, but nothing said *on which passage* it travels,
  nor which addresses a given circuit serves. The two are orthogonal — a dozen
  routes may all be run under the single "own fleet" method, and a route carries
  no charge of its own.

  `xyz\oihana\schema\thesaurus\DeliveryRouteTerm` (extends `ThesaurusTerm`)
  describes the circuit itself : `byDay`, the days it is on the road, in the
  `DayOfWeek` vocabulary `Schedule::$byDay` already uses — an empty list means a
  route defined but not yet scheduled, which is not the `null` of a route whose
  days were never read — and `assignedPOS`, the warehouse it departs from,
  reusing the property name, the shape and the capitalized spelling
  `Customer::$assignedPOS` carries for the point of sale a customer is attached
  to. A joined row resolves into a `Warehouse`, a bare reference stays as read.

  `xyz\oihana\schema\shipping\DeliveryRouteAssignment` (extends
  `StructuredValue`) is the other half : the pairing of a route with one
  address, carrying what only that pairing knows — `route` (the reference,
  resolved into a `DeliveryRouteTerm` once joined), `byDay` (the days the route
  serves *this* address, always a subset of the days it runs), `position` (the
  order of passage), `startTime` / `endTime` (`HH:MM` bounds, independent of one
  another) and `weekFrom` / `weekThrough` (ISO week numbers, for a stop that
  only exists part of the year — deliberately not named `validFrom` /
  `validThrough`, which carry dates everywhere else in the library and would
  make the type a lie).

  The two ends are wired : `Site::$deliveryRoute` lists the assignments serving
  an address — a list, since a same address is commonly visited by more than one
  circuit — hydrated element by element through `#[HydrateWith]`, and
  `ParcelDelivery::$hasDeliveryRoute` names the route a delivery travels on,
  mirroring the `hasDeliveryMethod` it sits next to. Nothing anywhere copies a
  route's label : an assignment holds the bare reference, so renaming a circuit
  is a single write in the thesaurus. A business document is the deliberate
  exception, freezing what it names so a quote keeps saying what was chosen.

  New hydrator `hydrateDeliveryRouteAssignment()`, called by
  `hydrateCustomerSite()`, turns stored rows into assignments and resolves each
  nested route when the joined reference row is present — a bare code is left
  alone, since building a term out of a string would claim a label nobody read.
  Constants join `DeliveryRouteTermTrait` and the new
  `constants/traits/shipping/DeliveryRouteAssignment`, aggregated through
  `ThesaurusTrait` and `ShippingTrait` into `Oihana`; the six values shared with
  other entities are redeclared identically (the house pattern — a divergent
  value would be fatal at class load), and the suite covers that composition
  explicitly, since nothing else would catch it.

- Composes `xyz\oihana\schema\traits\HasColor` on
  `xyz\oihana\schema\business\documents\BusinessDocumentLine`, so a line can
  carry the same optional `#RRGGBB` house color the thesaurus families and
  `ProductType` already carry. A document line is what an interface actually
  draws — a quote grid, a picking list, an order preview — and until now the
  only way to tint one was to walk back to the item, then to its type, hoping
  something was set there ; a line highlighted for its own reason (an
  out-of-stock item, a renegotiated price, a block being assembled) had nowhere
  to say so. The `COLOR` constant joins `BusinessDocumentLineTrait` with the
  same `'color'` value the other declaring traits use, and the suite covers the
  constant, the default and the constructor path.

- Adds `xyz\oihana\schema\business\documents\Adjustment::$taxes`
  (`null|array|TaxDetail`, `#[HydrateWith(TaxDetail::class)]`), the tax an
  adjustment owes on its own account. A charge is rarely tax-free, and its rate
  is not necessarily the rate of what it accompanies : a shipping fee answers to
  the carrier, an environmental contribution to its own schedule. Until now the
  only place such a tax could land was a document-level total, which said how
  much was owed without ever saying what had produced it — a reader adding up
  the lines found a remainder no field explained. The property reuses the shape
  of `BusinessDocumentLine::$taxes` (one `TaxDetail` per rate, each carrying its
  basis and its amount), so a consumer reads the tax of a charge with the code
  that already reads the tax of a line. The `TAXES` constant joins
  `AdjustmentTrait` — an identical redeclaration of the value already composed
  into `DocumentsTrait` by `BusinessDocumentLineTrait` and
  `BusinessDocumentTrait`, hence conflict-free — and the suite covers the
  constant, the default and the `Reflection::hydrate()` conversion into
  `TaxDetail` objects.

- Adds `xyz\oihana\schema\enumerations\PriceType::SELLING_UNIT_PRICE`
  (`https://schema.oihana.xyz/SellingUnitPrice`), the real selling price
  expressed per unit of sale — the tariff price converted into the unit the
  customer actually buys. It completes the house pricing ladder next to
  `SELLING_REFERENCE` (the T4 reference price) and `SELLING_FORCED`. The class,
  which had no suite of its own, is now covered by a new `PriceTypeTest`
  (the `PriceTypeEnumeration` lineage, the inherited schema.org constants, the
  seven house constants, the new one and `includes()`).

- Adds `xyz\oihana\schema\business\documents\BusinessDocumentLine::$additionalProperty`
  (`null|array|PropertyValue`, `#[HydrateWith(PropertyValue::class)]`), the
  schema.org extension point the line was missing while the other commerce
  types of the library (`Person`, `Company`, `PricingCondition`, `Product`…)
  already carried it — a line can now say what no dedicated property covers
  (a lot number, a serial number, an ERP-specific line flag) as
  `PropertyValue` pairs, readable through `GetAdditionalPropertyTrait`
  consumers. The `ADDITIONAL_PROPERTY` constant joins
  `BusinessDocumentLineTrait` (an identical redeclaration of the value already
  composed into `Oihana`, hence conflict-free), and the test suite covers the
  constant, the default, the raw-array constructor path and the
  `Reflection::hydrate()` conversion into `PropertyValue` objects.

- Adds static analysis to the project : `phpstan/phpstan` as a dev dependency,
  a `phpstan.neon` covering `src`, and a `composer phpstan` script. The level is
  set to **5** and is meant as a non-regression floor, raised one batch at a time
  as the reported errors get fixed. Level 6 is deliberately skipped for now :
  96% of what it adds is a single identifier (`missingType.iterableValue`, ~730
  hits across 161 files), which asks every `array` member of a union to document
  its value type — a typing convention this library never adopted, and not a list
  of defects. For the record, the error count per level on `src` at the time of
  writing : 1 (level 0), 6, 9, 10, 29, 29 (level 5), 756 (level 6), 788, 788,
  825, 944 (level 10). The CI workflow is left untouched and still runs the test
  suite only.

- Adds `org\schema\ParcelDelivery::$requestedDeliveryDate`
  (`null|string|int`), the date the *customer asks* the parcel to be delivered
  on. It says something `expectedArrivalFrom` / `expectedArrivalUntil` do not :
  those bound the window the carrier says the parcel *may* arrive in, and stay
  free to express it later, once the carrier has answered. The property fills a
  hole `BusinessDocument::$orderDelivery` already promised in its docblock
  ("the shipping address, the delivery method and the requested date") without
  anything backing it.
- Adds `org\schema\constants\traits\ParcelDelivery`, the property-name trait the
  type was missing while all its neighbours had one — consumers had nothing but
  hard-coded strings for `deliveryAddress`, `hasDeliveryMethod`,
  `trackingNumber`… The twelve constants are aggregated into
  `org\schema\constants\traits\Properties`, hence reachable from
  `org\schema\constants\Schema`. `PROVIDER` is redeclared with its existing
  value (`'provider'`), as the seven other traits of the aggregator already
  declaring it do — identical redeclarations compose without conflict.
- Adds `xyz\oihana\schema\places\CustomPostalAddress`, a `PostalAddress`
  subclass whose sole member is `CONTEXT = Oihana::SCHEMA`, so
  `getSchemaType()` yields `https://schema.oihana.xyz/CustomPostalAddress`. A
  delivery address is not always picked from an address book : it can be
  dictated on the spot, for a place no record describes. This type lets a frozen
  copy *say* so in its `additionalType`, instead of leaving it to be inferred
  from the absence of a reference key — an inference that flips the day such
  addresses do get recorded. It exists to name a type, never to hydrate into,
  and appears in no property union.

- Adds `addressDepartment` to `org\schema\traits\PostalAddressTrait` — the
  French second-level administrative division, between the locality and the
  region. Not a Schema.org term (the `ADDRESS_DEPARTMENT` /
  `FULL_ADDRESS_DEPARTMENT` constants already existed on
  `org\schema\constants\traits\PostalAddress`, unused, ahead of this
  property) : a deliberate, documented exception to the library's usual
  `org\schema` = pure Schema.org mirror rule, kept simple rather than
  introducing a house `PostalAddress` subclass just to carry one ERP-sourced
  field. Covered by a new `PostalAddressTest` (defaults, constructor, direct
  assignment, property-name constants) and a new
  `HydratePostalAddressTest::testHydratesAddressDepartment` case.

- Adds `org\schema\traits\helpers\GetAdditionalPropertyTrait`, the read
  counterpart of `SetAdditionalPropertyTrait` : `getAdditionalPropertyValue()`
  looks up a `propertyID` in the thing's `additionalProperty` list and returns
  its `value` (or `null` when absent), and `hasAdditionalPropertyFlag()` wraps
  it with `filter_var( … , FILTER_VALIDATE_BOOLEAN )` so a flag reads the same
  whether the stored value is a real boolean, the string `"1"` or the number
  `1`. Both the hydrated `PropertyValue` objects and the plain arrays a
  document comes back as when nothing re-hydrated it are honoured. Built on
  top of it, two new domain traits answer the flag questions asked throughout
  the business-documents code by name instead of by raw `additionalProperty`
  lookup : `xyz\oihana\schema\traits\people\EmployeeFlagsTrait`
  (`isDeliveryNoteRecipient()`, `isDocumentRecipient()`,
  `isInvoiceRecipient()`, `isOrderRecipient()`, `isQuoteRecipient()`,
  `showsApplications()`, backed by `PersonAdditionalProperty`) and
  `xyz\oihana\schema\traits\places\SiteFlagsTrait` (`isBillingAddress()`,
  `isConstructionSite()`, `isDefaultAddress()`, `isDeliveryAddress()`,
  `isShippingAddress()`, backed by `Oihana`'s site constants). `Person` now
  composes `GetAdditionalPropertyTrait`, `CustomerEmployee` composes
  `EmployeeFlagsTrait` directly, and `CustomerSite`/`ProviderSite` compose
  `SiteFlagsTrait`. Covered by three new suites
  (`GetAdditionalPropertyTraitTest`, `EmployeeFlagsTraitTest`,
  `SiteFlagsTraitTest`), including the tolerant-flag data provider and the
  case of a site claiming none of the flags.
- Adds `xyz\oihana\schema\thesaurus\DeliveryMethodTerm`, a `ThesaurusTerm`
  carrying what a delivery method costs : `shippingRate` (the flat carriage),
  `freeShippingThreshold` (the order value above which carriage is free, `null`
  meaning never free) and `vat` (raw tax code, or the resolved `TaxRate` once
  joined). The two amounts are scalars rather than `MonetaryAmount` instances so
  a term hydrates directly from a flat table row. Property names come from the
  new `DeliveryMethodTermTrait`, aggregated into `ThesaurusTrait`.
- Adds `xyz\oihana\schema\enumerations\ShippingChargeTiming` (`AT_ORDER`,
  `AT_DELIVERY`) and backs it with a new `DeliveryMethodTerm::$chargeTiming`
  property (`CHARGE_TIMING` in `DeliveryMethodTermTrait`), so a delivery method
  can say when its carriage amount is locked in, alongside what it costs.
  `null` leaves the timing unspecified. Covered by a new
  `ShippingChargeTimingTest` and new `DeliveryMethodTermTest` cases (defaults,
  assignment, constructor and reflection hydration paths).
- Adds `org\schema\helpers\hydrate\hydrateOrganizationOrPerson()` and
  `xyz\oihana\schema\helpers\hydrate\documents\hydrateBusinessDocument()`, the
  helper-layer counterparts of the `#[HydrateWith]` fix below.
  `hydrateOrganizationOrPerson()` resolves an `Organization|Person` payload from
  its JSON-LD `@type` (`Person` → `Person`, anything else — `Organization` itself
  or one of its many subtypes — → `Organization`, the safe default when `@type`
  is absent), and accepts optional `$organizationClass`/`$personClass` overrides
  so a caller can pin the result to a business subtype (e.g. `Customer`,
  `CustomerEmployee`) instead of the plain Schema.org class.
  `hydrateBusinessDocument()` hydrates a whole document through
  `Reflection::hydrate()` and takes an optional `$class` parameter (default
  `BusinessDocument::class`) so the same function serves every subclass
  (`Quote`, `PurchaseOrder`, `Invoice`, `CreditNote`, `DebitNote`,
  `DeliveryNote`, `GoodsReceiptConfirmation`, `Receipt`, `RemittanceAdvice`,
  `Statement`). Since the attributes now settle the unions on their own, what
  these two add over a direct `Reflection::hydrate()` call is the
  single/indexed-list/passthrough input handling shared by the whole helpers
  layer. Both are registered in `autoload.files` and covered by two new suites
  (`HydrateOrganizationOrPersonTest`, `HydrateBusinessDocumentTest` — 16 tests).
- Adds the `xyz\oihana\schema\helpers\hydrate\documents` namespace — the first
  `documents` sub-layer of the hydration helpers — with `hydrateDocumentLine()`
  and `hydrateDocumentLineItem()`. A `BusinessDocument` served by an API carries
  its `documentLines` as raw arrays : the constructor's shallow assignment leaves
  them that way, so a line's `price`, `quantity`, `subtotal`, `taxes`,
  `adjustments` and `total` never become objects. `hydrateDocumentLine()` handles
  the three usual shapes (single line / indexed list of lines / passthrough) and
  builds each line through `Reflection::hydrate()` rather than the constructor —
  the only path that honors the `#[HydrateAs]` / `#[HydrateWith]` attributes
  already declared on `BusinessDocumentLine`, so nothing about the mapping is
  duplicated in the helper (the reflection instance is kept `static`, so a whole
  document costs one hydration plan, not one per line). `item` is the exception :
  its `Product|Service` union cannot be resolved from the property type alone
  (reflection would always pick `Product`, even for a service), so
  `hydrateDocumentLineItem()` reads the payload's JSON-LD `@type` — a type ending
  with `Service` gives an `org\schema\Service`, anything else the
  commerce-enriched `xyz\oihana\schema\products\Product` (itself an
  `org\schema\Product`, so the line's declared type still holds), with its own
  `eligibleQuantity` / `inventoryLevel` references hydrated. Both functions are
  registered in `autoload.files` and covered by two new suites
  (`HydrateDocumentLineTest`, `HydrateDocumentLineItemTest` — 15 tests) spanning
  the nested references, the `@type` resolution and its fallbacks, the indexed
  list, the already-hydrated input and the passthrough.
- Adds `org\schema\creativeWork\Credential` (extends `CreativeWork`) — the
  schema.org parent type of `EducationalOccupationalCredential`, "a certificate
  that is used to verify the identity of a person or entity". It carries the four
  properties schema.org defines on `Credential` : `credentialCategory`
  (`null|string|array|DefinedTerm`), `recognizedBy`
  (`null|string|array|Organization`), `validFor`
  (`null|string|array|int|float|Duration`) and `validIn`
  (`null|string|array|AdministrativeArea`) — each including `array` so raw,
  pre-hydration payloads survive the constructor's shallow assignment. The
  matching `org\schema\constants\traits\Credential` constants trait
  (`CREDENTIAL_CATEGORY`, `RECOGNIZED_BY`, `VALID_FOR`, `VALID_IN`) is aggregated
  into `Schema`/`Prop` through `Properties`, and composed by the
  `EducationalOccupationCredential` trait so the educational subtype keeps
  exposing them. Covered by the new `CredentialTest` and
  `EducationalOccupationalCredentialTest` suites (defaults, lineage, schema type,
  constant aggregation, scalar/structured/array typing, both hydration paths and
  `jsonSerialize`).
- Adds the `xyz\oihana\schema\enumerations\BusinessDocumentDirection` enumeration
  (`Sale` / `Purchase`) and two header properties to `BusinessDocument` under
  `xyz\oihana\schema\business\documents` — `direction`
  (`null|string|BusinessDocumentDirection`), the commercial direction of the
  document from the operator's point of view (which of `seller` / `customer` is
  the operator's own organization), orthogonal to the document's type and to its
  `BusinessDocumentStatus` lifecycle ; and `author`
  (`null|Organization|Person|array`), the party who authored the document,
  reusing the Schema.org `author` name. Their constants (`DIRECTION`, `AUTHOR`)
  are added to `BusinessDocumentTrait`. Both properties default to `null` and the
  class stays covered at 100%, with a new `BusinessDocumentDirectionTest` suite
  (lineage, constants and `includes`) and the `BusinessDocument` suite extended
  for the two new constants, defaults and hydration.
- Adds `xyz\oihana\schema\thesaurus\ProductPriceCategoryTerm` (extends `Concept`,
  `use HasColor`, `HasTreeMetrics`) — a product **price** category (a tariff
  family used to scope pricing rules and conditions), the second hierarchical
  and colored thesaurus family alongside `ProductCategoryTerm`. It is kept as its
  own class rather than reusing `ProductCategoryTerm` so a term's `@type` stays
  meaningful : a price category never advertises itself as a catalog category.
  It shares the exact same shape — the SKOS `broader`/`narrower` relations and
  `@context` from `Concept`, the `color` from `HasColor`, and the projection-only
  `childrenCount` from `HasTreeMetrics` (`isLeaf` ⟺ `childrenCount === 0`).
  Covered at 100% by the new `ProductPriceCategoryTermTest` suite (defaults, the
  `Concept`/`DefinedTerm` lineage, the `HasColor`/`HasTreeMetrics` composition,
  the relation and `CHILDREN_COUNT` constants and their `Oihana` aggregation, the
  distinction from `ProductCategoryTerm`, and both hydration paths — raw
  constructor assignment vs the reflection path).
- Adds six resolved, display-only target properties to `PricingCondition` under
  `xyz\oihana\schema\products` — `category` (`DefinedTerm`), `customer`
  (`Customer`), `product` (`Product`), `provider` (`Provider`), `subsidiary`
  (`Subsidiary`) and `warehouse` (`Warehouse`). Each is the hydrated twin of an
  id the resolver already reads off the `selector` (`itemId` for
  `category`/`product`, `customerId` for `customer`, `providerId` for `provider`,
  `areaServed` for `subsidiary`/`warehouse`): the resolver never reads them, they
  exist only to display the entity a condition targets. All six are single value
  objects typed `null|array|X`, hydrated through `#[HydrateAs]` and defaulting to
  `null`. Their constants are added to the products `PricingCondition` trait,
  surfacing `category` / `customer` / `product` / `provider` / `subsidiary` /
  `warehouse` on the `Oihana` aggregator (`category`, `customer`, `product` and
  `provider` were already carried, with the same value, by other product traits),
  and the class stays covered at 100%.
- Adds four header properties to `BusinessDocument` under
  `xyz\oihana\schema\business\documents` — `billingAddress` (`PostalAddress`),
  `contact` (`Person`, the interlocutor a document is dealt with — a concept
  absent from Schema.org's commercial documents), `orderDelivery`
  (`ParcelDelivery`, the delivery address / method / requested date, reusing the
  name already used by `DeliveryNote`) and `pointOfSale` (`Place`, the outlet
  the document is bound to). All four are single value objects hydrated through
  `#[HydrateAs]` and meant to be stored as frozen copies so a document stays
  self-contained. Their constants are added to `BusinessDocumentTrait`, surfacing
  `billingAddress` / `contact` / `orderDelivery` / `pointOfSale` on the `Oihana`
  aggregator (`orderDelivery` was already carried, with the same value, by
  `DeliveryNoteTrait`), and the `business-documents` wiki page is updated (FR + EN).
- Adds the `PricingAreaScope` enumeration under
  `xyz\oihana\schema\enumerations` — the granularity of the place a
  `PricingCondition` is valid at, resolved most-specific-first: `WAREHOUSE`
  (one point of sale) outranks `COMPANY` (every point of sale of a company),
  which outranks `GROUP`, which outranks the catch-all `ALL`. The
  `PricingConditionSelector` gains a matching `areaScope` property (typed
  `null|string|PricingAreaScope`) telling the resolver *what kind* of place its
  `areaServed` carries, and `areaServed` is widened from `?string` to
  `null|int|string|Place|GeoShape|AdministrativeArea|array` so a condition can
  be scoped to a raw id, a `Place`, a `GeoShape` or an `AdministrativeArea`. Its
  property-constant trait surfaces `areaScope` on the `Oihana` aggregator, and
  both the enumeration and the selector are covered at 100%.
- `hydrateWarehouse()` now hydrates the `address` property of a `Warehouse`:
  when the incoming `address` is a plain array (and not already a
  `PostalAddress`), it is passed through `hydratePostalAddress()`, mirroring the
  existing `ownedBy` → `Subsidiary` handling. Covered by a new
  `HydrateWarehouseTest::testHydratesThePostalAddress` case.
- Adds the `customerKeys()` account pivot under
  `xyz\oihana\schema\helpers\pivots` — the plural counterpart of `customerKey()`:
  it resolves every `CustomerEmployee` identity of an authenticated account and
  returns the deduplicated `_key` list of the customer organizations it is a
  contact for (`worksFor`), for scoping the resources of the customer(s) an
  account represents. Registered in the autoload `files` and documented in the
  `helpers` wiki page (FR + EN).
- Adds `CustomerOffer` under `xyz\oihana\schema\products` — a sell offer priced
  for one specific customer, specializing `OfferForPurchase`. It reuses the whole
  inherited pricing surface (`price`, `priceCurrency`, `priceSpecification`,
  `eligibleCustomerType`, `availableAtOrFrom`, `validFrom` / `validThrough`,
  `seller`) and adds two own properties: `customer` (a lightweight reference to
  the beneficiary, hydrated as `Customer`) and `appliedCondition` (the
  `PricingCondition` that produced the price, hydrated through `#[HydrateAs]`,
  `null` when the base tariff applies as-is). Its property-constant trait is
  wired into `ProductsTrait`, surfacing `customer` / `appliedCondition` on the
  `Oihana` aggregator, and it is documented in the `products` wiki page (FR + EN).
- Adds the `SELLING_MARGIN` price component type under
  `xyz\oihana\schema\enumerations\PriceComponentType`
  (`https://schema.oihana.xyz/SellingMargin`) — the selling margin (selling
  price minus cost price), carried as a `priceComponent` of a compound price
  specification on the sell side.
- Adds `PricingCondition` and its `PricingConditionSelector`, under
  `xyz\oihana\schema\products` — a conditional pricing rule: a discount (or a
  tariff substitution) granted to a scoped set of buyers on a scoped set of
  items, valid over a period. It is the sell-side twin of a provider buying
  condition, resolved most-specific-first for a given (customer, item, place)
  context.
  - `PricingConditionSelector` (a `StructuredValue`) carries the scope on three
    axes: the buyer (`customerScope` + `customerId`), the item (`itemScope` +
    `itemId`, refined by `categoryLevel` for hierarchical categories) and the
    place (`areaServed`). A `providerId` further restricts the condition to a
    targeted provider.
  - `PricingCondition` (a `StructuredValue`) carries at most one of three
    mutually exclusive effects — a list of stacked `adjustment` (`Adjustment`)
    applied in order, a `substitutesSegment` (`PriceSegmentation`, applied
    instead of a discount) or a `fixedPrice` (`MonetaryAmount`) imposing a fixed
    net price — plus a `free` flag marking the item as granted free of charge,
    `excludedCustomers` / `excludedProducts` exceptions, a `validFrom` /
    `validThrough` window, and an optional `quantityDiscount`
    (`PriceQuantityDiscount`). It also carries an `additionalProperty` for
    not-yet-modelled properties, hydrated as `PropertyValue` instances through
    `#[HydrateWith]`. `adjustment` is now **always a list** — hydrated element
    by element through `#[HydrateWith]` — so even a single adjustment must be
    wrapped in an array; the remaining nested effects and the selector hydrate
    through `#[HydrateAs]`.
- Adds the `PricingTargetScope` (`INDIVIDUAL` / `GROUP` / `COMPANY` / `ALL`) and
  `PricingItemScope` (`PRODUCT` / `CATEGORY` / `PROVIDER` / `ALL`) enumerations
  under `xyz\oihana\schema\enumerations`, driving the two resolution axes of a
  `PricingCondition`.
- Wires the two new property-constant traits into `ProductsTrait` (so the
  constants surface on the `Oihana` aggregator) and documents the whole in the
  `products` wiki page (FR + EN).
- Adds the `HasPricingMarkup` trait under `xyz\oihana\schema\traits` — an
  optional `pricingMarkup` guardrail carried as a `null|array|QuantitativeValue`
  (`minValue` floor, `maxValue` ceiling, `value` target, `unitCode` and
  `valueReference` naming the base, e.g. `PriceType::COGS`), hydrated through
  `#[HydrateAs]`. It models a commercial bound on the markup a seller may quote,
  never a price effect (those stay on `PricingCondition`). The trait is not yet
  composed by any class — it is provided for opt-in use, mirroring `HasColor`.

- Adds the house display `color` to `xyz\oihana\schema\products\ProductType`,
  through the shared `HasColor` trait — the same `#RRGGBB` presentation hint the
  thesaurus families already carry, so a product type can be tinted in a user
  interface like any other classification term. `ProductType` stays a flat
  `DefinedTerm` (no SKOS hierarchy, unlike `ProductCategoryTerm`) : only the
  color is borrowed, not the thesaurus shape. The matching `COLOR` constant joins
  `STOCKABLE` and `TRACKABLE` in the `constants\traits\products\ProductType`
  trait, hence in `Oihana` — its `'color'` value matches the other `COLOR` keys
  already aggregated there, so the trait constants stay compatible. The class,
  which had no suite of its own, is now covered by a new `ProductTypeTest`
  (defaults, the `DefinedTerm` lineage, `@context`, the `HasColor` composition,
  the constant aggregation into `Oihana`, both hydration paths and
  `jsonSerialize` with and without a color).

### Changed

- Replaces `cacheResult="false"` with `recordTestRunHistory="false"` in
  `phpunit.xml` : the former is deprecated and removed in PHPUnit 14. The
  attribute is set rather than dropped, because PHPUnit defaults it to `true`
  when neither attribute is present — deleting the line would have turned the
  run history on instead of preserving the current behavior. `false` is the
  value that matches this suite : it declares no `executionOrder`, so nothing
  reads the history it would write. The run is back to a clean
  `OK (1799 tests, 6118 assertions)`.

- **Behavior change.** The hydration helpers that resolve nested references now
  write the nested helper's answer as is, `null` included. Where the payload
  held an array that resolves to nothing — an empty list, a list of unhydratable
  entries — the property was left holding that raw array; it now holds `null`.
  Affects `hydrateCustomer`, `hydrateCustomerEmployee`, `hydrateCustomerSite`,
  `hydrateDeliveryRouteAssignment`, `hydrateBusinessDocument` and
  `hydrateDocumentLine`, across every nested reference they resolve.

  A consumer that received `[]` will now receive `null`. Concretely, a site
  served by no delivery route used to answer `deliveryRoute => []` when read
  through `hydrateCustomerSite()` but `null` when read through
  `hydrateDeliveryRouteAssignment()` directly — the same fact in two shapes,
  depending on the path taken. Code that iterated the result without a guard was
  working by accident on one path and broken on the other; `null` is now the
  single answer both paths give.

  The cause was a `if ( $result !== null )` guard wrapping each assignment. It
  never protected a well-formed value — no nested helper returns `null` on data
  it can hydrate — so its only effect was to restore the raw array the helper
  had just refused. The guard is now on the input instead: a nested reference is
  hydrated only when the raw value is an array, which also means a string
  reference or an already typed instance is no longer passed through the helper
  at all, and a property the payload never carried is not materialized to
  `null`. This is the idiom `hydrateWarehouse`, `hydrateStockLevel` and
  `hydrateAggregateOffer` already used.

- Renames `org\schema\enumerations\PriceTypeEnumeration::MinimumAdvertisedPrice`
  to `MINIMUM_ADVERTISED_PRICE`, the last camel-cased constant of the class,
  now aligned on the SCREAMING_SNAKE_CASE convention every other enumeration
  constant of the library follows. The value is unchanged and nothing in `src`
  or `tests` referenced the old name. The constants are also reordered
  alphabetically (`SALE_PRICE` before `SRP`).

- `org\schema\ParcelDelivery::$deliveryAddress` widens from
  `null|string|PostalAddress` to `null|array|string|PostalAddress`. A base read
  hands back an associative array and `Thing`'s constructor assigns values
  as-is, so a raw array on the former union raised a `TypeError`. Its direct
  counterpart, `BusinessDocument::$billingAddress`, was already
  `null|array|PostalAddress` — the two now agree.
- `org\schema\ParcelDelivery::$hasDeliveryMethod` widens from
  `null|array|DeliveryMethod` to
  `null|array|string|DeliveryMethod|DefinedTerm`. `DeliveryMethod` is the
  schema.org enumeration — eight goodrelations URLs, no property — whereas a
  back office keeps its own priced list of methods. `DefinedTerm` covers those
  by inheritance (`DeliveryMethodTerm` → `ThesaurusTerm` → `DefinedTerm`)
  without `org\schema` ever depending on `xyz\oihana`, which is exactly the
  union `Site::$deliveryMethod` and `Company::$deliveryMethod` already declare.
- `org\schema\ParcelDelivery`'s three other structured properties take a raw
  array as well, for the reason that drove `deliveryAddress` :
  `$originAddress` becomes `null|string|array|PostalAddress`, `$partOfOrder`
  becomes `null|array|Order` and `$provider` becomes
  `null|array|Organization|Person`. The whole type now survives a plain base
  read, not just its delivery address.
- `org\schema\ParcelDelivery::$provider` carries
  `#[HydrateWith(Organization::class, Person::class)]`. Its `Organization|Person`
  union cannot be resolved from the property type alone — `Reflection::hydrate`
  walks the union in declaration order and takes its first class member, so a
  `Person` payload came out an `Organization`. The attribute pins the candidates,
  as `BusinessDocument::$author` and `$customer` already do. Worth stating
  plainly, because the attribute promises less than it looks : it decides from
  the payload's JSON-LD discriminator (`@type` / `atType` / `type`) and nothing
  else. Its second strategy — guessing from the properties present — scores a
  candidate on *the share of its own properties* found in the payload, against a
  0.3 threshold ; with 91 properties on `Person` and 99 on `Organization`, no
  realistic payload comes close, so an undiscriminated one still falls back to
  the first candidate. That default is deliberate and safe (`Organization`), and
  the library's own `jsonSerialize()` emits `@type`, so every round-trip through
  it does resolve.
- `hydrateBusinessDocument()` re-resolves `orderDelivery.provider` — the carrier —
  through `hydrateOrganizationOrPerson()`, the same treatment `customer`,
  `seller`, `author` and the `Invoice` pair already get. The document's
  `ParcelDelivery` is built by `#[HydrateAs]`, so nothing inside it was ever
  re-resolved from the raw payload. This overlaps with the attribute above and
  changes no observable outcome today ; it is kept because it does not depend on
  an attribute declared on a schema.org mirror class, and because the helper's
  `$organizationClass` / `$personClass` parameters are the only way to aim at a
  house subtype (`Customer`, `CustomerEmployee`) later on.

- `org\schema\helpers\hydrate\hydrateDefinedTerm()` takes an optional
  `$class` parameter (default `DefinedTerm::class`), so a caller can pin the
  hydration target to an enriched subclass instead of the plain schema.org
  class. `xyz\oihana\schema\helpers\hydrate\hydrateCustomerSite()` now passes
  `DeliveryMethodTerm::class`, so a site's `deliveryMethod` keeps
  `shippingRate`, `freeShippingThreshold` and `vat` instead of losing them to
  the base `DefinedTerm` shape. Existing callers are unaffected — the default
  reproduces the prior behaviour exactly. Covered by two new
  `HydrateDefinedTermTest` cases (single definition and indexed array into the
  given class) and a new `HydrateCustomerSiteTest` case asserting the
  `DeliveryMethodTerm` instance and its two amounts.
- `BusinessDocumentLine::$price` now carries
  `#[HydrateAs(CompoundPriceSpecification::class)]`, so a raw price payload is
  hydrated into a `CompoundPriceSpecification` instead of a `MonetaryAmount`. The
  declared type (`null|array|MonetaryAmount|PriceSpecification`) is unchanged, and
  so is the constructor's shallow assignment — a raw array still survives it
  untouched. What changes is `Reflection::hydrate` : the union was ambiguous and
  resolved to its first class member (`MonetaryAmount`), which silently dropped
  any `price` / `priceCurrency` payload ; the attribute now pins the target, and
  a unit price can be broken down into the `UnitPriceSpecification` components
  applying in parallel (base price, eco-fee, deposit...) through
  `priceComponent`, which hydrates too. Note the flip side of a single-class
  attribute : a `MonetaryAmount`-shaped payload (`value` / `currency`) is now the
  one that hydrates into an empty object — a line price is expected in the
  `price` / `priceCurrency` shape. Covered by three new `BusinessDocumentLine`
  tests (hydration target, nested `priceComponent`, and the `@type` surviving
  `jsonSerialize`).
- Replaces the hardcoded `'Y-m-d'` default of
  `UnitPriceSpecificationTrait::getLastUnitPriceSpecification()` with
  `org\iso\Iso8601Format::DATE`, so the expected date shape is named rather than
  spelled out. The value is identical — no behaviour change — and the suite now
  pins the default explicitly and covers a non-default format
  (`Iso8601Format::DATE_BASIC`) and a non-default property name
  (`validThrough`).
- Stops committing the JSON Schema generator output: `schemas/` is now gitignored
  and its 283 tracked `*.schema.json` files are untracked (the files stay on disk
  locally). Nothing in `src`, `tests`, `tools` or CI consumed them, the committed
  set had drifted well out of sync with `src` (whole namespaces such as
  `organizations/` and `actions/` were missing), and `composer schemas:all` — which
  deletes every artifact *before* regenerating — aborts on a fatal error partway
  through, so any attempt to refresh them destroyed the committed set. The
  generator is kept as local, on-demand tooling; the README now flags it as
  experimental and documents its known limitations (the fatal, the
  `additionalProperties: false` vs `@type`/`@context` mismatch that makes every
  serialized document fail its own schema, the unsatisfiable `oneOf` on `int|float`
  unions, the dropped `null` on simple nullable types, and the stub `$defs` and
  colliding `$id`s).
- Reparents `org\schema\creativeWork\EducationalOccupationalCredential` from
  `CreativeWork` to the new `Credential`, matching schema.org's own hierarchy.
  The four credential properties move up to the parent and are no longer
  declared (nor their constants duplicated) on the educational subtype, which now
  only owns `competencyRequired` and `educationalLevel`. Consequently
  `Person::$hasCredential` and `Organization::$hasCredential` are widened from
  `EducationalOccupationalCredential` to `Credential`, so a non-educational
  credential can be attached. Existing code is unaffected : an
  `EducationalOccupationalCredential` is still a valid value, and every moved
  property keeps its name and stays reachable on the subtype.
- Moves `HasColor` from `xyz\oihana\schema\thesaurus\traits` to the shared
  `xyz\oihana\schema\traits` namespace: `color` is a house presentation hint, not
  a thesaurus-specific concern (it is already composed by `ThesaurusScheme` and
  `ThesaurusDomain`, which are not terms). All imports are updated across `src`
  and `tests`; the SKOS-specific `HasTreeMetrics` stays under `thesaurus\traits`.
  The thesaurus wiki page trait link is updated (FR + EN).
- Removes the redundant `$active` property redeclarations from `WebApplication`,
  `ThesaurusScheme` and `ThesaurusDomain`: the flag is already declared on the
  root `Thing`, so every entity inherits it. Behavior is unchanged (the types
  were compatible); the property is simply no longer shadowed.

### Fixed

 - Fixed - fix(hydrate): a job title was the one nested reference left behind (2026-08-08) : 
 `hydrateCustomerEmployee()` resolved the three other nested references of an employee — its
  properties, its contact points, the site it works at — and walked past `jobTitle`. A caller
  joining the term against its own reference data got the whole row back, and a plain array
  where every sibling answered a typed instance.
- It is now hydrated into a `DefinedTerm`, under the same input guard as the others: a bare code,
  the shape a payload carries before any join, is not an array and travels through untouched.

- Fixes the URL of `org\schema\enumerations\PriceTypeEnumeration::STRIKE_THROUGH_PRICE` :
  it read `http://schema/org/StrikethroughPrice` — plain `http`, and a slash
  where the dot of `schema.org` belongs — a dead host no schema.org term ever
  lived on. It now points at `https://schema.org/StrikethroughPrice`, and the
  new `PriceTypeTest` pins the corrected value.

- Fixes four unguarded `mixed` values, found by auditing PHPStan level 9 and each
  reproduced before being touched :
  `SetContactPointTrait` passed its `mixed $value` straight to
  `isValidPhoneNumber( string $phone )`, so assigning an array to a phone-shaped
  property raised a `TypeError` — and `__set` routes whatever a caller assigns ;
  `Product::resolveUnitCode()` cast `mixed` to `string`, turning an array into the
  literal unit code `"Array"` — a silent corruption — and raising a fatal `Error`
  on an object ;
  `Product::getInventoryLevelInUnitOfSale()` divided a `StockLevel::$value` that
  a base read may well have left as a raw array or a non-numeric string,
  raising *"Unsupported operand types"* ;
  and the `QuantitativeValue` builder cast any non-null value to `float`, so a
  non-numeric one became a meaningless `0.0` instead of "unknown". All four now
  guard on the shape they need and read an unusable value as absent. Covered by
  three new test cases, each failing with the original error when the fix is
  reverted.
  This clears 11 of level 9's 35 findings. The floor stays at **8** : the
  remaining 24 are not defects but the cost of two deliberate choices — the
  hydration helpers' `mixed` passthrough contract, and the untyped arrays AQL and
  SQL rows arrive as. Silencing those would mean adding defensive checks that
  prove to the analyser what the design already guarantees.

- Raises the PHPStan floor to **level 8** and clears everything below it. The 17
  remaining level 7 findings split into two unrelated families, which the report
  presented identically :
  `hydrateAdditionalProperty()` and `hydrateContactPoint()` were natively typed
  `?array` while their four sibling helpers take `mixed` and pass through what
  they cannot hydrate. Since callers feed them straight from properties declared
  `null|array|ContactPoint|string`, a lone instance or an unresolved string
  reference raised a `TypeError` — reachable from an ordinary `hydrateCustomer()`
  payload. Both now follow the sibling contract : anything that is not an array
  is handed back untouched, an array that is not an indexed non-empty list still
  yields `null`. Widening a parameter is backward compatible for existing
  callers.
  The other family was documentation : five `@param array|null` docblocks on
  functions whose native signature is already `mixed`, corrected to match.
  Two further fixes came out of the same pass :
  `BusinessIdentity::extractKey()` filtered on `is_scalar()` while declaring a
  `null|int|string` return, so a `bool` came back as `1` and a `float` was
  truncated with a precision-loss deprecation — it now filters on
  `is_int()`/`is_string()`, matching the check the rest of the method already
  used. Reachable through a nested `worksFor`, which is untyped ; not through
  `subject`, whose own union coerces such values to string first.
  And `SetPostalAddressTrait::normalizePostalAddress()` guards against an empty
  separator, which made `explode()` raise a `ValueError`.
  Five new test cases, each reproducing its defect when the fix is reverted.
  Level 8 needed no further work — it reports nothing this library does.

- Fixes four fatal errors in `xyz\oihana\schema\traits\SetContactPointTrait`,
  raised by shapes its own property type declares as legal. `contactPoint` is
  `null|ContactPoint|array|string` everywhere the trait is composed, but the code
  only ever handled the list form :
  a lone `ContactPoint` died on *"Cannot use object of type ContactPoint as
  array"*, an unresolved string reference on *"[] operator not supported for
  strings"*, and `findContactPointByType()` raised a `TypeError` on both a lone
  instance (`array_find()` wants an array) and on a list of raw arrays — which is
  precisely what a base read hands back before anything hydrates it, so that last
  one was reachable from the most ordinary path there is. A new
  `contactPointList()` normalises the property to a list, wrapping a lone
  instance or a string rather than discarding it, and the lookup now skips
  entries that are not hydrated instead of dereferencing them. Behaviour on the
  nominal list form is unchanged. This also clears 40 of the 57 findings PHPStan
  reports at level 7, from five source lines. Covered by four new
  `SetContactPointTraitTest` cases, each of which reproduces one of the crashes
  when the fix is reverted.

- Raises the PHPStan floor to **level 6**, which costs nothing : every single one
  of that level's 728 findings was `missingType.iterableValue`, now excluded in
  `phpstan.neon`. The rule assumes every `array` is a homogeneous collection and
  asks what it holds ; here it is not a collection but a *state*. A property
  declared `null|array|Organization|Person` receives an untyped associative array
  from an AQL or SQL row and later has it replaced by `new Organization( ... )` —
  the `array` member exists so that raw row can sit in the property until
  hydration swaps it. `array<Organization|Person>` would therefore not be a more
  precise annotation but a false one, since nothing in that array *is* an
  Organization ; and the only accurate form, `array<string, mixed>`, says nothing.
  With no correct annotation to write, this is a mismatch between the rule and the
  design rather than missing types. With it out of the way the
  levels above become readable — 57 real findings at level 7, 94 at level 9, 188
  at level 10 — and they concentrate far more than the raw counts suggest : the
  57 come from just 16 distinct source lines, since a line in a trait is
  reported once per composing class.

- Clears the last of PHPStan's level 4 findings, which turn out to be five
  distinct sites rather than the eighteen the report counts (one trait line is
  reported once per composing class) :
  `xyz\oihana\schema\products\Product::searchEligibleQuantityByType()` dropped a
  dead ternary branch — by the time it ran, the conversion a few lines above had
  already turned `$qv` into an array, so the `instanceof` arm was unreachable and
  a fresh `QuantitativeValue` was always built ;
  `hydrateWarehouse()` lost an `if` and a ternary that tested whether a
  `new Warehouse()` is a `Warehouse` ;
  `customerKeys()`, `sellerKeys()` and `SetContactPointTrait` dropped `?->`
  operators on values that cannot be null. `User::$identities` keeps its runtime
  `instanceof` guard — the guard is right and the docblock was wrong, promising
  `array<BusinessIdentity>` when the raw arrays a base read hands back survive
  the constructor untouched, so it now says `array<BusinessIdentity|array>`.
  Behaviour is unchanged throughout, and a new `SetContactPointTraitTest` case
  covers the raw-array entries the removed `?->` used to paper over.
  The four `trait.unused` findings are excluded in `phpstan.neon` instead : the
  analysis covers `src` only, so a trait used solely by the test suite — or
  exported for consumers and never composed in here — reads as dead, which is a
  question of API surface rather than of static correctness. **`src` is now clean
  at level 5.**

- Fixes the `@return` of `org\schema\helpers\hydrate\hydrateContactPoint()`,
  which announced `PropertyValue[]|null` while the function has always returned
  `ContactPoint[]|null` — a copy-paste from `hydrateAdditionalProperty()`, its
  neighbour. The `PropertyValue` import it dragged along is dropped too.
  Behaviour is unchanged, and the existing `HydrateContactPointTest` already
  asserted the real contract (`assertContainsOnlyInstancesOf(ContactPoint::class)`),
  so the docblock was the only thing that ever disagreed. Reported by PHPStan
  at level 3, with its level 4 twin (`return.unusedType`) falling away with it.

- Fixes the class docblock of `xyz\oihana\schema\traits\SetGeoCoordinatesTrait`,
  whose only content was `@property ?GeoCoordinates geo` — invalid PHPDoc (the
  variable name needs its `$`), and wrong twice over : `geo` is a real declared
  property, not a magic one, and its type is
  `null|array|GeoCoordinates|GeoShape` on `org\schema\traits\PlaceTrait`, not
  `?GeoCoordinates`. Removed rather than repaired, and replaced by a description
  of what the trait actually does. Reported by PHPStan at level 2.

- Fixes the silent loss of contact points on every site flavour.
  `xyz\oihana\schema\places\Site` now declares `contactPoint`, and the
  declaration is removed from `xyz\oihana\schema\places\Place` (which extends
  `Site`) where it became redundant — the same treatment the `$active`
  redeclarations already got. `SetContactPointTrait` reads and writes that
  property, and is composed by every flavour through `SiteTrait` or directly,
  but only `Place` declared it : on `Office`, `Warehouse`, `JobSite`,
  `CustomerSite` and `ProviderSite` the write landed on the classes' own
  `__set` hook — which routes additional properties, geo coordinates and postal
  addresses, and nothing else — so the contact was dropped without a word. Not
  even a dynamic-property deprecation surfaced, precisely because `__set` was
  there to swallow it. Found by PHPStan at level 1 (5 errors, all of them this).
  Note that `setContactPointProperty()` is still not wired into `SiteTrait::__set`
  — it is invoked only by `Company` and `Person` — so this changes no behaviour
  on its own ; it makes the trait's contract hold wherever it is composed.
  Covered by a new `SiteTest` (the property exists on all seven flavours, is
  declared once on `Site`, and a contact really is stored instead of vanishing).

- Fixes a latent autoload failure in `org\schema\traits\PlaceTrait`, which
  imported `org\schema\creativeWork\Website` while the class — and its file —
  are named `WebSite`. PHP matches class names case-insensitively, so the
  `WebSite $website` declaration resolved through that faulty alias ; PSR-4,
  however, turns the name straight into a path and looked for a `Website.php`
  that does not exist. On macOS the filesystem is case-insensitive and the class
  loaded anyway, which is why nothing ever surfaced ; on the Linux CI and in
  production it would not have. Found by PHPStan at level 0 — the only error
  that level reported. Covered by a new `Psr4ImportCaseTest` that walks every
  `use` in `src` and `tests`, resolves it through the project's PSR-4 roots and
  compares against the real directory listing (`file_exists()` is itself
  case-insensitive on macOS, so it cannot be trusted here). The library's other
  ~800 files are clean.

- Fixes the class docblock of `org\schema\ParcelDelivery`, which described an
  `Order` word for word — the text and the `@see` link had been copied from
  that class — and now describes the parcel delivery it actually models, with
  the matching `@see https://schema.org/ParcelDelivery`.

- Documents the `#[HydrateWith]` union resolution and its project-side override
  in the bilingual getting-started guide (FR + EN), as two new sections : how the
  hydrator picks a class from the payload's discriminator (`@type`/`atType`/`type`,
  matched on the short or fully-qualified name), then from the properties present,
  then from the first candidate ; and how a downstream project aims at its own
  classes by extending and redeclaring the property — with the same type, since
  PHP enforces property type invariance and narrowing it is a fatal error at class
  load. Also corrects the guide's reflection example, which called
  `Reflection::hydrate()` statically : `hydrate()` is an instance method, so the
  documented snippet raised an `Error` as written.
- Fixes the silent mis-hydration of every ambiguous `A|B` union across the
  business documents, by declaring the candidate classes on the property itself :
  `#[HydrateWith(Organization::class, Person::class)]` on
  `BusinessDocument::$customer`/`$seller`/`$author`,
  `Invoice::$broker`/`$provider` and `Site::$ownedBy` ;
  `#[HydrateWith(Product::class, Service::class)]` on the `item` of
  `BusinessDocumentLine`, `DeliveryLine` and `GoodsReceiptLine`. Without them,
  `Reflection::hydrate()` had nothing to discriminate on and kept the union's
  first class member whatever the payload said — so a `Person` customer came out
  as an (empty-ish) `Organization`, and a `Service` line item as a `Product`.
  `#[HydrateWith]` accepts several classes and lets the hydrator pick from the
  payload's discriminator (`@type`, `atType` or `type`, matched against each
  candidate's short or fully-qualified name) and, failing that, from the
  properties present. The fix is therefore declarative and applies to **every**
  caller, including a direct `Reflection::hydrate()` with no helper involved, and
  is inherited by all ten `BusinessDocument` subclasses since none of them
  override these properties. The declared types are unchanged, the constructor's
  shallow assignment is unchanged, and a raw identifier (string or integer)
  passed instead of an object still comes through untouched. The `item`
  properties resolve to `xyz\oihana\schema\products\Product` — the
  commerce-enriched product, itself an `org\schema\Product`, so the declared
  union still holds.
- Widens `BusinessDocument::$customer` and `BusinessDocument::$seller` from
  `null|Organization|Person` to `null|array|Organization|Person`. Both properties
  were the last two party slots of the class still missing `array`, so a raw,
  pre-hydration payload (`[ 'customer' => [ 'name' => 'Jane Doe' ] ]`) threw a
  `TypeError` in the constructor's shallow assignment, while the sibling
  `author`, `contact` and `billingAddress` accepted it. They now match the rest
  of the class — and `Reflection::hydrate` still yields real `Organization` /
  `Person` instances. Covered by a new `BusinessDocument` test asserting the
  three party slots keep their raw arrays through the constructor.
- Adds the missing `array` member to the eleven remaining structured properties
  of the `xyz\oihana` namespace, closing the same `TypeError` as above wherever
  the house domain classes can be built straight from a raw payload :
  `Product::$eligibleQuantity`, `Provider::$productInfo`,
  `Application::$createdBy` / `$disabledBy` / `$keyfile`, the same three on
  `Service`, `TaxDetail::$category`, `EcoFeeRule::$category` and
  `Person::$ownedBy`. `Product::$eligibleQuantity` was the clearest
  inconsistency : it carries `#[HydrateAs(QuantitativeValue::class)]`, which
  declares the value arrives as an array, while its type forbade one. The
  matching `@var` tags are realigned on the declarations, and each class gains a
  constructor test asserting the widened properties keep their raw arrays. The
  `org\schema` mapping layer is deliberately left untouched : its unions mirror
  schema.org and it is not built from raw payloads directly.

## [1.3.0] - 2026-07-07

### Added

- Adds the three documents the retrospective standards audit flagged as
  entirely absent from the hierarchy (Lot 9, closing the post-audit backlog):
  `DebitNote`, `GoodsReceiptConfirmation` (+ its `GoodsReceiptLine` value
  object) and `RemittanceAdvice`, all under
  `xyz\oihana\schema\business\documents`.
  - `DebitNote` — the symmetric inverse of `CreditNote` (increases what's
    owed, correcting an under-billed invoice): `reason`, `referencesInvoice`
    (→ one or more `Invoice`). UBL defines it as its own document type; the
    adjusting amount flows through the inherited `totals`.
  - `GoodsReceiptConfirmation` — the buyer confirming receipt of a
    `DeliveryNote`'s goods (`referencesDeliveryNote`, `lines`), each
    `GoodsReceiptLine` reconciling `expectedQuantity`/`receivedQuantity` with
    a `condition` and `discrepancyNote`. This is what UBL/Peppol's
    `ReceiptAdvice` actually models — the buyer-side mirror of a despatch
    advice, NOT this namespace's payment-focused `Receipt` — and the first
    buyer-side document of an otherwise seller-centric hierarchy.
  - `RemittanceAdvice` — the payer-side counterpart of `Receipt`
    (`amountRemitted`, `referencesInvoice`): the document a payer sends the
    payee to detail a payment. The two deliberately coexist, each modelling
    the same event from opposite ends of the transaction.
  - Adds the companion `DebitNoteTrait`, `GoodsReceiptConfirmationTrait`,
    `GoodsReceiptLineTrait` and `RemittanceAdviceTrait` constants traits,
    wired into `DocumentsTrait` — their `reason`/`referencesInvoice`/`lines`/
    `item`/`position`/`discrepancyNote` constants reuse values already
    declared elsewhere, so they compose cleanly; no collision, all reachable
    through `Oihana`.
  - Adds the four dedicated test suites and extends the bilingual
    `business-documents.md` wiki guide (FR canonical + EN mirror), bumping
    the README overview table (23 → 27) and both wiki indexes.
- Enriches the credit note (Lot 7 of the post-audit business-documents
  backlog) with the fields commercial accounting APIs expose on a credit
  note. Adds `xyz\oihana\schema\enumerations\CreditNoteReasonCode`
  (DUPLICATE_BILLING, GOODS_RETURNED, GOODWILL, OTHER, PRICING_ERROR,
  SERVICE_NOT_RENDERED — the structured cause UBL's `DiscrepancyResponse`
  and Peppol's reason codes expect) and `CreditNoteDisposition` (REFUNDED,
  REAPPLIED, PENDING — the cash-refund vs. reapply-to-invoice distinction
  Odoo's reversal wizard makes explicit), both free-value maison
  enumerations. `CreditNote` gains `reasonCode` (→ `CreditNoteReasonCode`,
  alongside the existing free-text `reason`, not replacing it),
  `remainingBalance` (`null|array|MonetaryAmount`, the not-yet-applied part
  of the credit — Xero's `RemainingCredit`, QuickBooks' `Balance`) and
  `disposition` (→ `CreditNoteDisposition`).
  - Adds the `DISPOSITION`/`REASON_CODE`/`REMAINING_BALANCE` constants on
    `CreditNoteTrait` (reachable through `Oihana`, no collision) and the
    `CreditNoteDispositionTest`/`CreditNoteReasonCodeTest` suites, and
    extends `CreditNoteTest` (new constants, `Reflection::hydrate()` of
    `remainingBalance`).
  - Extends the bilingual `business-documents.md` wiki guide (FR canonical
    + EN mirror) with the enriched `CreditNote` entry and the enumerations
    pointer.
- Enriches the account statement (Lot 6 of the post-audit business-documents
  backlog) with the reporting fields a statement of account is usually
  expected to expose. Adds `xyz\oihana\schema\enumerations\StatementEntryType`
  (INVOICE, PAYMENT, CREDIT_NOTE, ADJUSTMENT, OPENING_BALANCE, OTHER — free
  values, like the other maison enumerations) and the
  `xyz\oihana\schema\business\documents\AgingSummary` value object (the
  accounts-receivable aging breakdown: `current`, `days1To30`, `days31To60`,
  `days61To90`, `over90`, each a `MonetaryAmount`) — a reporting convention
  expected by QuickBooks/Xero that UBL's own `Statement` schema doesn't carry
  either; the library models the shape only, the consumer computes each
  bucket. `StatementEntry` gains `type` (→ `StatementEntryType`, so the
  movement's nature is explicit rather than inferred from the referenced
  document, à la Odoo `move_type`), `dueDate` (the maturity aging is computed
  from), and `debitAmount`/`creditAmount` (an optional explicit debit/credit
  split, mirroring UBL's `DebitLineAmount`/`CreditLineAmount` — the existing
  signed `amount` is kept as-is, these complement it rather than replacing
  it). `Statement` gains `agingSummary` (→ `AgingSummary`) and
  `totalDebit`/`totalCredit` (period aggregates, mirroring UBL's
  `TotalDebitAmount`/`TotalCreditAmount`).
  - Adds the companion `AgingSummaryTrait` constants trait and the new
    `StatementEntryTrait`/`StatementTrait` constants, wired into
    `DocumentsTrait` — `TYPE`/`DUE_DATE` reuse the values already declared on
    `AdjustmentTrait`/`PaymentInstallmentTrait`, so they compose cleanly; no
    name collision found, all reachable through `Oihana`.
  - Adds the `AgingSummaryTest` and `StatementEntryTypeTest` suites and
    extends `StatementEntryTest`/`StatementTest` (defaults, trait constants,
    `Reflection::hydrate()` deep-hydration of the new nested properties).
  - Extends the bilingual `business-documents.md` wiki guide (FR canonical
    + EN mirror) with the aging/type/debit-credit example, and bumps the
    README overview table (22 → 23) and both wiki indexes.
- Adds `xyz\oihana\schema\business\documents\DeliveryLine` and
  `ProofOfDelivery` (Lot 5 of the post-audit business-documents backlog) —
  closing the biggest gap confirmed by the retrospective standards audit
  (UBL `DespatchLine`, GS1/EDIFACT, Odoo `stock.move`, SAP delivery items):
  a bare `DeliveryNote::$orderDelivery` (a single `org\schema\ParcelDelivery`)
  could only say a parcel shipped, not how much of what was ordered it
  actually contains. `DeliveryLine` (`position`, `item`, `orderedQuantity`/
  `deliveredQuantity`/`backorderQuantity` + `backorderReason`, `batchNumber`,
  `serialNumbers`) reconciles ordered vs. delivered vs. backorder quantity
  per line, with optional lot/serial traceability. `ProofOfDelivery`
  (`signatory`, `date`, `discrepancyNote`) records the delivery confirmation
  — a pure trace, not an engine, in the same spirit as `PaymentReminder`.
  Both attach to `DeliveryNote` as `lines` (`null|array|DeliveryLine`,
  `#[HydrateWith]`) and `proofOfDelivery` (`null|array|ProofOfDelivery`,
  `#[HydrateAs]`), alongside the existing `orderDelivery`.
  - Adds the companion `DeliveryLineTrait`/`ProofOfDeliveryTrait` constants
    traits, and the `LINES`/`PROOF_OF_DELIVERY` constants on
    `DeliveryNoteTrait`, wired into `DocumentsTrait` — no name collision
    found, so reachable through `Oihana` as well.
  - Adds the `DeliveryLineTest`/`ProofOfDeliveryTest` suites and extends
    `DeliveryNoteTest` (defaults, trait constants, `Reflection::hydrate()`
    deep-hydration of both nested properties).
  - Extends the bilingual `business-documents.md` wiki guide (FR canonical
    + EN mirror) with both classes, a partial-delivery hydration example,
    and bumps the README overview table (20 → 22) and both wiki indexes.
- Attaches the reminders to the payment plan: adds a `reminders` property
  (`null|array|PaymentReminder`, deep-hydrated through `#[HydrateWith]`) to
  both `PaymentInstallment` (reminders for that installment) and
  `PaymentSchedule` (reminders for the plan as a whole), so reminders can be
  recorded at either grain. Adds the `REMINDERS` constant on both
  `PaymentInstallmentTrait` and `PaymentScheduleTrait` (same value, so they
  compose cleanly through `DocumentsTrait` and stay reachable as
  `Oihana::REMINDERS`). Updates the `PaymentSchedule` PHPDoc (reminders are
  no longer "a later iteration"), extends `PaymentInstallmentTest` and
  `PaymentScheduleTest` with the reminders hydration, and completes the
  bilingual `business-documents.md` wiki guide (FR canonical + EN mirror)
  with the `PaymentReminder` catalog entry, a reminders example, the
  when-to-use row and the enumerations pointer — bumping the namespace class
  count (19 → 20) in the README overview and both wiki indexes.
- Adds the `xyz\oihana\schema\business\documents\PaymentReminder` value
  object — the record of a payment reminder sent to (or planned for) the
  customer about an unpaid installment or document. A pure trace, not an
  engine: `date`, `level` (→ `PaymentReminderLevel`), `channel`
  (→ `PaymentReminderChannel`), `status` (→ `org\schema\enumerations\status\ActionStatusType`
  and its `PotentialActionStatus`/`ActiveActionStatus`/`CompletedActionStatus`
  members — no new enumeration), `amountClaimed` (`MonetaryAmount`),
  `adjustments` (the late-payment charges expressed as `Adjustment`, the same
  vehicle used on lines and documents — never a bespoke "penalty" field) and
  a free-text `note`. The reminder *scheduling/sending* logic stays a
  consumer concern. Adds the companion `PaymentReminderTrait`, wired into
  `DocumentsTrait` — its `ADJUSTMENTS`/`DATE`/`LEVEL`/`NOTE`/`STATUS`
  constants share the value already present elsewhere, so they compose
  cleanly and stay reachable through `Oihana`. Adds the `PaymentReminderTest`
  suite (defaults, constants + `Oihana` aggregation, constructor and
  `Reflection::hydrate()` deep-hydration of the nested `MonetaryAmount` and
  `Adjustment`).
- Adds the `xyz\oihana\schema\enumerations\PaymentReminderLevel` and
  `PaymentReminderChannel` enumerations — the two "maison" enumerations
  backing the upcoming payment-reminder value object. Both extend
  `org\schema\Enumeration` with `schema.oihana.xyz` URI values and keep the
  value free (a member, a free-text label, or a project subclass adding its
  own members). `PaymentReminderLevel` carries the escalation scale
  `REMINDER` → `FIRST_REMINDER` → `SECOND_REMINDER` → `FINAL_NOTICE` →
  `FORMAL_NOTICE` (mise en demeure); `PaymentReminderChannel` carries
  `EMAIL`, `POSTAL`, `PHONE`, `SMS` and `OTHER`. Adds the
  `PaymentReminderLevelTest` and `PaymentReminderChannelTest` suites
  (instance, constants, `includes()`).
- Adds a per-installment payment status to
  `xyz\oihana\schema\business\documents\PaymentInstallment`:
  `$paymentStatus` (`null|string|PaymentStatusType`) reuses
  `org\schema\enumerations\status\PaymentStatusType` and its existing member
  classes (`PaymentComplete`, `PaymentDue`, `PaymentPastDue`...) — no new
  enumeration — so a payment plan can be tracked installment by installment
  (one installment paid while another is still due or past due), the
  finer-grained counterpart of `Invoice::$paymentStatus`. Adds the
  `PAYMENT_STATUS` constant on `PaymentInstallmentTrait` (already present on
  `InvoiceTrait` with the same value, so the two compose cleanly through
  `DocumentsTrait`) — `PaymentInstallment::PAYMENT_STATUS` and
  `Oihana::PAYMENT_STATUS` both resolve. Reminders stay out of scope, a later
  iteration. Extends `PaymentInstallmentTest` (constant + `Oihana`
  aggregation, default, member class-name assignment) and the bilingual
  `business-documents.md` wiki guide (FR canonical + EN mirror).
- Adds the upstream link of the quote → purchase order → invoice cycle:
  `xyz\oihana\schema\business\documents\PurchaseOrder::$referencesQuote`
  (→ `Quote`), the counterpart of `Invoice::$referencesOrder` downstream and
  the data behind the `BusinessDocumentStatus::CONVERTED` transition —
  previously `PurchaseOrder` was a property-less shell. Adds the companion
  `PurchaseOrderTrait` (`REFERENCES_QUOTE`), wired into `DocumentsTrait` —
  no name collision found, so reachable through `Oihana` as well.
- Adds a document-level `adjustments` property to
  `xyz\oihana\schema\business\documents\BusinessDocument` —
  completing the UBL `AllowanceCharge` design where an `Adjustment` applies
  either to a single `BusinessDocumentLine` **or** to the whole document
  (a footer discount, a global shipping fee, packaging billed at the
  document level). Typed `null|array|Adjustment` with `#[HydrateWith(
  Adjustment::class)]`, mirroring the existing `taxes` handling. The
  `ADJUSTMENTS` property-name constant is declared on `BusinessDocumentTrait`
  (already present on `BusinessDocumentLineTrait` with the same value, so the
  two compose cleanly through `DocumentsTrait` under PHP 8.4), making
  `BusinessDocument::ADJUSTMENTS` and `Oihana::ADJUSTMENTS` both resolve.
  - Adds the optional derived `allowanceTotal`/`chargeTotal` amounts to
    `DocumentTotals` (with the `ALLOWANCE_TOTAL`/`CHARGE_TOTAL` trait
    constants), mirroring UBL's `AllowanceTotalAmount`/`ChargeTotalAmount`
    monetary-total breakdown: the summed effect of the document-level
    allowances (discounts) and charges (surcharges/fees). Both are
    `null|array|MonetaryAmount` recap values — the individual adjustments
    remain on `BusinessDocument::$adjustments`.
  - Extends `BusinessDocumentTest` (default, constant + `Oihana` aggregation,
    constructor and `Reflection::hydrate()` of an `Adjustment` array) and
    `DocumentTotalsTest` (the two new amounts across defaults, constants and
    deep hydration).
  - Extends the bilingual `business-documents.md` wiki guide (FR canonical +
    EN mirror): the full `Quote` example gains a document-level discount and
    an `allowanceTotal`, and the line/document distinction is spelled out.
- Adds `CreditNote`, `DeliveryNote`, `Receipt` and `Statement`/`StatementEntry`
  to `xyz\oihana\schema\business\documents` (Lot 4, closing the
  business-documents workstream) — the quote → purchase order → invoice
  cycle's neighbors. `CreditNote` (`reason`, `referencesInvoice` →
  `Invoice`) corrects/cancels an already-issued invoice; the corrected
  amount flows through the inherited `totals` recap, the "this reduces
  what's owed" meaning is carried by the document type itself, not a sign
  convention. `DeliveryNote` (`orderDelivery`) reuses `org\schema\Order`'s
  own property name and its `org\schema\ParcelDelivery` type rather than
  re-inventing shipment tracking. `Receipt` (`confirmationNumber`,
  `paymentMethod`, `paymentMethodId`, `referencesInvoice` → `Invoice`)
  reuses `org\schema\Invoice`'s property names ; the received amount and
  date are not duplicated, they're the inherited `totals`/`issueDate`.
  `Statement` (`billingPeriod`, `entries`, `openingBalance`,
  `closingBalance`) is the only non-thin class of the lot: it introduces
  `StatementEntry` (`document`, `date`, `amount`, `balance`), a new
  `StructuredValue` line concept distinct from `BusinessDocumentLine`
  (an account movement, not a priced product/service).
  - Adds the companion `CreditNoteTrait`/`DeliveryNoteTrait`/`ReceiptTrait`/
    `StatementTrait`/`StatementEntryTrait` constants traits, wired into
    `DocumentsTrait` — no name collision found, so reachable through
    `Oihana` as well.
  - Adds the five dedicated test suites (defaults, `CONTEXT`, trait
    constants, constructor hydration, `Reflection::hydrate()` for the
    nested `referencesInvoice`/`orderDelivery`/`entries`/balance
    properties, inheritance checks).
  - Extends the bilingual `business-documents.md` wiki guide (FR canonical
    + EN mirror) with the four classes, a `Statement` hydration example,
    and bumps the README overview table (14 → 19) and both wiki indexes.
- Adds `xyz\oihana\schema\business\documents\Invoice` (Lot 3 of the
  business-documents workstream) — the final document of the quote →
  purchase order → invoice cycle: `accountId`, `billingPeriod`, `broker`,
  `category`, `confirmationNumber`, `paymentDueDate`, `paymentStatus`,
  `provider`, `referencesOrder`, `scheduledPaymentDate`. Reuses
  `org\schema\Invoice`'s property names, but deliberately does not share a
  property trait with it, even though both classes hydrate through the same
  raw-array `ThingTrait::__construct` path: `referencesOrder` must reference
  this namespace's own `PurchaseOrder` (the document actually being
  invoiced), not `org\schema\Order`, and the mirror's `broker`/`category`/
  `billingPeriod` unions predate the `null|array|X` typing convention —
  widening them to fit a shared trait would mean editing `org\schema\Invoice`,
  contradicting this hierarchy's "mirror stays untouched" rule. The
  properties are declared directly on the class instead, correctly typed.
  `paymentStatus` reuses `org\schema\enumerations\status\PaymentStatusType`
  and its existing member classes (`PaymentComplete`, `PaymentDue`,
  `PaymentDeclined`, `PaymentPastDue`, `PaymentAutomaticallyApplied`) rather
  than new constants.
  - Adds the companion `InvoiceTrait` constants trait, wired into
    `DocumentsTrait` — no name collision found (these properties already
    exist as Schema.org properties elsewhere), so reachable through `Oihana`
    as well.
  - Adds the dedicated test suite (defaults, `CONTEXT`, trait constants,
    constructor hydration, `paymentStatus`/`category` polymorphism,
    `Reflection::hydrate()` for `referencesOrder`, inheritance checks).
- Adds the `xyz\oihana\schema\business\documents\export` namespace — the
  `BusinessDocumentExporter` interface (`export(BusinessDocument $document): string`)
  and a trivial `JsonLdExporter` demonstration implementation delegating to
  `ThingTrait::jsonSerialize()`. Regulatory export formats (UBL, Factur-X,
  Peppol, PDF, HTML) remain out of scope for now.
  - Adds the dedicated test suite and extends the bilingual
    `business-documents.md` wiki guide (FR canonical + EN mirror) and the
    README overview table (11 → 14) with `Invoice` and the export layer.
- Adds `xyz\oihana\schema\business\documents\BusinessDocument` (Lot 2 of the
  business-documents workstream) — the common parent of the quote → purchase
  order → invoice cycle: `attachments`, `currency`, `customer`,
  `documentLines`, `issueDate`, `paymentTerms`, `references`, `seller`,
  `status` (→ `BusinessDocumentStatus`), `taxes`, `totals`. Extends
  `org\schema\Intangible` rather than reusing the mirror's `Order`/`Invoice`:
  a business document qualifies a transaction, it is not an addressable
  resource in its own right, and this keeps the schema.org mirror untouched —
  existing consumers of `org\schema\Order`/`Invoice` see no change. Adds
  `Quote` (adds `validThrough`, reusing the schema.org property already
  carried by `PriceSpecification`/`Offer` rather than a new `validUntil`
  name) and `PurchaseOrder` (no properties of its own in this version).
  - Adds the companion `BusinessDocumentTrait`/`QuoteTrait` constants traits,
    wired into `DocumentsTrait` — no name collision found, so reachable
    through `Oihana` as well.
  - Adds the three test suites (defaults, `CONTEXT`, trait constants,
    constructor hydration, `Reflection::hydrate()` for the nested
    `documentLines`/`taxes`/`totals`/`paymentTerms`, inheritance checks).
  - Extends the bilingual `business-documents.md` wiki guide with the three
    classes and a full-document hydration example, and bumps the namespace
    class count (8 → 11) in the README overview table and both wiki indexes.
- Adds the `xyz\oihana\schema\business\documents` namespace — the cross-cutting
  value objects of the quote/purchase-order/invoice cycle (Lot 1 of the
  business-documents workstream, ahead of the document hierarchy itself):
  `TaxDetail` (a tax breakdown: `category`, `rate`, `basisAmount`,
  `taxAmount`), `Adjustment` (a UBL `AllowanceCharge`-inspired price
  adjustment: `type`, `amount`/`percentage`, `reason`, `includedInBase`,
  reusing `PriceComponentType` for `type` rather than a redundant enum),
  `EcoFeeRule`/`AppliedEcoFee` (an environmental-fee calculation rule and its
  traced application on a line — the monetary effect always flows through an
  `Adjustment` of type `environmentalFee`, never a dedicated `ecoTax`
  property), `DocumentTotals` (the document's monetary recap — `subtotal`,
  `totalTax`, `total`, `prepaidAmount`, `balanceDue`, each a `MonetaryAmount`;
  a dedicated value object rather than a reuse of
  `CompoundPriceSpecification`, whose schema.org role doesn't match a
  document-level recap), `BusinessDocumentLine` (a document line: `item`,
  `position`, `quantity`, `unit`, `price`, line-scoped `taxes`/`adjustments`,
  `subtotal`, `total`) and `PaymentSchedule`/`PaymentInstallment` (a
  multi-installment payment plan — reminders are a later iteration). All
  eight classes extend `org\schema\StructuredValue`
  and carry the `#[HydrateAs]`/`#[HydrateWith]` attributes needed for
  `Reflection::hydrate()` to deep-hydrate their `MonetaryAmount` and nested
  collection properties.
  - Adds the companion constants traits under
    `constants/traits/business/documents/`, aggregated through the new
    `DocumentsTrait` and composed into `BusinessTrait` — unlike
    `BusinessIdentityTrait`/`UserProfileTrait`, no name collision was found,
    so the new constants are also reachable through the global `Oihana`
    aggregator (e.g. `Oihana::RATE`, `Oihana::AMOUNT`).
  - Adds the eight dedicated test suites (defaults, `CONTEXT`, trait
    constants, constructor hydration, `Reflection::hydrate()` for the
    `MonetaryAmount`/nested-collection properties).
  - Adds the bilingual `business-documents.md` wiki guide (FR canonical + EN
    mirror), wired into both `oihana` wiki indexes and the repository
    `README.md` overview table.
- Adds the eight missing `org\schema\enumerations\status\OrderStatus` members
  as dedicated classes — `OrderCancelled`, `OrderDelivered`, `OrderInTransit`,
  `OrderPaymentDue`, `OrderPickupAvailable`, `OrderProblem`, `OrderProcessing`,
  `OrderReturned` (each `extends OrderStatus`) — following the one-class-per-member
  convention already established by this same `status/` folder (see
  `PaymentComplete`, `ActiveActionStatus`...), rather than plain constants.
  `org\schema\enumerations\status\PaymentStatusType` needed no change: its five
  members already exist as such classes. Populates
  `org\schema\enumerations\DeliveryMethod` (which, unlike its `status/`
  siblings, follows the constants convention shared by `Enumeration`
  subclasses such as `ItemAvailability`/`BusinessFunction`) with the eight
  GoodRelations-derived constants already listed in its own docblock, e.g.
  `DHL`, `ON_SITE_PICKUP`, `UPS`. Dedicated test suites cover the new classes
  and `DeliveryMethod`'s constants and `includes()`.
- Extends `xyz\oihana\schema\enumerations\PriceComponentType` with `DEPOSIT`,
  `DISCOUNT`, `ENVIRONMENTAL_FEE`, `PACKAGING` and `SURCHARGE` — the
  price-component vocabulary needed by the upcoming business-document
  `Adjustment` value object, reusing this enum rather than introducing a
  redundant one.
- Adds `xyz\oihana\schema\enumerations\BusinessDocumentStatus` (extends the
  schema.org `StatusEnumeration`) — the lifecycle status of a business
  document (`DRAFT`, `SENT`, `ACCEPTED`, `REJECTED`, `EXPIRED`, `CONVERTED`,
  `CANCELLED`), distinct from `OrderStatus` (which tracks an order's
  fulfillment, not the document's own lifecycle). First foundation piece of
  the upcoming business-document hierarchy; documented in the bilingual
  `oihana-core.md` wiki guide.
- Adds the autoloaded hydration and pivot helper functions — the library's
  first `autoload.files` layer. `org\schema\helpers\hydrate` carries the six
  pure schema.org array-to-object hydrators (`hydrateAdditionalProperty`,
  `hydrateContactPoint`, `hydrateDefinedTerm`, `hydrateGeoCoordinates`,
  `hydrateOfferPurchase`, `hydratePostalAddress`) and
  `xyz\oihana\schema\helpers\hydrate` the six business-layer ones built on
  top (`hydrateCustomer`, `hydrateCustomerEmployee`, `hydrateCustomerSite`,
  `hydrateWarehouse`, `hydrateStockLevel`, `hydrateAggregateOffer`) — each
  handles the single / indexed-list / passthrough shapes and hydrates its
  nested references (contact points, addresses, geo, defined terms, offers).
  `xyz\oihana\schema\helpers\pivots` adds the authenticated-account pivots
  (`customerKey`, `sellerKey`, `sellerKeys`) resolving the business
  identities of a `User` into the organization or seller keys that scope its
  resources. Fifteen dedicated test suites keep the source at 100% line
  coverage. A bilingual wiki guide (`oihana-helpers.md`, FR/EN) documents
  the layer — the loading, the org/xyz layering rule, the three accepted
  shapes and the full function catalog — wired into both wiki indexes.
- Completes the wiki coverage of the business layer with four bilingual
  guides (FR canonical + EN mirror): `oihana-organizations.md` (`Company`
  and its `Customer`/`Provider`/`Subsidiary`/`Affiliate` flavors),
  `oihana-people.md` (`Person` and its five typed flavors),
  `oihana-products.md` (the eligible-quantity tree, the unit-of-sale
  conversions, the `resolveUnitCode()` extension point and the satellite
  catalog) and the cross-cutting `oihana-ingestion.md` (the `__set`-driven
  bridges turning flat dataset rows into nested objects, trait by trait,
  and how ingestion complements the `hydrate*` helpers). Both wiki indexes
  gain the three namespace rows and a "Cross-cutting mechanisms" section.

### Changed

- Documents `Receipt`'s direct/cash-sale shape (Lot 8 of the post-audit
  business-documents backlog) — no new property, since `Receipt` already
  inherits `documentLines`/`taxes`/`totals` from `BusinessDocument` and
  `referencesInvoice` is already optional. Clarifies in the class docblock,
  the bilingual `business-documents.md` wiki guide (FR + EN) and a dedicated
  test that a receipt need not reference an invoice at all : a point-of-sale
  sale with no prior invoice (QuickBooks' `SalesReceipt`, Xero's `RECEIVE`
  bank transaction) leaves `referencesInvoice` null and carries the sale on
  the inherited `documentLines`/`taxes`/`totals`, so no separate
  "sales receipt" type is needed.
- Harmonizes the whole business-document cycle's `references*` links to
  **collections**: `Invoice::$referencesOrder`,
  `CreditNote::$referencesInvoice` and `Receipt::$referencesInvoice` switch
  from `#[HydrateAs]` (single object) to `#[HydrateWith]` (one-or-many),
  matching the new `PurchaseOrder::$referencesQuote` and the real cardinality
  of the domain — a consolidated invoice bills several purchase orders, a
  single payment settles several invoices, an order may aggregate several
  accepted quotes. Deep hydration stays polymorphic (a single associative
  array yields one object, a list yields an array of objects), so the type
  stays `null|array|X` and existing single-reference hydration is unaffected;
  this also resolves the prior mismatch where `Invoice::$referencesOrder`'s
  PHPDoc promised "one or more" while the attribute only hydrated one. All of
  it unreleased (`dev-main`), so no published API changes. Extends the
  reference test suites with the list case (`PurchaseOrderTest`,
  `InvoiceTest`, `CreditNoteTest`, `ReceiptTest`) and the bilingual
  `business-documents.md` wiki guide (FR canonical + EN mirror) with a
  "chaining the cycle" example.
- Reorganizes the wiki: every `oihana-*.md` page moves into a dedicated
  `wiki/{fr,en}/oihana/` folder (dropping the prefix), mirroring the
  existing `schema-org/` layout — the wiki root shrinks to the language
  index, the getting-started guide and the three namespace folders. Each
  `oihana/` folder gains its own `README.md` namespace index (guides +
  mechanisms tables). Every relative link is rewritten and verified
  (language indexes, getting-started pages, `schema-org` cross-references,
  the repository `README.md`), and the repository overview table gains the
  `organizations`, `people` and `products` rows.

- Adds the `xyz\oihana\schema\organizations` namespace — the business-entity layer. `Company` (extends the schema.org `Corporation`) is the base entity: French administrative identifiers (SIRET via `taxID`, APE via `naics`), `category`/`industry`/`invoiceType`/`status`/`vat`/`website` and the postal-address + contact-point ingestion via the `Set*` traits (see below). `Customer` (additional properties, assigned company/POS/seller, credit status, payment terms, price segmentation), `Provider` (carrier, minimum order value, `ProductProviderInfo` ingestion), `Subsidiary` and `Affiliate` specialize it.
- Adds the `xyz\oihana\schema\people` namespace — `Person` (extends the schema.org `Person`; additional properties + contact-point ingestion, `ownedBy`, `position`) and its flavors `Employee`, `CustomerEmployee`, `ProviderEmployee`, `Seller` and `SubsidiaryEmployee`.
- Adds the `xyz\oihana\schema\products` namespace — the commerce layer. `Product` (extends the schema.org `SomeProducts`) carries the unit of sale, the eligible-quantity tree (unit → package → pallet, UN/CEFACT codes and labels) and the stock conversions (`getUnitOfSaleConversionFactor`, `getInventoryLevelInUnitOfSale`, `findEligibleQuantityByType`). Pure data-to-data transformations (category hierarchy mapping, certification parsing) are deliberately left out of the class: they encode dataset-specific nomenclatures and belong to consumer-side helper functions. Around it: `ProductType`, `TaxRate`, `PriceSegmentation`, `ExtraPriceSpecification` (with `toUnitPriceSpecification()`), `PriceQuantityDiscount`, `PaymentCondition`, `PaymentMethod`, `ProductProviderInfo`, `ProductWarehouseInfo`, `ProviderProductWarehouseInfo`, `ProductWarehouseAvailability` and `StockLevel` (with `fromArray()` hydrating the `assignedPOS` as `Warehouse`).
  - The ingestion of proprietary unit nomenclatures is an explicit extension point: the protected `Product::resolveUnitCode()` hook returns the raw value unchanged by default, and a downstream subclass can override it to translate an ERP-specific unit code table into UN/CEFACT before the eligible-quantity tree is built.
- Adds the `xyz\oihana\schema\places` flavors `Place`, `CustomerSite` and `ProviderSite`, composing the new `SiteTrait` on top of the existing `Site`.
- Adds `xyz\oihana\schema\Website` (extends the schema.org `Thing`) with the companion `WebsiteTrait` constants.
- Adds the ingestion traits in `xyz\oihana\schema\traits` — the `__set`-driven bridges that let a flat dataset row hydrate a nested schema object without manual mapping: `SiteTrait` (aggregate), `SetPostalAddressTrait` (flat keys → nested `PostalAddress`, with `normalizePostalAddress()`), `SetContactPointTrait` (flat keys → typed `ContactPoint` collection), `SetGeoCoordinatesTrait` (flat keys → `GeoCoordinates`), `SetProductProviderInfoTrait` (buying-price keys → `ProductProviderInfo`), plus `UnitPriceSpecificationTrait` (`getLastUnitPriceSpecification()`) and the `ProductProperty` descriptive attributes.
- Adds the companion constants — `AcceptedPaymentTarget`, and the `CustomerAdditionalProperty` / `PersonAdditionalProperty` / `ProductAdditionalProperty` / `SiteAdditionalProperty` enumerations with their `normalize()` value coercion (boolean / integer casts). The property-name traits are organized by domain (`constants/traits/{extras,organizations,people,places,products}`) and aggregated through the new `ExtrasTrait`, `OrganizationTrait`, `PeopleTrait`, `ProductsTrait` and `WebsiteTrait` into `Oihana`.
- Adds the enumerations `BusinessEntityType`, `PriceComponentType`, `PriceType` and `UnitOfSaleType` in `xyz\oihana\schema\enumerations`.
- Adds the unit-test suites for the whole layer (`organizations`, `people`, `places`, `products`, `traits`, `constants`) — the library source is back to **100% line coverage** (`composer coverage:md`).
- Adds the `oihana/php-enums` and `oihana/php-standards` dependencies (UN/CEFACT measure and package codes).

- Adds the `xyz\oihana\schema\thesaurus\traits\HasTreeMetrics` trait carrying `childrenCount` (`?int`) — a **non-SKOS, structural** metric (the count of direct `narrower` concepts) deliberately kept out of the pure-SKOS `Concept` and composed only by hierarchical, project-specific terms (mirrors `HasColor`). Traversal/projection-only: computed at query time (e.g. an ArangoDB edge-count), never persisted nor harvested; a UI derives `isLeaf` as `childrenCount === 0`. `ProductCategoryTerm` now `use`s it. Companion `xyz\oihana\schema\constants\traits\thesaurus\TreeMetricsTrait` (`CHILDREN_COUNT`), aggregated through `ThesaurusTrait` into `Oihana` (reachable as `Oihana::CHILDREN_COUNT`).
  - Extends `ProductCategoryTermTest` to cover `HasTreeMetrics`: the `childrenCount` default, the `HasTreeMetrics` composition, the `CHILDREN_COUNT` constant and its aggregation into `Oihana`, and the `childrenCount` hydration through both the constructor and the reflection path (including the leaf `0` case).
- Adds the thesaurus **registry layer** — the administrative view of a vocabulary catalog (registry → domains → schemes → concepts), on top of the existing SKOS layer.
  - Adds `xyz\oihana\schema\thesaurus\ThesaurusScheme` (extends `ConceptScheme`) — a thesaurus taken as a whole, as it appears in a registry: visibility (`active`), display (`color` via `HasColor`, `order`), filing (`domain`, `null|string|array|ThesaurusDomain`, hydrated via `#[HydrateWith(ThesaurusDomain::class)]` on the reflection path), routing (`path`) and the provenance flags — `harvested` (the term core is fed by an external source and read-only, only the house overlays are editable) and `system` (the technical skeleton is defined in code and cannot be deleted through an API).
  - Adds `xyz\oihana\schema\thesaurus\ThesaurusDomain` (extends the schema.org `Intangible`) — the top-level, **flat-by-design** grouping of the registry's schemes (`active`, `color`, `order`; the i18n-ready `name`/`alternateName`/`description` labels are inherited from `Thing`). The domain↔scheme link is carried by `ThesaurusScheme::$domain`, not by the domain, so a domain is a pure filing shelf — not a set of terms.
  - Adds the companion `xyz\oihana\schema\constants\traits\thesaurus\ThesaurusSchemeTrait` (constants `ACTIVE`, `DOMAIN`, `HARVESTED`, `ORDER`, `PATH`, `SYSTEM`), aggregated through `ThesaurusTrait` into `Oihana` (reachable as `Oihana::ACTIVE`, etc.; the `DOMAIN`/`PATH` values match the auth/audit keys already exposed there). No `ThesaurusDomainTrait` is needed: the domain reuses the shared `ACTIVE`/`ORDER` keys from `ThesaurusSchemeTrait` and `COLOR` from `ThesaurusTermTrait`, mirroring `ProductCategoryTerm`.
  - Adds the `ThesaurusSchemeTest` and `ThesaurusDomainTest` suites — defaults, constants and the `Oihana` aggregation, both hydration paths (raw constructor assignment vs the `domain` `#[HydrateWith]` honored by `Reflection::hydrate()`, including the bare-reference case), the i18n language-map labels and the JSON-LD serialization (`@type`/`@context`).
  - Updates the bilingual `oihana-thesaurus.md` wiki guide (EN/FR) with the registry layer — the two new classes in the catalog, a `ThesaurusScheme` example and the `ThesaurusSchemeTrait` constants row — and bumps the namespace class count (6 → 8) in the README overview table and both wiki indexes.
- Adds `xyz\oihana\schema\Favorite` (extends the schema.org `Intangible`) — the edge linking an authentication account (`_from`, `users/{key}`) to a favorited resource (`_to`), stored in the `user_has_favorite` edge collection. Its `additionalType` records the **functional** favorite type (e.g. `customers`, `products`, `sellers`) — what kind of resource is bookmarked — which is distinct from the targeted resource's own schema.org `additionalType`, since several functional types may share a physical collection. Server-side only: it routes a write to the right model and lets reads be grouped/scoped by type without loading the targets; a favorites listing rebuilds each targeted resource with its own schema and never exposes the `Favorite` itself.
- Adds the `FavoriteTest` suite covering the `Intangible` inheritance, the `CONTEXT` constant, the edge/type defaults, constructor hydration of `_from`/`_to`/`additionalType` and the JSON-LD serialization (`@type`/`@context`, edge and `created`).

### Changed

- The `Oihana` constants aggregator now composes the `org\schema\constants\traits\Properties` mega-trait, so the full schema.org property vocabulary is reachable through `Oihana::*` alongside the domain-specific traits. No conflict with the existing domain traits — the class loads and the whole suite stays green.
- Refactors the ArangoDB system-attribute constants so the edge attributes live in a single place. The `_FROM`/`_TO` constants now belong to `org\schema\constants\traits\Edge` (documented as edge-only system attributes), and `org\schema\constants\traits\ArangoDB` `use`s `Edge` rather than redeclaring them — `ArangoDB` keeps the document-level `_ID`/`_KEY`/`_REV`. The `Properties` aggregator now composes `ArangoDB` (which pulls in `Edge`) instead of `Edge` directly. No public surface change: `Schema::_FROM`, `Schema::_TO`, `Schema::_ID`, `Schema::_KEY` and `Schema::_REV` resolve to the same values as before.
- Renames the `constants/traits/places` property-name trait `SiteTrait` to `Site`, aligning it with the other domain traits — and wires it back into the `Oihana` aggregator (the rename had silently dropped the site constants, e.g. `Oihana::OWNED_BY` and `Oihana::DELIVERY_METHOD`, from the aggregation).
- Fixes the `@package` tags of the `xyz\oihana\schema\people` classes (they pointed to the `organizations` namespace) and of `ApplicationType`, and completes the missing `@author`/`@package`/`@since` PHPDoc tags across the new namespaces.

### Fixed

- `Product::getUnitOfSaleConversionFactor()` no longer crashes on a fresh instance — the `$unitOfSale` typed property was read before initialization; it is now accessed with the usual `?? null` guard.
- `Product::toCertification()` now accepts a partial `id;name` definition (the destructuring is padded, no more PHP warning on short expressions) and returns `null` on an empty definition — the previous empty-guard was unreachable.
- `hydrateStockLevel()` and `hydrateAggregateOffer()` now delegate the assigned/available `Warehouse` to `hydrateWarehouse()` instead of instantiating it directly — the nested `ownedBy` is now hydrated into a `Subsidiary` (it used to stay a raw array and lose its `@type` on serialization).
- `StockLevel::fromArray()` had the same issue and duplicated the whole hydration logic besides — it now delegates entirely to `hydrateStockLevel()` instead, so the two call sites can no longer drift apart.
- `Warehouse`, `Office` and `JobSite` now `use` the `SiteTrait` composed by their `CustomerSite`/`ProviderSite` cousins — without it, flat address properties (`streetAddress`, `postalCode`, `geoLatitude`...) were never routed to the nested `address`/`geo` objects and were instead created as undeclared dynamic properties (a PHP 8.4 deprecation).
- `org\schema\constants\traits\Order::ACCEPTED_OFFER` (reachable as `Schema::ACCEPTED_OFFER`) held `'acceptedPaymentMethod'` instead of `'acceptedOffer'`, mismatching the `Order::$acceptedOffer` property it names.
- The same trait's `ORDER__ITEM` constant (double underscore, unused) named no matching property; renamed to `ORDERED_ITEM` with value `'orderedItem'`, matching `Order::$orderedItem`.
- Removes the dead-code duplicate `org\schema\enumerations\status\StatusEnumeration` (a second, unrelated class sharing the short name of `org\schema\enumerations\StatusEnumeration`, the one actually used everywhere else in the `status` sub-namespace). `ActionStatusType` turned out to be silently relying on the duplicate through unqualified same-namespace class resolution (no explicit `use`); it now explicitly `use`s the real `StatusEnumeration`, restoring `ActionStatusType` and its four members (`ActiveActionStatus`, `CompletedActionStatus`, `FailedActionStatus`, `PotentialActionStatus`).
- The five `PaymentStatusType` members (`PaymentComplete`, `PaymentDue`, `PaymentDeclined`, `PaymentPastDue`, `PaymentAutomaticallyApplied`) now `extends PaymentStatusType` instead of `extends StatusEnumeration` directly, matching the one-class-per-member convention used by `OrderStatus`'s members — `is_subclass_of(PaymentComplete::class, PaymentStatusType::class)` now returns `true`.

## [1.2.0] - 2026-06-26

### Added

- Adds the `xyz\oihana\schema\thesaurus` namespace and the `ThesaurusTerm` entity (extends the schema.org `DefinedTerm`) — a thesaurus term enriched with house-specific properties (e.g. `color`, a `#RRGGBB` hex string) layered on top of harvested data. These local properties survive a re-harvest because the harvest performs an AQL `UPSERT ... UPDATE` merge that only rewrites the source fields and leaves untouched attributes in place.
  - Adds the companion `xyz\oihana\schema\constants\traits\thesaurus\ThesaurusTermTrait` trait centralising the `ThesaurusTerm` property name constant (`COLOR`), and the domain-level `xyz\oihana\schema\constants\traits\ThesaurusTrait` aggregator (mirrors `AuthTrait`/`HttpTrait`). The aggregator is wired into `xyz\oihana\schema\constants\Oihana`, so the field is reachable via `Oihana::COLOR` (its value matches the `COLOR` keys already exposed by the auth traits).
  - Adds the `tests/xyz/oihana/schema/thesaurus` unit-test suite covering `ThesaurusTerm` defaults, constants and hydration.
- Adds the SKOS hierarchy layer to the `thesaurus` namespace, so the library can model parent/child concept trees that schema.org alone cannot express.
  - Adds `xyz\oihana\schema\thesaurus\Concept` (extends the schema.org `DefinedTerm`, equated by schema.org with `skos:Concept`) carrying the SKOS semantic relations through the new `xyz\oihana\schema\thesaurus\traits\HasSkosRelations` trait: `broader` (the direct parent, `null|string|array|Concept`), `narrower`, `broaderTransitive` and `narrowerTransitive` (`null|string|array`, hydrated element-by-element via `#[HydrateWith(Concept::class)]`). All relations are nullable and traversal-only — never persisted, never harvested, populated only on selected API responses. Each accepts a bare `_key` string, an AQL-projected associative array (rebuildable via `new Concept($array)`) or a hydrated object.
  - Adds `xyz\oihana\schema\thesaurus\ProductCategoryTerm` (extends `Concept`, `use HasColor`) — the only thesaurus family that is both hierarchical and colored; flat families stay on `ThesaurusTerm`.
  - Adds the companion `xyz\oihana\schema\constants\traits\thesaurus\ConceptTrait` (constants `BROADER`, `BROADER_TRANSITIVE`, `NARROWER`, `NARROWER_TRANSITIVE`), aggregated through `ThesaurusTrait` into `Oihana` (reachable as `Oihana::BROADER`, etc.).
  - Adds the `ConceptTest` and `ProductCategoryTermTest` suites, covering defaults, constants, the `Oihana` aggregation, and both hydration paths (raw constructor assignment vs `#[HydrateWith]` honored by `Reflection::hydrate()`).
- Extends the SKOS layer with concept schemes, associative relations and documentation notes.
  - Adds `xyz\oihana\schema\thesaurus\ConceptScheme` (extends the schema.org `DefinedTermSet`, equated by schema.org with `skos:ConceptScheme`) with `hasTopConcept` (`null|string|array`, hydrated via `#[HydrateWith(Concept::class)]`) — the scheme's root concepts, inverse of `Concept::$topConceptOf`. Scheme membership of a concept is already carried by the inherited `DefinedTerm::$inDefinedTermSet` (`skos:inScheme`).
  - Adds `related` (`skos:related`, associative link) to `HasSkosRelations`, plus `topConceptOf` (`null|string|array|ConceptScheme`) and `hiddenLabel` (search-only label) to `Concept`.
  - Adds the `xyz\oihana\schema\thesaurus\traits\HasSkosNotes` trait — the SKOS documentation notes `changeNote`, `editorialNote`, `example`, `historyNote`, `note` and `scopeNote` (`skos:definition`/`prefLabel`/`altLabel` are already covered by the inherited `description`/`name`/`alternateName`).
  - Adds the companion constant traits `SkosNotesTrait` and `ConceptSchemeTrait`, extends `ConceptTrait` (`HIDDEN_LABEL`, `RELATED`, `TOP_CONCEPT_OF`), and aggregates them all through `ThesaurusTrait` into `Oihana`.
  - Adds the `ConceptSchemeTest` suite and extends `ConceptTest` (relations, notes, label, the new constants and the `Oihana` aggregation, both hydration paths).
- Adds the SKOS mapping relations to `Concept`, to align a local concept with concepts in other schemes (e.g. a back-office category to an external taxonomy).
  - Adds the `xyz\oihana\schema\thesaurus\traits\HasSkosMappings` trait — `broadMatch`, `closeMatch`, `exactMatch`, `narrowMatch` and `relatedMatch` (`null|string|array`, hydrated via `#[HydrateWith(Concept::class)]`), ranging from the strongest claim (`exactMatch`) to the loosest (`relatedMatch`).
  - Adds the companion `SkosMappingsTrait` constants, aggregated through `ThesaurusTrait` into `Oihana`, and extends `ConceptTest` with the mapping constants, the `Oihana` aggregation and hydration.
  - With this, the `thesaurus` namespace covers the SKOS core: `Concept`/`ConceptScheme`, the hierarchy (`broader`/`narrower` and their transitive forms), the associative (`related`) and cross-scheme mapping relations, the labels (`name`/`alternateName`/`hiddenLabel`) and the documentation notes.
- Adds the bilingual wiki guide for the `xyz\oihana\schema\thesaurus` namespace (`oihana-thesaurus.md`, EN/FR) — when to use it, the traversal-only relations, code examples, the class catalog, a SKOS-coverage table and the property/constant traits. Lists it in the README `🗂️ Schemas overview` table and in both wiki indexes.
- Adds the SKOS collections — non-hierarchical groupings of concepts — completing the SKOS core coverage.
  - Adds `xyz\oihana\schema\thesaurus\Collection` (extends `Intangible`, like the schema.org `ItemList`) with a **polymorphic** `member` (`null|string|array`, `#[HydrateWith(Concept::class, Collection::class)]`): each entry dispatches on its `@type` discriminator, so a collection can hold concepts and/or nested collections (falling back to `Concept`).
  - Adds `xyz\oihana\schema\thesaurus\OrderedCollection` (extends `Collection`) with `memberList` for an explicitly ordered grouping.
  - Adds the companion `CollectionTrait` (`MEMBER`, `MEMBER_LIST`), aggregated through `ThesaurusTrait` into `Oihana`. Members stay nullable and traversal-only.
  - Adds the `CollectionTest` (incl. the polymorphic `@type` dispatch) and `OrderedCollectionTest` suites. SKOS-XL and the abstract super-properties (`semanticRelation`, `mappingRelation`) remain intentionally out of scope.
- Updates the bilingual `oihana-thesaurus.md` wiki guide (EN/FR) for the SKOS collections — adds `Collection`/`OrderedCollection` to the class catalog and the SKOS-coverage table, the `HasSkosMembers`/`CollectionTrait` rows, a polymorphic-`member` example, and bumps the namespace class count (4 → 6) in the README overview table and both wiki indexes.

### Changed

- Extracts the `ThesaurusTerm::$color` property into a reusable `xyz\oihana\schema\thesaurus\traits\HasColor` trait, so the color hint can be composed by several thesaurus entities without duplication. `ThesaurusTerm` now `use`s this trait — no change to its public surface (the `color` property and the `ThesaurusTerm::COLOR` constant are unchanged).

## [1.1.0] - 2026-06-23

### Added

- Adds the bilingual wiki guides for the `xyz\oihana\schema\business` (`oihana-business.md`) and `xyz\oihana\schema\http` (`oihana-http.md`) namespaces (EN/FR), and lists both in the README `🗂️ Schemas overview` table and the wiki indexes.
- Adds the `Docs` GitHub Actions workflow that builds the phpDocumentor site (`composer doc`) and deploys it to GitHub Pages on every push to `main`. The generated `docs/` output is no longer tracked in git (now gitignored), and `phpdoc.xml` is bumped to `1.1.0`. Uses the Node 24 majors of the Pages actions (`configure-pages@v6`, `upload-pages-artifact@v5`, `deploy-pages@v5`) to avoid the Node 20 deprecation warnings.
- Adds the `xyz\oihana\schema\business` namespace and the `BusinessIdentity` entity (extends `Intangible`) — the typed link between an authenticated account and a business entity (a `Person` or an `Organization`), exposed through a single `subject` property. Keeps the account and its linked entity decoupled (no data merging); `subject` is a resolved reference, never a copy. The identity type is **derived** from the subject (its Schema.org `additionalType`), not stored on the link. `subject` accepts a hydrated object (`Person` / `Organization` / `Thing`), a scalar reference, **or** a raw associative `array` (an AQL-projected reference) — the hydrator does not force a class, so a downstream project can subclass and override the property with `#[HydrateWith]` if it wants a fixed type.
  - Adds the companion `xyz\oihana\schema\constants\traits\business\BusinessIdentityTrait` trait centralising the `BusinessIdentity` property name constant (`SUBJECT`). It is intentionally **not** aggregated into `xyz\oihana\schema\constants\Oihana` because its `SUBJECT` key collides with the already-aggregated `auth\PermissionTrait::SUBJECT`.
  - Adds the `identities` property (array of `BusinessIdentity`, hydrated via `#[HydrateWith]`) to `xyz\oihana\schema\auth\User`, with the companion `UserTrait::IDENTITIES` constant. An account may hold several identities (e.g. both a seller and a customer contact).
  - Adds neutral read accessors so consumers can navigate the link without re-implementing the lookup logic : `BusinessIdentity::subjectType()` (the subject's `additionalType`), `BusinessIdentity::isType()` (type test), `BusinessIdentity::subjectKey()` and `BusinessIdentity::worksForKey()` (resolve the `_key`/`id` of the subject, or of the organization it works for), plus `User::firstIdentityBySubjectType()` and `User::identitiesBySubjectType()` to filter the account's identities by subject type. Every accessor handles a `subject` that is either a hydrated object or a raw associative array.
  - Adds the `tests/xyz/oihana/schema/business` unit-test suite covering `BusinessIdentity` and the new `User::$identities` field.
  - Adds the `UserProfile` entity (extends `Intangible`) — a creation-time template that pairs an authorization `role` with the `expectedType` (`additionalType`) of the person the account will be linked to, plus a `color` UI hint. It carries no per-account state : it is consumed once, at creation time, to grant a role and attach a business identity. `role` is a resolved reference accepting a hydrated `Role` object, a scalar role key / name, **or** a raw associative `array` (an AQL-projected reference).
  - Adds the companion `xyz\oihana\schema\constants\traits\business\UserProfileTrait` trait centralising the `UserProfile` property name constants (`COLOR`, `EXPECTED_TYPE`, `ROLE`). Like `BusinessIdentityTrait`, it is intentionally **not** aggregated into `xyz\oihana\schema\constants\Oihana`; both are composed into the `xyz\oihana\schema\constants\traits\BusinessTrait` aggregator instead.
  - Adds the `tests/xyz/oihana/schema/business/UserProfileTest` unit-test suite covering the `UserProfile` defaults, constants and hydration.
- Adds the `xyz\oihana\schema\http` namespace and the `UserAgentInfo` DTO (extends `Intangible`) — structured view of an HTTP `User-Agent` header with `browser`, `browserVersion`, `os`, `osVersion`, `deviceType`, `isBot` and `raw` properties. Designed to be populated by the parsing helpers in `oihana/php-http` and to be embedded in `Session` / `AuditAction` records.
  - Adds the companion `xyz\oihana\schema\constants\traits\http\UserAgentInfoTrait` trait centralising the property name constants.
  - Adds the `xyz\oihana\schema\constants\http\DeviceType` constant class (`bot`, `desktop`, `mobile`, `tablet`, `unknown`) used by `UserAgentInfo::$deviceType`.
  - Adds the domain-level `xyz\oihana\schema\constants\traits\HttpTrait` aggregator (mirrors `AuthTrait`'s pattern) and wires it into `xyz\oihana\schema\constants\Oihana`, so the new HTTP field constants are reachable via `Oihana::BROWSER`, `Oihana::DEVICE_TYPE`, etc.
- Adds the com\progress\schema namespace — object-oriented mapping of the OpenEdge Progress SQL system catalog tables (~16 classes under com\progress\schema\system)
  - Adds Table (SYSTABLES), Column (SYSCOLUMNS) and Index (SYSINDEXES) — refactored with full PHPDoc, corrected `Column::$columnType` typing (previously mislabelled as the table type discriminator), and added the missing Progress columns (`columnId`, `precision`, `radix`, `format`, `label`, `mandatory`, `caseSensitive`, `decimal`, `numberOfRows`, `percentTouched`, `recordSize`, `tableAttributes`, `updateStats`, `ascDesc`, `fieldNumber`, `indexOwner`, `indexSequence`, `numberOfComponents`, `primary`, `unique`)
  - Adds the View class (SYSVIEWS — checkOption, textLength, viewText)
  - Adds the User class (SYSDBAUTH — grantee, grantor, dbaAccess, resourceAccess — de facto OpenEdge SQL user list)
  - Adds the Sequence class (SYSSEQUENCES — cycle, increment, initialValue, minValue, maxValue, sequenceOwner)
  - Adds the Synonym class (SYSSYNONYMS — baseTable, baseTableOwner, synonymOwner)
  - Adds the Procedure class (SYSPROCEDURES — procedureId, procedureOwner, numberOfArguments, returnType, remarks, procedureText)
  - Adds the Trigger class (SYSTRIGGER — event I/U/D, forEach R/S, timing B/A, triggerOwner, triggerText)
  - Adds the TableConstraint, CheckConstraint, ReferentialConstraint and KeyColumnUsage classes (SYS_TBL_CONSTRS, SYS_CHK_CONSTRS, SYS_REF_CONSTRS, SYS_KEYCOL_USAGE — full constraint metadata including matchType, updateRule, deleteRule, keySequence, deferrability)
  - Adds the TableAuth and ColumnAuth classes (SYSTABAUTH, SYSCOLAUTH — per-table and per-column GRANT flags: select, insert, update, delete, references, index, alter)
  - Adds the DataType class (SYSDATATYPES — typeCode, columnLength, dataTypePrecision, dataTypeRadix)
  - Adds the com\progress\schema\constants\Progress aggregator class with `Progress::SCHEMA = 'https://schema.progress.com'`
  - Adds 13 specialized constant traits (Authorization, Column, Common, Constraint, DataType, Index, Procedure, Sequence, Synonym, Table, Trigger, User, View) composed into a single Properties trait
  - Registers the PSR-4 autoload entry `com\progress\\` → `src/com/progress`
  - Adds the tests/com/progress unit-test suite (17 test classes, ~80 tests, 317 assertions) covering every system class and the Progress constants aggregator
- Adds a hand-written bilingual EN/FR wiki under `wiki/` complementing the phpDocumentor API reference
  - Adds wiki/en and wiki/fr language folders with reciprocal cross-links, getting-started guides (`getting-started.md` / `demarrage.md`) and per-namespace guides for `xyz\oihana\schema\auth`, `xyz\oihana\schema\places`, `xyz\oihana\schema` (cross-cutting types) and `com\progress\schema`
  - Splits the `org\schema` guide under `schema-org/` into one sub-page per sub-namespace (`core`, `actions`, `creative-work`, `events`, `places`, `organizations`, `services`, `items`, `enumerations`)
- Adds a `🗂️ Schemas overview` section in README.md with a summary table of all top-level namespaces and `@context` URIs, each row linking to the corresponding bilingual wiki guide
- Adds the SchemaResolver helper class.
- Adds the xyz\oihana\schema\auth namespace
  - Adds the WebApi (extends the schema.org definition), Permission, Role and User classes
  - Adds the Application class (OAuth2/PKCE/M2M client with permissions, IP whitelist, expiration)
  - Adds the Keyfile class (self-sufficient PRIVATE_KEY_JWT keyfile for M2M clients — IdP material `key`, `keyId`, `userId`, `clientId`, `type` plus connection metadata `issuer`, `audience`, `scope`, `apiBaseUrl`)
  - Extends Application with keyId and keyfile properties (active key id and one-shot keyfile payload returned on creation/rotation)
  - Adds the Invitation class (extends Schema.org InviteAction, tracks email invitation lifecycle)
  - Adds the PasswordReset class (extends Schema.org UpdateAction — password-reset request lifecycle: token hash, email, redirectUrl, sentAt ; action-status pending / consumed / expired / cancelled)
  - Adds the PendingRevocation class (provider-agnostic deferred revocation queue — attempts, lastAttemptAt, lastError, provider, reason, targetId, targetType, userIdentifier, userKey) for retrying failed IdP revocations (Zitadel, Magento, Auth0)
  - Adds the OAuthClient class (Zitadel client mirror, resolves opaque clientId to human-readable label)
  - Adds the Policy class (RBAC authorization bundle for M2M applications, with applications, color, permissions, protected, roles and system properties)
  - Adds the Policy::toPolicy() and Policy::toCasbinPolicy() methods (Casbin-ready policy entries from attached permissions)
  - Adds the Role::toCasbinPolicy() and Permission::toCasbinPolicy() aliases of the existing toPolicy() methods
  - Extends Application with createdBy, disabledAt, disabledBy, disabledReason, lastSeenIP, policies and policiesCount properties
  - Adds the Service class (machine identity / Service Account backed by a Zitadel Machine User — JWT private_key_jwt RFC 7523 ; clientId, keyId, keyfile, allowedIPs, expiresAt, lastSeenIP, lastUsedAt, permissions, policies, protected and disabled* audit fields)
  - Adds the Session class (tracks active connections with IP, user-agent, token hash, expiration, revocation) + the SessionRevocationReason constants
  - Extends SessionRevocationReason with the TOKENS_REVOKED constant (surfaced by the auth middleware when a token `iat` predates User::$tokensInvalidBefore)
  - Extends SessionRevocationReason with the USER_REVOKED constant (session explicitly invalidated by the user, e.g. "Sign out from other devices" — distinct from LOGOUT which terminates the current authenticated context)
  - Extends SessionRevocationReason with the EMERGENCY_REVOKE constant (session terminated as part of an emergency security response — incident response, confirmed compromise, automated threat-mitigation workflow)
  - Extends SessionRevocationReason with the ORPHANED constant (session no longer references a valid owning entity — surfaced by background cleanup jobs and referential-integrity sweeps, distinct from USER_DELETED which records an intentional user-deletion event)
  - Extends Role with color, default, level, policies, policiesCount, protected, system properties
  - Extends User with activated, appMetadata, applications, blockedFor, devices, firstLoginAt,maxLevel, pendingEmail, pendingEmailSince, signedUp and metadata properties
  - Extends User with invitationStatus and status properties (admin lifecycle gating and invitation projection)
  - Extends User with pendingEmailCodeExpiresAt and pendingEmailCodeHash properties (verification code lifecycle for email change flow)
  - Extends User with color, protected and system properties (admin display color and write/delete protection flags, provided by ProtectedResourceTrait)
  - Extends User with tokensInvalidBefore property (epoch-seconds cutoff used by the auth middleware to reject access tokens whose `iat` predates a bulk session revocation)
- Adds the JWTAlgorithm constant class
- Adds the JwtClaim constant class — full IANA JSON Web Token Claims registry coverage:
  - RFC 7519 §4.1 registered claims exposed under both short and long aliases (`ISS`/`ISSUER`, `SUB`/`SUBJECT`, `AUD`/`AUDIENCE`, `EXP`/`EXPIRES_AT`, `NBF`/`NOT_BEFORE`, `IAT`/`ISSUED_AT`, `JTI`/`JWT_ID`)
  - OAuth 2.0 / OIDC common claims (`azp`, `nonce`, `auth_time`, `acr`, `amr`, `scope`, `scp`, `client_id`)
  - OIDC Session Management (`sid` / `SESSION_ID` — Front-Channel / Back-Channel Logout)
  - OIDC ID Token validation hashes (`at_hash`, `c_hash`)
  - OIDC Core §5.1 standard profile claims (`name`, `given_name`, `family_name`, `middle_name`, `nickname`, `preferred_username`, `profile`, `picture`, `website`, `email`, `email_verified`, `gender`, `birthdate`, `zoneinfo`, `locale`, `phone_number`, `phone_number_verified`, `address`, `updated_at`)
  - RFC 8693 Token Exchange (`act`, `may_act`)
  - RFC 7800 Proof-of-Possession (`cnf`)
  - Provider-specific / non-standard (`groups`, `roles`, `entitlements`, `tid`, `oid`)
- Adds the InvitationStatus constant class (none, pending, accepted, expired, canceled — user-side projection of the latest invitation lifecycle)
- Adds the UserStatus constant class (active, disabled — admin-controlled login gating, distinct from the immutable activated flag)
- Adds the xyz\oihana\schema\constants\auth namespace
  - Adds the KeyfileType constant class (application, serviceaccount — IdP-emitted keyfile types, backed by ConstantsTrait)
  - Adds the TokenRequestField constant class (OAuth2 token endpoint form fields — assertion, client_assertion, client_assertion_type, grant_type, scope)
  - Adds the TokenRequestValue constant class (canonical values paired with TokenRequestField — DEFAULT_SCOPE, GRANT_CLIENT_CREDENTIALS, GRANT_JWT_BEARER, JWT_BEARER_ASSERTION_TYPE)
  - Adds the TokenResponseField constant class (RFC 6749 §5.1 successful response fields, plus OIDC and vendor extensions — access_token, expires_at, expires_in, id_token, refresh_token, scope, token_type)
- Adds ItemAvailability
- Adds the PostalAddress::extendedAddress property (new standard property in https://schema.org/PostalAddress)
- Adds the xyz\oihana\schema\places namespace
  - Adds the Site, JobSite, Office, Warehouse classes
- Adds the xyz\oihana\schema\auth\WebApplication class
- Adds the org\schema\actions namespace with the full Schema.org Action type hierarchy (~115 action classes)
- Adds the JSONSerializer tool and integrates it in ThingTrait::jsonSerialize
- Adds the ThingTrait::getReduceOptions method
- Adds the Offer::provider property
- Adds role fields and WebApplication trait in the auth namespace
- Adds the xyz\oihana\schema\constants\traits\auth namespace with property-name traits:
  - ApplicationTrait, InvitationTrait, KeyfileTrait, OAuthClientTrait, PasswordResetTrait, PendingRevocationTrait, PolicyTrait, ServiceTrait, SessionTrait
  - Extends ApplicationTrait with keyId and keyfile constants
  - Adds the shared property traits: ClientIdTrait (clientId), ProtectedResourceTrait (color, protected, system)
  - Adds the plural collection traits: ApplicationsTrait, PermissionsTrait, PoliciesTrait, RolesTrait, ServicesTrait, UsersTrait
  - Composes ServicesTrait into PolicyTrait and UserTrait (services and servicesCount constants now reachable through Policy and User)
  - Extends RoleTrait with default, level, policies, policiesCount constants (color, protected, system now provided by ProtectedResourceTrait)
  - Extends UserTrait with activated, appMetadata, applications, blockedFor, devices, firstLoginAt, metadata, signedUp constants
  - Extends UserTrait with invitationStatus and status constants
  - Extends UserTrait with pendingEmailCodeExpiresAt and pendingEmailCodeHash constants
  - Extends UserTrait with tokensInvalidBefore constant (paired with the new User::$tokensInvalidBefore property)
  - Extends KeyfileTrait with apiBaseUrl, audience, issuer, scope and userId constants (removes the now-redundant APP_ID — the Keyfile property still exists, but the constant is provided by OAuthClientTrait)
- Composes the new auth traits into the AuthTrait aggregator
- Adds the xyz\oihana\schema\AuditAction class (auditable action with request tracking and RGPD-compliant logging)
- Extends AuditAction with event and outcome properties (business event tag and machine-readable result of the action)
- Adds the xyz\oihana\schema\enumerations\AuditActionType enumeration (CREATE, UPDATE, DELETE, ADD, LOGIN, LOGOUT, REJECT)
- Adds the xyz\oihana\schema\constants\traits\AuditTrait with AuditAction property constants
- Extends AuditTrait with event and outcome constants
- Adds the AuditTrait in the Oihana constants class

### Changed

- `SchemaResolver` now accepts a **list** of types for the discriminator (multi-typed documents), resolved by **map declaration order** (consistent with `php-arango`'s `FederatedSearch`), falling back to the default; scalar resolution unchanged.
- ThingTrait::jsonSerialize now returns all null properties by default (no compression)
- Refactors the ThingTrait::toArray implementation (removes the $class parameter)
- Refactors ApplicationTrait, OAuthClientTrait, SessionTrait and WebApplicationTrait to consume the shared ClientIdTrait
- Refactors PolicyTrait, RoleTrait and UserTrait to consume the shared ProtectedResourceTrait
- Extracts the `$color`, `$protected` and `$system` properties into the new HasProtectedResource property trait (xyz\oihana\schema\auth\traits) — applied to Permission, User and WebAPI (inherited by Policy and Role), removing the duplicated inline declarations
- Refactors PermissionTrait and WebAPITrait to consume the shared ProtectedResourceTrait (exposes COLOR, PROTECTED, SYSTEM constants for consistency with PolicyTrait, RoleTrait and UserTrait)
- Extends Permission with color, protected and system properties (admin display color and write/delete protection flags, provided by HasProtectedResource)
- Extends Policy with services and servicesCount properties (inbound services referencing this policy)

### Fixed

- Fixes `DataFeed` and `DataCatalog` referencing their related type as `DataSet` while the actual class is `Dataset` (the schema.org casing, https://schema.org/Dataset). On a case-sensitive filesystem (e.g. Linux CI) the PSR-4 autoloader could not resolve `DataSet.php`, making `DataFeed` a fatal load error. References now match the real class name.
- Fixes `ProductCollection` being impossible to instantiate (fatal error): it extends `Product` (which declares `$funding` as `null|string|array|Grant`) while also using `CreativeWorkTrait`, whose `$funding` was the incompatible `null|Grant|array`. The trait property is now `null|string|array|Grant`, so the composition is valid.
- Fixes the areaServed property type to accept integer values
- Fixes Role::toPolicy() crashing when the permissions property is uninitialized
- Fixes PermissionTrait::NAME constant value (was incorrectly set to 'domain' instead of 'name')
- Fixes Application using the plural ApplicationsTrait (collection constants APPLICATIONS, APPLICATIONS_COUNT) instead of the singular ApplicationTrait — Application::ALLOWED_IPS, KEY_ID, KEYFILE, etc. now resolve correctly
- Fixes SessionRevocationReason::USER_DELETED value (was incorrectly set to 'user_disabled', now resolves to 'user_deleted')

### Removed

- Removes the Scope class and ScopeTrait (replaced by Policy)
- Removes the ApplicationTemplate class and ApplicationTemplateTrait (Application lifecycle simplified)

## [1.0.1] - 2025-10-30

### Added

- Adds xyz\oihana\schema (new package)
- Adds xyz\oihana\schema\Log
- Adds xyz\oihana\schema\Pagination

- Adds the org\schema\DublinCore class

- Adds the org\schema\constants\Schema constant

## [1.0.0] - 2025-06-17

### Added

- Adds schema\ folder with all first classes and helpers
