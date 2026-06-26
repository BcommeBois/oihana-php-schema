# `xyz\oihana\schema\thesaurus` — Thésaurus Oihana (SKOS)

Le namespace `xyz\oihana\schema\thesaurus` modélise les **schémas de classification et les arbres de concepts** — thésaurus, taxonomies, vocabulaires contrôlés. Il superpose le vocabulaire W3C **SKOS** (`broader`/`narrower`, schémas de concepts, relations d'alignement) au `DefinedTerm` de Schema.org, que Schema.org assimile lui-même à `skos:Concept`.

> 🇬🇧 This page is also available in [English](../en/oihana-thesaurus.md).

---

## Quand l'utiliser

Choisissez ces classes lorsqu'un simple `DefinedTerm` ne suffit pas — lorsque vos termes forment une **hiérarchie parent/enfant** (par ex. un arbre de catégories produits) ou nécessitent un alignement entre vocabulaires :

- un *ThesaurusTerm* pour un terme **plat** enrichi d'un indice de couleur maison `color`,
- un *Concept* lorsque les termes se relient entre eux — `broader` (parent), `narrower` (enfants) et leurs fermetures transitives, plus les liens associatifs (`related`) et d'alignement inter-schémas,
- un *ProductCategoryTerm* lorsqu'un concept est **à la fois** hiérarchique et coloré (le cas des catégories produits),
- un *ConceptScheme* pour porter un vocabulaire et exposer ses concepts racines (`hasTopConcept`),
- une *Collection* (ou *OrderedCollection*) pour regrouper des concepts **hors** hiérarchie — un groupe étiqueté, éventuellement ordonné, du type « cépages mis en avant ».

Pourquoi SKOS plutôt qu'un couple `parent`/`children` maison ? Parce que Schema.org est léger sur les relations, alors que SKOS est le standard conçu exactement pour les thésaurus — et ancrer sur `DefinedTerm` (plutôt que repartir d'une racine sous `Thing`) conserve le pont Schema.org : `name`, `termCode`, `inDefinedTermSet`, les métadonnées ArangoDB et la sérialisation JSON-LD sont acquis gratuitement.

Chaque entité expose le distinguisheur `@context = 'https://schema.oihana.xyz'` dans le JSON-LD.

---

## Relations « uniquement pour le parcours »

Les relations SKOS (`broader`, `narrower`, `related`, les alignements `*Match`, `topConceptOf`, `hasTopConcept`, `member`, `memberList`) sont **uniquement destinées au parcours** : elles ne sont jamais persistées ni moissonnées — elles ne sont peuplées que sur certaines réponses d'API (par ex. `/{key}/children`, `/descendants`, `/ancestors`).

Chaque relation est une **référence résolue** : elle accepte un objet hydraté, une référence scalaire (une `_key` nue), **ou** un `array` associatif brut (un document projeté par AQL, reconstructible via `new Concept($array)`). Les relations plurielles sont hydratées élément par élément via `#[HydrateWith(Concept::class)]` — mais uniquement sur le chemin d'hydratation par réflexion, pas via le constructeur léger (qui recopie les valeurs telles quelles). Les `member`/`memberList` des collections vont plus loin avec `#[HydrateWith(Concept::class, Collection::class)]` : chaque entrée est aiguillée **polymorphiquement** selon son discriminant `@type` (repli sur `Concept`).

---

## Exemple express

```php
use xyz\oihana\schema\thesaurus\Concept;

$term = new Concept
([
    'name'            => 'Vin rouge' ,
    'termCode'        => 'RED' ,
    Concept::BROADER  => 'categories/100' ,            // une référence _key nue
    Concept::NARROWER =>
    [
        [ 'name' => 'Cabernet Sauvignon' , 'termCode' => 'CAB-SAUV' ] ,
    ],
]);
```

```php
use xyz\oihana\schema\thesaurus\ProductCategoryTerm;

$category = new ProductCategoryTerm
([
    'name'                       => 'Vin rouge' ,
    'termCode'                   => 'RED' ,
    ProductCategoryTerm::COLOR   => '#7B1E3A' ,        // indice d'UI, via HasColor
    ProductCategoryTerm::BROADER => 'categories/100' , // parent SKOS, via Concept
]);
```

```php
use xyz\oihana\schema\thesaurus\ConceptScheme;

$scheme = new ConceptScheme
([
    'name'                         => 'Catégories produits' ,
    ConceptScheme::HAS_TOP_CONCEPT =>
    [
        [ 'name' => 'Vins' ,        'termCode' => 'WINE' ] ,
        [ 'name' => 'Spiritueux' ,  'termCode' => 'SPIRIT' ] ,
    ],
]);
```

```php
use xyz\oihana\schema\thesaurus\Collection;

$collection = new Collection
([
    'name'             => 'Cépages mis en avant' ,
    Collection::MEMBER =>
    [
        [ '@type' => 'Concept'    , 'name' => 'Merlot' ] ,            // hydraté en Concept
        [ '@type' => 'Collection' , 'name' => 'Blancs effervescents' ] , // hydraté en Collection
    ],
]);
```

---

## Catalogue des classes

