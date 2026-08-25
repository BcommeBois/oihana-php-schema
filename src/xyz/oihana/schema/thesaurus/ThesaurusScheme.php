<?php

namespace xyz\oihana\schema\thesaurus;

use oihana\reflect\attributes\HydrateWith;

use xyz\oihana\schema\constants\traits\thesaurus\ThesaurusSchemeTrait;
use xyz\oihana\schema\constants\traits\thesaurus\ThesaurusTermTrait;
use xyz\oihana\schema\traits\HasColor;

/**
 * A thesaurus taken as a whole, as it appears in a registry : the manageable metadata plus the provenance flags.
 *
 * In the registry layer of the model (registry → domains → schemes → concepts),
 * a `ThesaurusScheme` is the administrative view of a vocabulary : it extends
 * the SKOS {@see ConceptScheme} (so it keeps `hasTopConcept` and the inherited
 * `hasDefinedTerm` member list) and adds what a registry needs to manage it —
 * visibility (`active`), display (`color`, `order`), filing (`domain`), routing
 * (`path`), provenance (`harvested`, `system`), the type of the terms it holds
 * (`termType`) and the write surface (`writes`).
 *
 * The provenance flags split the editing rights : on a `harvested` scheme the
 * term core (`id`/`name`) is fed by an external source and read-only, only the
 * house overlays are editable ; on a `system` scheme the technical skeleton is
 * defined in code, so it cannot be deleted through an API. The `writes` map
 * states that surface field by field, per HTTP verb, so a consumer knows what
 * a write on the family honors — and how to draw it — without guessing any of
 * it from the flags.
 *
 * The `domain` property carries the domain↔scheme link (see
 * {@see ThesaurusDomain}) : a bare key, an AQL-projected associative array or a
 * hydrated object — hydrated through `#[HydrateWith(ThesaurusDomain::class)]`
 * on the reflection path only, like the other resolved references.
 *
 * ### Example
 * ```php
 * use xyz\oihana\schema\thesaurus\ThesaurusScheme;
 *
 * $scheme = new ThesaurusScheme
 * ([
 *     'name'                      => 'Product categories' ,
 *     ThesaurusScheme::ACTIVE     => true ,
 *     ThesaurusScheme::COLOR      => '#22C55E' ,
 *     ThesaurusScheme::DOMAIN     => 'products' ,
 *     ThesaurusScheme::HARVESTED  => true ,
 *     ThesaurusScheme::ORDER      => 1 ,
 *     ThesaurusScheme::PATH       => '/thesaurus/products/categories' ,
 *     ThesaurusScheme::SYSTEM     => true ,
 *     ThesaurusScheme::TERM_TYPE  => 'https://schema.oihana.xyz/ProductCategoryTerm' ,
 *     ThesaurusScheme::WRITES     => [ 'patch' => [ 'color' => [ 'type' => 'string' , 'erasable' => true ] ] ] ,
 * ]);
 * ```
 *
 * @see ConceptScheme
 * @see ThesaurusDomain
 * @see HasColor
 * @see ThesaurusSchemeTrait
 *
 * @package xyz\oihana\schema\thesaurus
 * @category Thesaurus
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.3.0
 */
class ThesaurusScheme extends ConceptScheme
{
    use HasColor             ,
        ThesaurusSchemeTrait ,
        ThesaurusTermTrait   ;

    /**
     * The domain the scheme is filed under : a bare key, an AQL-projected
     * associative array or a hydrated {@see ThesaurusDomain}.
     *
     * @var null|string|array|ThesaurusDomain
     */
    #[ HydrateWith( ThesaurusDomain::class ) ]
    public null|string|array|ThesaurusDomain $domain ;

    /**
     * The provenance flag : a harvested scheme is fed by an external source,
     * so the term core (`id`/`name`) is read-only and only the house overlays
     * are editable.
     *
     * @var bool|null
     */
    public ?bool $harvested ;

    /**
     * The display order of the scheme within its domain.
     *
     * @var int|null
     */
    public ?int $order ;

    /**
     * The relative path of the scheme routes (e.g. `/thesaurus/products/categories`).
     *
     * @var string|null
     */
    public ?string $path ;

    /**
     * The system flag : the technical skeleton of the scheme is defined in
     * code, so it cannot be deleted through an API.
     *
     * @var bool|null
     */
    public ?bool $system ;

    /**
     * The type of the terms the scheme holds, as a full URI —
     * `https://schema.oihana.xyz/ProductCategoryTerm`, `https://schema.org/DefinedTerm`.
     *
     * A registry answers what vocabularies exist before anything is read from
     * them ; without this, a consumer has to fetch a family's terms just to
     * learn what it will be handed, and pick its rendering afterwards.
     *
     * 🔑 **The full URI, not the bare name.** The families do not share one
     * vocabulary — a plain term comes from Schema.org, an enriched one from this
     * package — so `DefinedTerm` alone would not say where to look it up.
     *
     * ⚠️ **It types the members, never the set.** `additionalType` would be the
     * wrong slot for it : that property adds a type to *the item carrying it*,
     * and a scheme is not one of its own terms.
     *
     * @var string|null
     */
    public ?string $termType ;

    /**
     * The write surface of the scheme : per HTTP verb, then per field, what a
     * write on the family honors — what the body allow-list actually keeps, not
     * what a caller is permitted to send (permissions answer *who*, this
     * property answers *what*).
     *
     * **Every field describes itself**, so a consumer draws its form without
     * knowing the family beforehand :
     *
     * - `type` — what the field holds (`string`, `i18n`, `bool`, `int`…). It
     *   picks the widget, and on an `i18n` field it also says one language may
     *   be cleared on its own ;
     * - `required` — present only when the field may not be omitted ;
     * - `erasable` — present only when an explicit `null` takes the value back.
     *   It never appears on a creation : clearing supposes something is already
     *   there, and only a body read as partial strips the nulls it receives ;
     * - `default` — what the server poses when the caller stays silent.
     *
     * An absent key is an answer of its own. An empty array reads as read-only,
     * and a missing verb key means the verb is not exposed at all.
     *
     * ```php
     * // a natively administered family
     * $scheme->writes =
     * [
     *     'post' =>
     *     [
     *         'name'   => [ 'type' => 'string' , 'required' => true ] ,
     *         'active' => [ 'type' => 'bool'   , 'default'  => true ] ,
     *         'color'  => [ 'type' => 'string' ] ,
     *     ],
     *     'patch' =>
     *     [
     *         'active' => [ 'type' => 'bool'   ] ,
     *         'color'  => [ 'type' => 'string' , 'erasable' => true ] ,
     *     ],
     * ] ;
     *
     * // a read-only mirror
     * $scheme->writes = [] ;
     * ```
     *
     * 🚨 **The validation constraints are deliberately absent** — no length, no
     * pattern, no range. Whoever serves the scheme validates and answers per
     * field ; carrying the constraints here too would open a second source of
     * truth, free to drift from the one that decides. `required` is the
     * exception because it shapes the form rather than validating it.
     *
     * The whole map is derived from the family's own declarations by the
     * registry maintainer, never written by hand.
     *
     * @var array<string, array<string, array<string, mixed>>>|null
     */
    public ?array $writes ;
}
