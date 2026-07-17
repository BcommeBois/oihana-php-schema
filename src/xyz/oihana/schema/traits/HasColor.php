<?php

namespace xyz\oihana\schema\traits;

/**
 * Provides an optional display color for a schema entity.
 *
 * The color is a house property — a presentation hint layered on top of the
 * harvested data, expressed as a `#RRGGBB` hex string. It is extracted into a
 * dedicated trait so it can be composed by several thesaurus entities (e.g.
 * {@see ThesaurusTerm} and {@see ProductCategoryTerm}) without duplicating
 * the property declaration.
 *
 * The companion property name constant lives in {@see ThesaurusTermTrait}.
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
