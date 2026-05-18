# Démarrage rapide

Ce guide présente les quatre piliers d'**Oihana PHP Schema** :

1. installation de la bibliothèque,
2. instanciation et hydratation d'un `Thing`,
3. sérialisation JSON-LD,
4. utilisation des constantes de propriétés pour écrire du code plus sûr.

> 🇬🇧 This page is also available in [English](../en/getting-started.md).

---

## 1. Installation

Oihana PHP Schema requiert **PHP 8.4 ou plus récent**. Installez-la via [Composer](https://getcomposer.org) :

```bash
composer require oihana/php-schema
```

La bibliothèque dépend de `oihana/php-core` et `oihana/php-reflect`. Ces deux paquets sont actuellement distribués en `dev-main` ; si votre projet racine exige des versions stables, ajoutez ceci à votre `composer.json` :

```json
{
    "minimum-stability": "dev",
    "prefer-stable": true
}
```

Une fois installée, trois namespaces de premier niveau sont autoloadés :

| Namespace        | Contenu                                                       |
|------------------|---------------------------------------------------------------|
| `org\schema`     | Implémentation PHP typée du vocabulaire Schema.org.           |
| `xyz\oihana`     | Extensions spécifiques à Oihana (auth, pagination, lieux, …). |
| `com\progress`   | Mapping du catalogue système OpenEdge Progress SQL (tables, colonnes, index, utilisateurs, …). |

---

## 2. La classe de base `Thing`

Toutes les entités exposées par la bibliothèque étendent `org\schema\Thing`. `Thing` fournit :

- les propriétés communes de Schema.org (`id`, `name`, `url`, `description`, `owner`, `image`, `sameAs`, …),
- des champs ArangoDB pour le stockage en graphe (`_id`, `_key`, `_rev`, `_from`, `_to`),
- une implémentation de `jsonSerialize()` consciente du JSON-LD,
- un constructeur qui accepte un tableau associatif et recopie les valeurs dans les propriétés publiques.

Exemple minimal :

```php
use org\schema\Thing;

$thing = new Thing
([
    'name' => 'Hello World',
    'url'  => 'https://example.com',
]);

echo $thing->name; // Hello World
```

Comme toutes les propriétés sont `public`, vous pouvez aussi les affecter directement :

```php
$thing       = new Thing();
$thing->name = 'Hello World';
```

---

## 3. Hydratation : du tableau aux objets

### 3.1 Hydratation superficielle via le constructeur

Le constructeur hérité de `ThingTrait` effectue une copie **superficielle** : il lit les clés du tableau fourni et les écrit sur les propriétés publiques correspondantes en ignorant les clés inconnues.

```php
use org\schema\Person;

$person = new Person
([
    'name'  => 'Alice',
    'email' => 'alice@example.com',
]);
```

### 3.2 Hydratation récursive par réflexion

Pour des graphes profonds (entités imbriquées, types union, tableaux d'objets), utilisez l'utilitaire `hydrate()` basé sur la réflexion et fourni par [`oihana/php-reflect`](https://github.com/BcommeBois/oihana-php-reflect) :

```php
use oihana\reflect\Reflection;
use org\schema\Person;

$person = Reflection::hydrate
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

// $person->address est désormais une instance de PostalAddress complètement typée.
```

`hydrate()` parcourt les propriétés publiques de la classe cible, résout les types union et instancie les objets imbriqués au fil de l'eau.

---

## 4. Sérialisation JSON-LD

`Thing` implémente `JsonSerializable`, donc `json_encode()` produit du JSON-LD valide nativement. Deux clés synthétiques sont injectées automatiquement :

- `@type`    — le nom court de la classe de l'entité.
- `@context` — la constante `Thing::CONTEXT` (par défaut `https://schema.org` ; les sous-classes peuvent la redéfinir, par exemple `Pagination::CONTEXT = 'https://schema.oihana.xyz'`).

Les propriétés à `null` sont retirées de la sortie pour produire une charge utile compacte.

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

Sortie :

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

## 5. Accès sécurisé aux propriétés via les constantes

Coder en dur le nom des propriétés sous forme de chaînes est risqué. La bibliothèque fournit une classe agrégatrice `Schema` qui expose chaque propriété Schema.org en tant que `public const string` typée :

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

Les constantes sont organisées par type dans des traits sous `org\schema\constants\traits` (`Thing`, `Person`, `Event`, …) puis composées dans la classe `Schema` — l'auto-complétion de l'IDE est donc toujours pertinente.

Le même schéma s'applique à chaque namespace d'extension :

- `xyz\oihana\schema\constants\Oihana` — extensions Oihana.
- `com\progress\schema\constants\Progress` — catalogue système Progress OpenEdge SQL.

---

## 6. Pour aller plus loin

- 📖 Parcourez la [référence d'API](../../docs) complète générée par phpDocumentor.
- 🇫🇷 Explorez le namespace `xyz\oihana` pour les extensions métier (pagination, auth, lieux, …) — voir [`README.md`](../../README.md#-xyzoihana-namespace-oihanaxyz-extensions).
- 🗄️ Modélisez une base OpenEdge avec le namespace `com\progress` — `Table`, `Column`, `Index`, `User`, `View`, `TableAuth`, `Sequence`, `Trigger`, contraintes et plus.
- 🧮 Générez des JSON Schemas depuis vos classes typées :

  ```bash
  composer schemas:all
  ```

- ✅ Lancez la suite de tests pour vérifier votre installation :

  ```bash
  composer test
  ```
