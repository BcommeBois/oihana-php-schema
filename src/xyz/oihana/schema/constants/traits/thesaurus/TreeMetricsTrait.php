<?php

namespace xyz\oihana\schema\constants\traits\thesaurus;

/**
 * Defines the property name constants of the structural tree metrics carried by
 * {@see \xyz\oihana\schema\thesaurus\traits\HasTreeMetrics}.
 *
 * Centralizing these keys avoids hardcoded string literals and provides a single
 * source of truth for hydration and serialization.
 *
 * It is aggregated, through {@see \xyz\oihana\schema\constants\traits\ThesaurusTrait},
 * into the global {@see \xyz\oihana\schema\constants\Oihana} constants class, so
 * the field is reachable via `Oihana::CHILDREN_COUNT`.
 *
 * Typical usage:
 *
 * ```php
 * $term[ TreeMetricsTrait::CHILDREN_COUNT ] = 8 ;
 * ```
 *
 * @package xyz\oihana\schema\constants\traits\thesaurus
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.2.1
 */
trait TreeMetricsTrait
{
    /**
     * The number of direct children — the count of `narrower` concepts.
     *
     * Related model property:
     *
     * ```php
     * public ?int $childrenCount ;
     * ```
     */
    const string CHILDREN_COUNT = 'childrenCount' ;
}
