<?php

namespace xyz\oihana\schema\constants\traits\thesaurus;

/**
 * Defines the custom property names of the
 * {@see \xyz\oihana\schema\thesaurus\ThesaurusScheme} schema entity.
 *
 * Centralizing these keys avoids hardcoded string literals and provides a
 * single source of truth for hydration and serialization.
 *
 * It is composed by the {@see \xyz\oihana\schema\thesaurus\ThesaurusScheme}
 * entity (and reused by {@see \xyz\oihana\schema\thesaurus\ThesaurusDomain}
 * for the shared `ACTIVE`/`ORDER` keys) and aggregated, through
 * {@see \xyz\oihana\schema\constants\traits\ThesaurusTrait}, into the global
 * {@see \xyz\oihana\schema\constants\Oihana} constants class (its `DOMAIN` and
 * `PATH` values match the auth/audit keys already exposed there).
 *
 * Typical usage:
 *
 * ```php
 * $scheme[ ThesaurusSchemeTrait::ACTIVE ] = true ;
 * ```
 *
 * @package xyz\oihana\schema\constants\traits\thesaurus
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.3.0
 */
trait ThesaurusSchemeTrait
{
    /**
     * The visibility flag : an inactive scheme is hidden from the interfaces
     * without being deleted.
     *
     * Related model property:
     *
     * ```php
     * public ?bool $active ;
     * ```
     */
    const string ACTIVE = 'active' ;

    /**
     * The domain the scheme is filed under, as a bare key or a hydrated
     * {@see \xyz\oihana\schema\thesaurus\ThesaurusDomain}.
     *
     * Related model property:
     *
     * ```php
     * public null|string|array|ThesaurusDomain $domain ;
     * ```
     */
    const string DOMAIN = 'domain' ;

    /**
     * The provenance flag : a harvested scheme is fed by an external source,
     * so the term core (`id`/`name`) is read-only and only the house overlays
     * are editable.
     *
     * Related model property:
     *
     * ```php
     * public ?bool $harvested ;
     * ```
     */
    const string HARVESTED = 'harvested' ;

    /**
     * The display order of the scheme within its domain.
     *
     * Related model property:
     *
     * ```php
     * public ?int $order ;
     * ```
     */
    const string ORDER = 'order' ;

    /**
     * The relative path of the scheme routes (e.g. `/thesaurus/products/categories`).
     *
     * Related model property:
     *
     * ```php
     * public ?string $path ;
     * ```
     */
    const string PATH = 'path' ;

    /**
     * The system flag : the technical skeleton of the scheme is defined in
     * code, so it cannot be deleted through an API.
     *
     * Related model property:
     *
     * ```php
     * public ?bool $system ;
     * ```
     */
    const string SYSTEM = 'system' ;
}
