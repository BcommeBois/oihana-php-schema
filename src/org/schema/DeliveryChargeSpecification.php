<?php

namespace org\schema;

use org\schema\enumerations\DeliveryMethod;
use org\schema\places\AdministrativeArea;

/**
 * A structured value representing a price or price range. Typically, only the subclasses of this type are used for markup. It is recommended to use MonetaryAmount to describe independent amounts of money such as a salary, credit
 * @see https://schema.org/DeliveryChargeSpecification
 */
class DeliveryChargeSpecification extends PriceSpecification
{
    /**
     * The delivery method(s) to which the delivery charge or payment charge specification applies.
     * @var array|DeliveryMethod|null
     */
    public null|array|DeliveryMethod $appliesToDeliveryMethod ;

    /**
     * The geographic area where a service or offered item is provided.
     * @var null|int|string|Place|GeoShape|AdministrativeArea|array
     */
    public null|int|string|Place|GeoShape|AdministrativeArea|array $areaServed ;

    /**
     * The ISO 3166-1 (ISO 3166-1 alpha-2) or ISO 3166-2 code, the place, or the GeoShape for the geo-political region(s) for which the offer or delivery charge specification is valid.
     */
    public null|string|Place|GeoShape $eligibleRegion ;

    /**
     * The ISO 3166-1 (ISO 3166-1 alpha-2) or ISO 3166-2 code, the place, or the GeoShape for the geo-political region(s) for which the offer or delivery charge specification is not valid, e.g. a region where the transaction is not allowed.
     */
    public null|string|Place|GeoShape $ineligibleRegion ;
}