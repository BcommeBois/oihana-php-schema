# `xyz\oihana\schema\thesaurus` — Oihana thesaurus (SKOS)

The `xyz\oihana\schema\thesaurus` namespace models **classification schemes and concept trees** — thesauri, taxonomies, controlled vocabularies. It layers the W3C **SKOS** vocabulary (`broader`/`narrower`, concept schemes, mapping relations) on top of the Schema.org `DefinedTerm`, which Schema.org itself equates with `skos:Concept`.

> 🇫🇷 Cette page existe aussi en [français](../fr/oihana-thesaurus.md).

---

## When to use it

Reach for these classes when a plain `DefinedTerm` is not enough — when your terms form a **parent/child hierarchy** (e.g. a product-category tree) or need cross-vocabulary alignment:

- a *ThesaurusTerm* for a **flat** term enriched with a house `color` hint,
- a *Concept* when terms relate to one another — `broader` (parent), `narrower` (children) and their transitive closures, plus associative (`related`) and cross-scheme mapping links,
- a *ProductCategoryTerm* when a concept is **both** hierarchical and colored (the product-category case),
- a *ConceptScheme* to hold a vocabulary and expose its root concepts (`hasTopConcept`).

Why SKOS rather than a home-grown `parent`/`children` pair? Because Schema.org is light on relations, while SKOS is the standard built exactly for thesauri — and anchoring on `DefinedTerm` (rather than re-rooting under `Thing`) keeps the Schema.org bridge: `name`, `termCode`, `inDefinedTermSet`, the ArangoDB metadata and the JSON-LD serialization all come for free.

Every entity exposes the `@context = 'https://schema.oihana.xyz'` distinguisher in the JSON-LD output.

---

## Traversal-only relations

The SKOS relations (`broader`, `narrower`, `related`, the `*Match` mappings, `topConceptOf`, `hasTopConcept`) are **traversal-only**: they are never persisted and never harvested — they are populated only on selected API responses (e.g. `/{key}/children`, `/descendants`, `/ancestors`).

Each relation is a **resolved reference**: it accepts a hydrated object, a scalar reference (a bare `_key`), **or** a raw associative `array` (an AQL-projected document, rebuildable via `new Concept($array)`). Plural relations are hydrated element by element through `#[HydrateWith(Concept::class)]` — but only on the reflection-based hydration path, not the lightweight constructor (which copies values verbatim).

---

## Quick example

```php
use xyz\oihana\schema\thesaurus\Concept;

$term = new Concept
([
    'name'            => 'Red wine' ,
    'termCode'        => 'RED' ,
    Concept::BROADER  => 'categories/100' ,            // a bare _key reference
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
    'name'                       => 'Red wine' ,
    'termCode'                   => 'RED' ,
    ProductCategoryTerm::COLOR   => '#7B1E3A' ,        // UI hint, from HasColor
    ProductCategoryTerm::BROADER => 'categories/100' , // SKOS parent, from Concept
]);
```

```php
use xyz\oihana\schema\thesaurus\ConceptScheme;

$scheme = new ConceptScheme
([
    'name'                         => 'Product categories' ,
    ConceptScheme::HAS_TOP_CONCEPT =>
    [
        [ 'name' => 'Wines' ,   'termCode' => 'WINE' ] ,
        [ 'name' => 'Spirits' , 'termCode' => 'SPIRIT' ] ,
    ],
]);
```

---

## Class catalog

| Class                 | Extends          | Purpose                                                                                                                  |
|-----------------------|------------------|--------------------------------------------------------------------------------------------------------------------------|
| `ThesaurusTerm`       | `DefinedTerm`    | A **flat** thesaurus term enriched with a house `color` (`#RRGGBB`) — for vocabularies with no hierarchy.                 |
| `Concept`             | `DefinedTerm`    | A **SKOS concept** carrying the hierarchical (`broader`/`narrower` + transitive), associative (`related`) and cross-scheme mapping (`*Match`) relations, the `hiddenLabel` and the documentation notes. |
| `ProductCategoryTerm` | `Concept`        | A concept that is **both** hierarchical and colored (`use HasColor`) — the product-category family. Flat families stay on `ThesaurusTerm`. |
| `ConceptScheme`       | `DefinedTermSet` | A **SKOS concept scheme** (a vocabulary), exposing its root concepts via `hasTopConcept`. Concept membership stays on the inherited `inDefinedTermSet` (`skos:inScheme`). |

