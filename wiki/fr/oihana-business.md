# `xyz\oihana\schema\business` — Métier Oihana

Le namespace `xyz\oihana\schema\business` modélise le **lien entre un compte authentifié et le monde métier** : *qui* est un compte en termes métier (`BusinessIdentity`), et le *gabarit* réutilisable servant à provisionner un nouveau compte (`UserProfile`).

> 🇬🇧 This page is also available in [English](../en/oihana-business.md).

---

## Quand l'utiliser

Choisissez ces classes lorsque vous devez relier l'**axe authentification** (rôles, permissions — voir [`xyz\oihana\schema\auth`](oihana-auth.md)) à l'**axe identité** (la `Person` ou l'`Organization` que le compte représente) sans fusionner leurs données :

- une *BusinessIdentity* pour répondre à *« à quelle entité métier correspond ce compte ? »* — un compte peut en porter plusieurs (par ex. à la fois un vendeur et un contact client),
- un *UserProfile* comme modèle de création qui associe un rôle d'autorisation au type d'entité auquel le compte est censé être rattaché.

Les deux classes étendent `org\schema\Intangible` : elles qualifient un compte, ce ne sont pas des ressources adressables en propre. Elles exposent le distinguisheur `@context = 'https://schema.oihana.xyz'` dans le JSON-LD.

---

## Exemple express

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
    UserProfile::COLOR         => '#22C55E' ,           // indice d'UI
    UserProfile::ROLE          => 'seller' ,            // Role d'auth à accorder
    UserProfile::EXPECTED_TYPE => 'Seller' ,            // additionalType attendu de la Person
]);
```

Le `subject` d'une `BusinessIdentity` et le `role` d'un `UserProfile` sont des **références résolues** : chacun accepte un objet hydraté, une référence scalaire (clé / nom), **ou** un `array` associatif brut (un document projeté par AQL) — jamais une classe forcée.

---

## Catalogue des classes

| Classe             | Étend        | Rôle                                                                                                                          |
|--------------------|--------------|-------------------------------------------------------------------------------------------------------------------------------|
| `BusinessIdentity` | `Intangible` | Lien typé entre un compte et une entité métier (`Person` / `Organization`), exposé via un unique `subject`. Le type d'identité est **dérivé** de l'`additionalType` du sujet, jamais stocké sur le lien. |
| `UserProfile`      | `Intangible` | Modèle de création associant un `role` à l'`expectedType` de la personne à laquelle le compte sera rattaché, plus un indice d'UI `color`. Ne porte aucun état propre au compte. |

### Accesseurs de lecture de `BusinessIdentity`

Helpers neutres pour naviguer le lien sans réimplémenter la résolution — chacun tolère un `subject` qui est un objet hydraté **ou** un tableau associatif brut :

- `subjectType()` — l'`additionalType` Schema.org du sujet (chaîne ou tableau).
- `isType( string $type )` — test de type (égalité stricte, ou appartenance quand `additionalType` est un tableau).
- `subjectKey( string|array $key = '_key' )` — résout l'identifiant du sujet.
- `worksForKey( string|array $key = '_key' )` — résout la `_key`/`id` de l'organisation pour laquelle le sujet `worksFor`.

Côté compte, [`User`](oihana-auth.md) expose `firstIdentityBySubjectType()` et `identitiesBySubjectType()` pour filtrer ses `identities` par type de sujet.

Pour la liste exhaustive des propriétés, parcourez le code source sous [`src/xyz/oihana/schema/business/`](../../src/xyz/oihana/schema/business) ou la [référence d'API](../../docs).

---

## Constantes associées

Les clés de propriétés sont exposées par deux traits — [`BusinessIdentityTrait`](../../src/xyz/oihana/schema/constants/traits/business/BusinessIdentityTrait.php) (`SUBJECT`) et [`UserProfileTrait`](../../src/xyz/oihana/schema/constants/traits/business/UserProfileTrait.php) (`COLOR`, `EXPECTED_TYPE`, `ROLE`).

Contrairement aux traits `places` et `http`, ils ne sont **volontairement pas** agrégés dans la classe maîtresse [`Oihana`](../../src/xyz/oihana/schema/constants/Oihana.php) : `BusinessIdentityTrait::SUBJECT` entrerait en collision avec `auth\PermissionTrait::SUBJECT` déjà agrégé. Les deux sont composés dans l'agrégateur [`BusinessTrait`](../../src/xyz/oihana/schema/constants/traits/BusinessTrait.php), et atteints directement via les constantes de classe (par ex. `UserProfile::ROLE`, `BusinessIdentity::SUBJECT`).

---

## Pour aller plus loin

- [`xyz\oihana\schema\auth`](oihana-auth.md) — rôles, permissions, `User::$identities`.
- [`org\schema`](schema-org/README.md) — `Person`, `Organization`, `Intangible`.
- [Démarrage rapide](demarrage.md) — installation, hydratation, bases du JSON-LD.
- [Référence d'API](../../docs).
