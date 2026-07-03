<?php

namespace xyz\oihana\schema\constants\traits\places;

/**
 * The enumeration of all the site properties constants.
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\constants\traits\places
 * @since   1.3.0
 */
trait Site
{
    /**
     * The main delivery method of the site.
     */
    public const string DELIVERY_METHOD = 'deliveryMethod'  ;

    /**
     * Determines whether the address is a billing address.
     */
    public const string IS_BILLING_ADDRESS = "isBillingAddress";

    /**
     * Determines whether the place is a construction site.
     */
    public const string IS_CONSTRUCTION_SITE = "isConstructionSite";

    /**
     * Determines whether the address is the default address.
     */
    public const string IS_DEFAULT_ADDRESS = "isDefaultAddress";

    /**
     * Determines whether the address is a delivery address.
     */
    public const string IS_DELIVERY_ADDRESS = "isDeliveryAddress";

    /**
     * Determines whether the address is a shipping address.
     */
    public const string IS_SHIPPING_ADDRESS = "isShippingAddress";

    /**
     * Identifies the owner of the place.
     * Typically refers to a Person or Organization that owns or manages the place.
     */
    public const string OWNED_BY = 'ownedBy' ;

    /**
     * The position of an item in a series or sequence of items.
     */
    public const string POSITION = 'position' ;
}