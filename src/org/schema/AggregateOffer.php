<?php

namespace org\schema;

/**
 * When a single product is associated with multiple offers (for example, the same pair of shoes is offered by different merchants), then AggregateOffer can be used.
 * @see https://schema.org/AggregateOffer
 */
class AggregateOffer extends Offer
{
    /**
     * The highest price of all offers available.
     * Usage guidelines:
     * Use values from 0123456789 (Unicode 'DIGIT ZERO' (U+0030) to 'DIGIT NINE' (U+0039)) rather than superficially similar Unicode symbols.
     * Use '.' (Unicode 'FULL STOP' (U+002E)) rather than ',' to indicate a decimal point. Avoid using these symbols as a readability separator.
     * @var int|float|string|null
     */
    public null|int|float|string $highPrice ;

    /**
     * The lowest price of all offers available.
     * Usage guidelines:
     * Use values from 0123456789 (Unicode 'DIGIT ZERO' (U+0030) to 'DIGIT NINE' (U+0039)) rather than superficially similar Unicode symbols.
     * Use '.' (Unicode 'FULL STOP' (U+002E)) rather than ',' to indicate a decimal point. Avoid using these symbols as a readability separator.
     * @var int|float|string|null
     */
    public null|int|float|string $lowPrice ;

    /**
     * he number of offers for the product.
     * @var int|null T
     */
    public null|int $offerCount ;

    /**
     * An offer to provide this item.
     * @var array|Offer|null|Demand
     */
    public array|Offer|Demand|null $offers ;
}