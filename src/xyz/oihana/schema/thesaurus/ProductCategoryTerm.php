<?php

namespace xyz\oihana\schema\thesaurus;

use xyz\oihana\schema\constants\traits\thesaurus\ThesaurusTermTrait;
use xyz\oihana\schema\constants\traits\thesaurus\TreeMetricsTrait;
use xyz\oihana\schema\thesaurus\traits\HasTreeMetrics;
use xyz\oihana\schema\traits\HasColor;

/**
 * A product category : a hierarchical {@see Concept} that also carries a house
 * display {@see HasColor::$color}.
 *
 * Product categories are the only thesaurus family that is both hierarchical
 * (the SKOS broader/narrower relations, inherited from {@see Concept}) and
 * colored. Other, flat thesaurus families stay on {@see ThesaurusTerm}.
 *
 * It inherits the `@context` and the SKOS relation constants from
 * {@see Concept}, and adds the `color` property through {@see HasColor} (whose
 * name constant comes from {@see ThesaurusTermTrait}, shared with
 * {@see ThesaurusTerm}). It also carries the non-SKOS structural `childrenCount`
 * through {@see HasTreeMetrics} — a projection-only (`full`-skin) metric, with
 * `isLeaf` ⟺ `childrenCount === 0`.
 *
 * ### Example
 * ```php
 * use xyz\oihana\schema\thesaurus\ProductCategoryTerm;
 *
 * $category = new ProductCategoryTerm
 * ([
 *     'name'                       => 'Red wine' ,
 *     'termCode'                   => 'RED' ,
 *     ProductCategoryTerm::COLOR   => '#7B1E3A' ,
 *     ProductCategoryTerm::BROADER => 'wines/100' ,
 * ]);
 * ```
 *
 * @see Concept
 * @see HasColor
 * @see HasTreeMetrics
 * @see ThesaurusTerm
 *
 * @package xyz\oihana\schema\thesaurus
 * @category Thesaurus
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.1.1
 */
class ProductCategoryTerm extends Concept
{
    use HasColor           ,
        HasTreeMetrics     ,
        ThesaurusTermTrait ,
        TreeMetricsTrait   ;
}
