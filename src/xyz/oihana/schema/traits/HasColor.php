<?php

namespace xyz\oihana\schema\traits;

/**
 * Provides an optional display color for a schema entity.
 *
 * The color is a house property — a presentation hint layered on top of the
 * harvested data, expressed as a `#RRGGBB` hex string. It is extracted into a
 * dedicated trait so it can be composed by any entity a user interface needs to
 * tint, without duplicating the property declaration : the thesaurus families
 * ({@see ThesaurusTerm}, {@see ProductCategoryTerm}, {@see ProductPriceCategoryTerm},
 * {@see ThesaurusDomain} and {@see ThesaurusScheme}) and, outside the thesaurus,
 * the product type ({@see ProductType}).
 *
 * The companion property name constant lives next to the composing entity : in
 * {@see ThesaurusTermTrait} for the thesaurus families, and in the products
 * `ProductType` constants trait for the product type. All of them carry the
 * same `'color'` value.
 *
 * @package xyz\oihana\schema\traits
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.1.1
 */
trait HasColor
{
    /**
     * An optional house color, expressed as a `#RRGGBB` hex string.
     *
     * Example:
     * ```php
     * $term->color = '#7B1E3A' ;
     * ```
     *
     * @var string|null
     */
    public ?string $color ;
}
