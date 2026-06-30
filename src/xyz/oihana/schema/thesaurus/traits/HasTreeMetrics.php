<?php

namespace xyz\oihana\schema\thesaurus\traits;

/**
 * Provides non-SKOS structural metrics about a concept's position in its tree.
 *
 * These are **house** properties — they are deliberately NOT part of W3C SKOS,
 * which models the hierarchy *relationally* (through `narrower`/`broader`), not
 * with counts. They are extracted into a dedicated trait so the pure-SKOS
 * {@see \xyz\oihana\schema\thesaurus\Concept} stays free of structural metadata,
 * and are composed only by the hierarchical, project-specific terms (e.g.
 * {@see \xyz\oihana\schema\thesaurus\ProductCategoryTerm}) — mirroring
 * {@see HasColor}.
 *
 * Like the SKOS relations, they are **traversal/projection-only** : computed at
 * query time (e.g. an ArangoDB edge-count projection), never persisted nor
 * harvested, populated only on selected API responses (typically a `full` skin).
 *
 * The companion property name constant lives in
 * {@see \xyz\oihana\schema\constants\traits\thesaurus\TreeMetricsTrait}.
 *
 * @package xyz\oihana\schema\thesaurus\traits
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.2.1
 */
trait HasTreeMetrics
{
    /**
     * The number of direct children — the count of `narrower` concepts.
     *
     * A value of `0` means the concept is a leaf ; `null` means the metric was
     * not loaded. It lets a UI decide whether to show an "expand" affordance
     * without loading the children. The derived `isLeaf` is simply
     * `childrenCount === 0`.
     *
     * Example:
     * ```php
     * $term->childrenCount = 8 ;
     * ```
     *
     * @var int|null
     */
    public ?int $childrenCount ;
}
