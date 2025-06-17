<?php

namespace org\schema;

use DateTime;

/**
 * A monetary value or range.
 * This type can be used to describe an amount of money such as $50 USD, or a range as in describing a bank account being suitable for a balance between £1,000 and £1,000,000 GBP, or the value of a salary, etc. It is recommended to use PriceSpecification Types to describe the price of an Offer, Invoice, etc.
 */
class MonetaryAmount extends StructuredValue
{
    /**
     * The currency in which the monetary amount is expressed.
     * Use standard formats: ISO 4217 currency format, e.g. "USD";
     * Ticker symbol for cryptocurrencies, e.g. "BTC";
     * well known names for Local Exchange Trading Systems (LETS) and other currency types, e.g. "Ithaca HOUR".
     */
    public null|string $currency ;

    /**
     * The upper value of some characteristic or property.
     * @var int|null
     */
    public ?int $maxValue ;

    /**
     * The lower value of some characteristic or property.
     * @var int|null
     */
    public ?int $minValue ;

    /**
     * The date when the item becomes valid (DateTime).
     */
    public null|string|int|DateTime $validFrom ;

    /**
     * The end of the validity of offer, price specification, or opening hours data (DateTime).
     */
    public null|string|int|DateTime $validThrough ;

    /**
     * The value of a QuantitativeValue or property value node.
     * @var mixed
     */
    public mixed $value ;
}


