<?php

namespace org\schema;

use org\schema\enumerations\BusinessFunction;

/**
 * A structured value indicating the quantity, unit of measurement, and business function of goods included in a bundle offer.
 * @see https://schema.org/TypeAndQuantityNode
 */
class TypeAndQuantityNode extends StructuredValue
{
    /**
     * The quantity of the goods included in the offer.
     * @var int|null
     */
    public ?int $amountOfThisGood ;

    /**
     * The business function (e.g. sell, lease, repair, dispose) of the offer or component of a bundle (TypeAndQuantityNode).
     * The default is http://purl.org/goodrelations/v1#Sell.
     * Commonly used values:
     * - http://purl.org/goodrelations/v1#ConstructionInstallation
     * - http://purl.org/goodrelations/v1#Dispose
     * - http://purl.org/goodrelations/v1#LeaseOut
     * - http://purl.org/goodrelations/v1#Maintain
     * - http://purl.org/goodrelations/v1#ProvideService
     * - http://purl.org/goodrelations/v1#Repair
     * - http://purl.org/goodrelations/v1#Sell
     * - http://purl.org/goodrelations/v1#Buy
     */
    public null|array|BusinessFunction|DefinedTerm $businessFunction ;

    /**
     * The product that this structured value is referring to.
     * @var Product|Service|null
     */
    public null|Product|Service $typeofGood ;

    /**
     * The unit of measurement given using the UN/CEFACT Common Code (3 characters) or a URL.
     * Other codes than the UN/CEFACT Common Code may be used with a prefix followed by a colon.
     * @var null|string
     */
    public ?string $unitCode ;

    /**
     * A string or text indicating the unit of measurement.
     * Useful if you cannot provide a standard unit code for unitCode.
     * @var null|string
     */
    public ?string $unitText ;
}