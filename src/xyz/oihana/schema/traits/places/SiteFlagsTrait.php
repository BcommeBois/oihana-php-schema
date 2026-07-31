<?php

namespace xyz\oihana\schema\traits\places ;

use org\schema\traits\helpers\GetAdditionalPropertyTrait;

use xyz\oihana\schema\constants\Oihana;

/**
 * Answers, on a site, what role it plays for the party that owns it.
 *
 * One address bills, another receives the goods, a third is a construction site.
 * A site may claim several of these at once, and — this is the part that bites —
 * it may claim **none**: a record whose list was never filled in answers false
 * everywhere, which is a fact about the record, not about the place.
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\traits\places
 * @since   1.4.0
 */
trait SiteFlagsTrait
{
    use GetAdditionalPropertyTrait ;

    /**
     * Whether this site is the billing address.
     */
    public function isBillingAddress() :bool
    {
        return $this->hasAdditionalPropertyFlag( Oihana::IS_BILLING_ADDRESS ) ;
    }

    /**
     * Whether this site is a construction site.
     */
    public function isConstructionSite() :bool
    {
        return $this->hasAdditionalPropertyFlag( Oihana::IS_CONSTRUCTION_SITE ) ;
    }

    /**
     * Whether this site is the default address.
     */
    public function isDefaultAddress() :bool
    {
        return $this->hasAdditionalPropertyFlag( Oihana::IS_DEFAULT_ADDRESS ) ;
    }

    /**
     * Whether this site receives the deliveries.
     */
    public function isDeliveryAddress() :bool
    {
        return $this->hasAdditionalPropertyFlag( Oihana::IS_DELIVERY_ADDRESS ) ;
    }

    /**
     * Whether this site is the shipping address.
     */
    public function isShippingAddress() :bool
    {
        return $this->hasAdditionalPropertyFlag( Oihana::IS_SHIPPING_ADDRESS ) ;
    }
}