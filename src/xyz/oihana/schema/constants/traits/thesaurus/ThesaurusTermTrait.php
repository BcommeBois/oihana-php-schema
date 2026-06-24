<?php

namespace xyz\oihana\schema\constants\traits\thesaurus;

/**
 * Defines the custom property names of the
 * {@see \xyz\oihana\schema\thesaurus\ThesaurusTerm} schema entity.
 *
 * Centralizing these keys avoids hardcoded string literals and provides a
 * single source of truth for hydration and serialization.
 *
 * It is composed by the {@see \xyz\oihana\schema\thesaurus\ThesaurusTerm}
 * entity and aggregated, through {@see \xyz\oihana\schema\constants\traits\ThesaurusTrait},
 * into the global {@see \xyz\oihana\schema\constants\Oihana} constants class
 * (its `COLOR` value matches the other `COLOR` keys already exposed there).
 *
 * Typical usage:
 *
 * ```php
 * $term[ ThesaurusTermTrait::COLOR ] = '#22C55E' ;
 * ```
 *
 * @package xyz\oihana\schema\constants\traits\thesaurus
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.1.1
 */
trait ThesaurusTermTrait
{
    /**
     * A house color, expressed as a `#RRGGBB` hex string.
     *
     * Related model property:
     *
     * ```php
     * public ?string $color ;
     * ```
     */
    const string COLOR = 'color' ;
}