| Classe                | Étend            | Rôle                                                                                                                     |
|-----------------------|------------------|--------------------------------------------------------------------------------------------------------------------------|
| `ThesaurusTerm`       | `DefinedTerm`    | Un terme de thésaurus **plat** enrichi d'une couleur maison `color` (`#RRGGBB`) — pour les vocabulaires sans hiérarchie.  |
| `Concept`             | `DefinedTerm`    | Un **concept SKOS** portant les relations hiérarchiques (`broader`/`narrower` + transitives), associatives (`related`) et d'alignement inter-schémas (`*Match`), le `hiddenLabel` et les notes documentaires. |
| `ProductCategoryTerm` | `Concept`        | Un concept **à la fois** hiérarchique et coloré (`use HasColor`) — la famille des catégories produits. Les familles plates restent sur `ThesaurusTerm`. |
| `ConceptScheme`       | `DefinedTermSet` | Un **schéma de concepts SKOS** (un vocabulaire), exposant ses concepts racines via `hasTopConcept`. L'appartenance d'un concept reste portée par `inDefinedTermSet` hérité (`skos:inScheme`). |
| `Collection`          | `Intangible`     | Une **collection SKOS** — un regroupement étiqueté et **non hiérarchique** de concepts (`member`). Les membres sont polymorphes : concepts et/ou sous-collections imbriquées. |
| `OrderedCollection`   | `Collection`     | Une `Collection` dont les membres ont un ordre signifiant (`memberList`).                                                 |

### Couverture SKOS en un coup d'œil

| Élément SKOS              | Où                                                          |
|---------------------------|-------------------------------------------------------------|
| `skos:Concept`            | `Concept` (← `DefinedTerm`)                                  |
| `skos:ConceptScheme`      | `ConceptScheme` (← `DefinedTermSet`)                         |
| Hiérarchie                | `broader`, `narrower`, `broaderTransitive`, `narrowerTransitive` |
| Relation associative      | `related`                                                   |
| Points d'entrée du schéma | `hasTopConcept`, `topConceptOf` (`inScheme` = `inDefinedTermSet`) |
| Libellés                  | `name` (prefLabel), `alternateName` (altLabel), `hiddenLabel` |
| Notes documentaires       | `changeNote`, `editorialNote`, `example`, `historyNote`, `note`, `scopeNote` |
| Alignements inter-schémas | `exactMatch`, `closeMatch`, `broadMatch`, `narrowMatch`, `relatedMatch` |
| Collections               | `Collection`, `OrderedCollection` (`member`, `memberList`)  |

`skos:definition` est volontairement mappé sur le `description` hérité, non dupliqué. **SKOS-XL** (libellés réifiés) et les super-propriétés abstraites (`skos:semanticRelation`, `skos:mappingRelation`) sont volontairement hors périmètre.

Pour la liste exhaustive des propriétés, parcourez le code source sous [`src/xyz/oihana/schema/thesaurus/`](../../src/xyz/oihana/schema/thesaurus) ou la [référence d'API](../../docs).

---

## Traits de propriétés

Chaque classe associe un **trait porteur de propriétés** à son **trait de constantes**, afin qu'un concept compose exactement les capacités dont il a besoin :

| Trait de propriétés                                                                                  | Porte                                                             |
|-----------------------------------------------------------------------------------------------------|------------------------------------------------------------------|
| [`HasColor`](../../src/xyz/oihana/schema/thesaurus/traits/HasColor.php)                              | `color` (partagé par `ThesaurusTerm` et `ProductCategoryTerm`)   |
| [`HasSkosRelations`](../../src/xyz/oihana/schema/thesaurus/traits/HasSkosRelations.php)              | `broader`, `narrower`, les formes transitives, `related`         |
| [`HasSkosNotes`](../../src/xyz/oihana/schema/thesaurus/traits/HasSkosNotes.php)                      | les six notes documentaires SKOS                                 |
| [`HasSkosMappings`](../../src/xyz/oihana/schema/thesaurus/traits/HasSkosMappings.php)                | les cinq relations d'alignement `*Match` inter-schémas           |
| [`HasSkosMembers`](../../src/xyz/oihana/schema/thesaurus/traits/HasSkosMembers.php)                  | `member` — l'appartenance polymorphe à une collection            |

---

## Constantes associées

Les clés de propriétés sont exposées par les traits de constantes sous [`constants/traits/thesaurus/`](../../src/xyz/oihana/schema/constants/traits/thesaurus) — `ThesaurusTermTrait` (`COLOR`), `ConceptTrait` (`BROADER`, `NARROWER`, …, `HIDDEN_LABEL`, `RELATED`, `TOP_CONCEPT_OF`), `SkosNotesTrait`, `ConceptSchemeTrait` (`HAS_TOP_CONCEPT`), `SkosMappingsTrait` et `CollectionTrait` (`MEMBER`, `MEMBER_LIST`).

Contrairement aux traits `business`, ils **sont** agrégés — via l'agrégateur de domaine [`ThesaurusTrait`](../../src/xyz/oihana/schema/constants/traits/ThesaurusTrait.php) — dans la classe maîtresse [`Oihana`](../../src/xyz/oihana/schema/constants/Oihana.php), si bien que chaque clé est atteignable via `Oihana::BROADER`, `Oihana::HAS_TOP_CONCEPT`, etc. (la valeur partagée `COLOR` coïncide avec celle déjà exposée par les traits d'auth).

---

## Pour aller plus loin

- [`org\schema`](schema-org/README.md) — `DefinedTerm`, `DefinedTermSet`, `Intangible`, `Thing`.
- [Démarrage rapide](demarrage.md) — installation, hydratation, bases du JSON-LD.
- [Référence SKOS](https://www.w3.org/TR/skos-reference/) — la spécification du W3C.
- [Référence d'API](../../docs).
