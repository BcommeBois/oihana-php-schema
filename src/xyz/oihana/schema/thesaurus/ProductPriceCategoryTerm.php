<?php

namespace xyz\oihana\schema\thesaurus;

use xyz\oihana\schema\constants\traits\thesaurus\ThesaurusTermTrait;
use xyz\oihana\schema\constants\traits\thesaurus\TreeMetricsTrait;
use xyz\oihana\schema\thesaurus\traits\HasTreeMetrics;
use xyz\oihana\schema\traits\HasColor;

/**
 * A product price category (a tariff family used to scope pricing rules and
 * conditions) : a hierarchical {@see Concept} that also carries a house
 * display {@see HasColor::$color}.
 *
 * A product price category is a distinct notion from a {@see ProductCategoryTerm}
 * (the customer-facing catalog category) : it classifies a product for pricing
 * purposes only (buying-price variations, selling conditions), even though it
 * is derived from the very same kind of cumulative, per-level identifier and
 * shares the exact same shape. Keeping it on its own class — rather than
 * reusing {@see ProductCategoryTerm} — keeps the `@type` of a term meaningful :
 * a price category never advertises itself as a catalog category.
 *
 * Product categories and product price categories are, for now, the only two
 * thesaurus families that are both hierarchical (the SKOS broader/narrower
 * relations, inherited from {@see Concept}) and colored. Other, flat thesaurus
 * families stay on {@see ThesaurusTerm}.
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
 * use xyz\oihana\schema\thesaurus\ProductPriceCategoryTerm;
 *
 * $priceCategory = new ProductPriceCategoryTerm
 * ([
 *     'name'                            => 'Premium tier' ,
 *     'termCode'                        => 'PREM' ,
 *     ProductPriceCategoryTerm::COLOR   => '#2E5E4E' ,
 *     ProductPriceCategoryTerm::BROADER => 'priceCategories/100' ,
 * ]);
 * ```
 *
 * @see Concept
 * @see HasColor
 * @see HasTreeMetrics
 * @see ProductCategoryTerm
 * @see ThesaurusTerm
 *
 * @package xyz\oihana\schema\thesaurus
 * @category Thesaurus
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.4.0
 */
class ProductPriceCategoryTerm extends Concept
{
    use HasColor           ,
        HasTreeMetrics     ,
        ThesaurusTermTrait ,
        TreeMetricsTrait   ;
}
