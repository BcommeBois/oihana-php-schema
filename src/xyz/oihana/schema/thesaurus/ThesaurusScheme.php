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
 * (`path`), provenance (`harvested`, `system`) and the write surface (`writes`).
 *
 * The provenance flags split the editing rights : on a `harvested` scheme the
 * term core (`id`/`name`) is fed by an external source and read-only, only the
 * house overlays are editable ; on a `system` scheme the technical skeleton is
 * defined in code, so it cannot be deleted through an API. The `writes` map
 * states that surface field by field, per HTTP verb, so a consumer knows what
 * a write on the family honors without guessing it from the flags.
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
 *     ThesaurusScheme::WRITES     => [ 'patch' => [ 'active' , 'color' ] ] ,
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
     * The write surface of the scheme, keyed by HTTP verb : each entry lists
     * the body fields a write on the family honors — what the body allow-list
     * of the family actually keeps, not what a caller is permitted to send
     * (permissions answer *who*, this property answers *what*).
     *
     * An empty array reads as read-only, and a missing verb key means the verb
     * is not exposed at all. The value is derived from the family's body
     * allow-list by the registry maintainer, never written by hand.
     *
     * ```php
     * // a natively administered family
     * $scheme->writes =
     * [
     *     'post'  => [ 'name' , 'active' , 'alternateName' , 'color' , 'description' ] ,
     *     'patch' => [ 'active' , 'alternateName' , 'color' , 'description' ] ,
     * ] ;
     *
     * // a harvested family with editable overlays
     * $scheme->writes = [ 'patch' => [ 'active' , 'color' ] ] ;
     *
     * // a read-only mirror
     * $scheme->writes = [] ;
     * ```
     *
     * @var array<string, list<string>>|null
     */
    public ?array $writes ;
}
