# `xyz\oihana\schema\business` — Oihana business

The `xyz\oihana\schema\business` namespace models the **link between an authenticated account and the business world** : *who* an account is in business terms (`BusinessIdentity`), and the reusable *template* used to provision a new account (`UserProfile`).

> 🇫🇷 Cette page existe aussi en [français](../fr/oihana-business.md).

---

## When to use it

Pick these classes when you need to connect the **auth axis** (roles, permissions — see [`xyz\oihana\schema\auth`](oihana-auth.md)) with the **identity axis** (the `Person` or `Organization` the account stands for) without merging their data:

- a *BusinessIdentity* to answer *"which business entity does this account correspond to?"* — an account may hold several (e.g. both a seller and a customer contact),
- a *UserProfile* as a creation-time blueprint that pairs an authorization role with the kind of entity the account is expected to be linked to.

Both classes extend `org\schema\Intangible` : they qualify an account, they are not independently addressable resources. They expose the `@context = 'https://schema.oihana.xyz'` distinguisher in the JSON-LD output.

---

## Quick example

```php
use org\schema\Person;
use xyz\oihana\schema\business\BusinessIdentity;

$identity = new BusinessIdentity
([
    BusinessIdentity::SUBJECT => new Person
    ([
        '_key'           => '94565' ,
        'additionalType' => 'Seller' ,
    ]),
]);

$identity->subjectKey() ;       // '94565'
$identity->subjectType() ;      // 'Seller'
$identity->isType( 'Seller' ) ; // true
```

```php
use xyz\oihana\schema\business\UserProfile;

$profile = new UserProfile
([
    'name'                     => 'Commercial' ,
    UserProfile::COLOR         => '#22C55E' ,           // UI hint
    UserProfile::ROLE          => 'seller' ,            // auth Role to grant
    UserProfile::EXPECTED_TYPE => 'Seller' ,            // expected Person additionalType
]);
```

The `subject` of a `BusinessIdentity` and the `role` of a `UserProfile` are **resolved references** : each accepts a hydrated object, a scalar reference (key / name), **or** a raw associative `array` (an AQL-projected document) — never a forced class.

---

## Class catalog

| Class             | Extends      | Purpose                                                                                                                       |
|-------------------|--------------|-------------------------------------------------------------------------------------------------------------------------------|
| `BusinessIdentity` | `Intangible` | Typed link between an account and a business entity (`Person` / `Organization`), exposed through a single `subject`. The identity type is **derived** from the subject's `additionalType`, never stored on the link. |
| `UserProfile`      | `Intangible` | Creation-time template pairing a `role` with the `expectedType` of the person the account will be linked to, plus a `color` UI hint. Carries no per-account state. |

### `BusinessIdentity` read accessors

Neutral helpers so consumers navigate the link without re-implementing the lookup — each tolerates a `subject` that is a hydrated object **or** a raw associative array:

- `subjectType()` — the subject's Schema.org `additionalType` (string or array).
- `isType( string $type )` — type test (strict equality, or membership when `additionalType` is an array).
- `subjectKey( string|array $key = '_key' )` — resolve the subject's identifier.
- `worksForKey( string|array $key = '_key' )` — resolve the `_key`/`id` of the organization the subject `worksFor`.

On the account side, [`User`](oihana-auth.md) exposes `firstIdentityBySubjectType()` and `identitiesBySubjectType()` to filter its `identities` by subject type.

For exhaustive property lists, browse the source under [`src/xyz/oihana/schema/business/`](../../src/xyz/oihana/schema/business) or the [API reference](../../docs).

---

## Related constants

Property keys are exposed by two traits — [`BusinessIdentityTrait`](../../src/xyz/oihana/schema/constants/traits/business/BusinessIdentityTrait.php) (`SUBJECT`) and [`UserProfileTrait`](../../src/xyz/oihana/schema/constants/traits/business/UserProfileTrait.php) (`COLOR`, `EXPECTED_TYPE`, `ROLE`).

Unlike the `places` and `http` traits, these are **intentionally not** aggregated into the master [`Oihana`](../../src/xyz/oihana/schema/constants/Oihana.php) class : `BusinessIdentityTrait::SUBJECT` would collide with the already-aggregated `auth\PermissionTrait::SUBJECT`. Both are composed into the [`BusinessTrait`](../../src/xyz/oihana/schema/constants/traits/BusinessTrait.php) aggregator instead, and reached through the class constants directly (e.g. `UserProfile::ROLE`, `BusinessIdentity::SUBJECT`).

---

## Related reading

- [`xyz\oihana\schema\auth`](oihana-auth.md) — roles, permissions, `User::$identities`.
- [`org\schema`](schema-org/README.md) — `Person`, `Organization`, `Intangible`.
- [Getting started](getting-started.md) — installation, hydration, JSON-LD basics.
- [API reference](../../docs).
