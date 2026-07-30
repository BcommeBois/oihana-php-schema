# Getting started

This guide walks you through the four pillars of **Oihana PHP Schema**:

1. installing the library,
2. instantiating and hydrating a `Thing`,
3. serializing it to JSON-LD,
4. using property constants for safer code.

> 🇫🇷 Cette page existe aussi en [français](../fr/demarrage.md).

---

## 1. Installation

Oihana PHP Schema requires **PHP 8.4 or newer**. Install it with [Composer](https://getcomposer.org):

```bash
composer require oihana/php-schema
```

The library depends on `oihana/php-core` and `oihana/php-reflect`. Both are currently distributed as `dev-main`, so if your root project enforces stable versions you may need to add the following to your `composer.json`:

```json
{
    "minimum-stability": "dev",
    "prefer-stable": true
}
```

Once installed, three top-level namespaces are autoloaded:

| Namespace        | What it contains                                              |
|------------------|---------------------------------------------------------------|
| `org\schema`     | Strongly-typed PHP implementation of the Schema.org vocabulary. |
| `xyz\oihana`     | Oihana-specific extensions (auth, pagination, places, …).     |
| `com\progress`   | OpenEdge Progress SQL system catalog mapping (tables, columns, indexes, users, …). |

---

## 2. The `Thing` base class

Every entity exposed by the library extends `org\schema\Thing`. `Thing` provides:

- the common Schema.org metadata properties (`id`, `name`, `url`, `description`, `owner`, `image`, `sameAs`, …),
- ArangoDB-friendly metadata fields (`_id`, `_key`, `_rev`, `_from`, `_to`) for graph storage,
- a JSON-LD–aware `jsonSerialize()` implementation,
- a constructor that accepts an associative array and copies the values into public properties.

A minimal example:

```php
use org\schema\Thing;

$thing = new Thing
([
    'name' => 'Hello World',
    'url'  => 'https://example.com',
]);

echo $thing->name; // Hello World
```

Because every property is `public`, you can also set them directly:

```php
$thing       = new Thing();
$thing->name = 'Hello World';
```

---

## 3. Hydration: from arrays to value objects

### 3.1 Shallow hydration via the constructor

The constructor inherited from `ThingTrait` performs a **shallow** copy: it reads keys from the supplied array and writes them onto matching public properties, ignoring unknown keys.

```php
use org\schema\Person;

$person = new Person
([
    'name'  => 'Alice',
    'email' => 'alice@example.com',
]);
```

### 3.2 Recursive hydration via reflection

For deep object graphs (nested entities, union types, arrays of objects) you can use the reflection-based `hydrate()` utility provided by [`oihana/php-reflect`](https://github.com/BcommeBois/oihana-php-reflect):

```php
use oihana\reflect\Reflection;
use org\schema\Person;

$person = new Reflection()->hydrate
(
    [
        'name'    => 'Alice',
        'address' =>
        [
            'streetAddress' => '2 chemin des Vergers',
            'postalCode'    => '49170',
        ],
    ],
    Person::class
);

// $person->address is now a fully-typed PostalAddress instance.
```

`hydrate()` walks the public properties of the target class and instantiates nested value objects as it goes. It is an **instance method**: calling `Reflection::hydrate(...)` statically throws an `Error`. Keep the instance around when hydrating in a loop — it caches the hydration plan of each class.

### 3.3 Union types: picking the target class with `#[HydrateWith]`

When a property declares a union of classes (`Organization|Person`, `Product|Service`...), the type alone does not say which one to instantiate: reflection then keeps **the first declared member**, whatever the payload says. A `Person` customer would come out as a half-empty `Organization`.

The `#[HydrateWith]` attribute settles it by listing the candidate classes:

```php
use oihana\reflect\attributes\HydrateWith;

#[HydrateWith(Organization::class, Person::class)]
public null|array|Organization|Person $customer ;
```

The hydrator then picks:

1. from the payload's **discriminator** — `@type`, `atType` or `type` — matched against each candidate's **short or fully-qualified name** (so `'@type' => 'Person'` is enough, no namespace needed);
2. failing that, from the **properties present** in the payload (match score);
3. as a last resort, **the first candidate in the list** — put the most common case there.

The library's affected properties already carry it (`customer`, `seller`, `author`, `broker`, `provider`, the line `item`s, `Site::$ownedBy`).

The attribute only applies to **array** values; anything else is left as-is, **provided the declared type accepts it**. `Site::$ownedBy` declares `int|string` in its union and therefore accepts a raw identifier in place of the object, whereas `customer`/`seller` do not declare `string`: passing them a raw identifier raises a `HydrationException`. That follows from the declared type, not from the attribute — a property without `#[HydrateWith]` behaves the same way.

### 3.4 Overriding the target from your own project

You can aim at **your own classes** by extending and **redeclaring the property with the same type**, carrying a new attribute:

```php
use oihana\reflect\attributes\HydrateWith;
use org\schema\Organization;
use org\schema\Person;
use xyz\oihana\schema\business\documents\BusinessDocument;

class MyDocument extends BusinessDocument
{
    // Same type as the parent; only the candidate classes change.
    #[HydrateWith( MyCustomer::class , MyContact::class )]
    public null|array|Organization|Person $customer ;
}
```

```php
$doc = new Reflection()->hydrate( [ 'customer' => [ '@type' => 'MyCustomer' , 'name' => 'ACME' ] ] , MyDocument::class ) ;
$doc->customer instanceof MyCustomer ; // true
```

Things to keep in mind:

- **the type must stay strictly identical** to the parent's — PHP enforces property type invariance. Narrowing it (`public null|array|MyCustomer $customer`) raises a *fatal error at class load*: `Type of MyDocument::$customer must be org\schema\Organization|org\schema\Person|array|null`. This is no hindrance: since `MyCustomer` is an `Organization`, the parent type already accepts it — only the attribute changes;
- the parent class is unaffected, and properties you do not redeclare keep the inherited attribute;
- overriding **stacks all the way down**: a document can point at your own lines (`#[HydrateWith(MyLine::class)]` on `documentLines`), which in turn point at your own products, and the whole graph hydrates into your classes — including their own properties.

---

## 4. JSON-LD serialization

`Thing` implements `JsonSerializable`, so `json_encode()` produces valid JSON-LD out of the box. Two synthetic keys are injected automatically:

- `@type`    — the short class name of the entity.
- `@context` — the constant `Thing::CONTEXT` (defaults to `https://schema.org`; subclasses can override it, e.g. `Pagination::CONTEXT = 'https://schema.oihana.xyz'`).

Null properties are stripped from the output, keeping payloads compact.

```php
use org\schema\Person;
use org\schema\PostalAddress;

$person = new Person
([
    'id'      => '2555',
    'name'    => 'John Doe',
    'address' => new PostalAddress
    ([
        'streetAddress' => '2 chemin des Vergers',
        'postalCode'    => '49170',
    ]),
]);

echo json_encode( $person , JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
```

Output:

```json
{
    "@type": "Person",
    "@context": "https://schema.org",
    "id": "2555",
    "name": "John Doe",
    "address": {
        "@type": "PostalAddress",
        "@context": "https://schema.org",
        "streetAddress": "2 chemin des Vergers",
        "postalCode": "49170"
    }
}
```

---

## 5. Safe property access with constants

Hard-coding property names as strings is error-prone. The library ships a `Schema` aggregator class that exposes every Schema.org property as a typed `public const string`:

```php
use org\schema\Person;
use org\schema\PostalAddress;
use org\schema\constants\Schema;

$person = new Person
([
    Schema::ID      => '2555',
    Schema::NAME    => 'John Doe',
    Schema::ADDRESS => new PostalAddress
    ([
        Schema::STREET_ADDRESS => '2 chemin des Vergers',
        Schema::POSTAL_CODE    => '49170',
    ]),
]);
```

Constants are grouped into per-type traits under `org\schema\constants\traits` (`Thing`, `Person`, `Event`, …) and composed into the `Schema` class — IDE auto-completion is therefore always topic-aware.

The same pattern is used by every extension namespace:

- `xyz\oihana\schema\constants\Oihana` — Oihana-specific extensions.
- `com\progress\schema\constants\Progress` — Progress OpenEdge SQL catalog.

---

## 6. Where to go next

- 📖 Browse the full [API reference](../../docs) generated by phpDocumentor.
- 🇫🇷 Explore the `xyz\oihana` namespace for domain-specific extensions (pagination, auth, places, …) — see [`README.md`](../../README.md#-xyzoihana-namespace-oihanaxyz-extensions).
- 🗄️ Map an OpenEdge database with the `com\progress` namespace — `Table`, `Column`, `Index`, `User`, `View`, `TableAuth`, `Sequence`, `Trigger`, constraints and more.
- 🧮 Generate JSON Schemas from your typed classes:

  ```bash
  composer schemas:all
  ```

- ✅ Run the test suite to verify your setup:

  ```bash
  composer test
  ```