### SKOS coverage at a glance

| SKOS feature              | Where                                                        |
|---------------------------|-------------------------------------------------------------|
| `skos:Concept`            | `Concept` (← `DefinedTerm`)                                  |
| `skos:ConceptScheme`      | `ConceptScheme` (← `DefinedTermSet`)                         |
| Hierarchy                 | `broader`, `narrower`, `broaderTransitive`, `narrowerTransitive` |
| Associative relation      | `related`                                                   |
| Scheme entry points       | `hasTopConcept`, `topConceptOf` (`inScheme` = `inDefinedTermSet`) |
| Labels                    | `name` (prefLabel), `alternateName` (altLabel), `hiddenLabel` |
| Documentation notes       | `changeNote`, `editorialNote`, `example`, `historyNote`, `note`, `scopeNote` |
| Cross-scheme mappings     | `exactMatch`, `closeMatch`, `broadMatch`, `narrowMatch`, `relatedMatch` |

`skos:definition` is deliberately mapped to the inherited `description`, not duplicated. The SKOS **Collections** (`skos:Collection`/`OrderedCollection`/`member`/`memberList`) are intentionally deferred until a non-hierarchical grouping is needed.

For exhaustive property lists, browse the source under [`src/xyz/oihana/schema/thesaurus/`](../../src/xyz/oihana/schema/thesaurus) or the [API reference](../../docs).

---

## Property traits

Each class pairs a **property-bearing trait** with its **constant trait**, so a concept can compose exactly the capabilities it needs:

| Property trait                                                                                      | Carries                                                            |
|-----------------------------------------------------------------------------------------------------|-------------------------------------------------------------------|
| [`HasColor`](../../src/xyz/oihana/schema/thesaurus/traits/HasColor.php)                              | `color` (shared by `ThesaurusTerm` and `ProductCategoryTerm`)     |
| [`HasSkosRelations`](../../src/xyz/oihana/schema/thesaurus/traits/HasSkosRelations.php)              | `broader`, `narrower`, the transitive forms, `related`            |
| [`HasSkosNotes`](../../src/xyz/oihana/schema/thesaurus/traits/HasSkosNotes.php)                      | the six SKOS documentation notes                                  |
| [`HasSkosMappings`](../../src/xyz/oihana/schema/thesaurus/traits/HasSkosMappings.php)                | the five cross-scheme `*Match` relations                          |

---

## Related constants

Property keys are exposed by the constant traits under [`constants/traits/thesaurus/`](../../src/xyz/oihana/schema/constants/traits/thesaurus) — `ThesaurusTermTrait` (`COLOR`), `ConceptTrait` (`BROADER`, `NARROWER`, …, `HIDDEN_LABEL`, `RELATED`, `TOP_CONCEPT_OF`), `SkosNotesTrait`, `ConceptSchemeTrait` (`HAS_TOP_CONCEPT`) and `SkosMappingsTrait`.

Unlike the `business` traits, these **are** aggregated — through the [`ThesaurusTrait`](../../src/xyz/oihana/schema/constants/traits/ThesaurusTrait.php) domain aggregator — into the master [`Oihana`](../../src/xyz/oihana/schema/constants/Oihana.php) class, so every key is reachable as `Oihana::BROADER`, `Oihana::HAS_TOP_CONCEPT`, etc. (the shared `COLOR` value matches the one already exposed by the auth traits).

---

## Related reading

- [`org\schema`](schema-org/README.md) — `DefinedTerm`, `DefinedTermSet`, `Intangible`, `Thing`.
- [Getting started](getting-started.md) — installation, hydration, JSON-LD basics.
- [SKOS Reference](https://www.w3.org/TR/skos-reference/) — the W3C specification.
- [API reference](../../docs).
