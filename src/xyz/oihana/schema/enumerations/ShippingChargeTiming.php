<?php

namespace xyz\oihana\schema\enumerations;

use org\schema\Enumeration;

/**
 * When a delivery method's carriage amount is settled : at order time, or
 * recomputed at delivery time.
 *
 * Answers a question {@see \xyz\oihana\schema\thesaurus\DeliveryMethodTerm}
 * does not otherwise carry : `shippingRate` and `freeShippingThreshold` say
 * what the carriage costs, `chargeTiming` says when that figure is fixed.
 *
 * The value is free : a consumer may use one of the constants below, its own
 * free-text label, or a subclass adding project-specific timings.
 *
 * | Constant    | Description                                                             | Value                                                      |
 * |-------------|--------------------------------------------------------------------------|-------------------------------------------------------------|
 * | AT_DELIVERY | The carriage is recomputed when the goods actually ship.                | https://schema.oihana.xyz/ShippingChargeTiming#AtDelivery  |
 * | AT_ORDER    | The carriage is settled when the order is placed, and no longer moves.  | https://schema.oihana.xyz/ShippingChargeTiming#AtOrder     |
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\enumerations
 * @since   1.4.0
 */
class ShippingChargeTiming extends Enumeration
{
    /**
     * The carriage is recomputed when the goods actually ship.
     */
    public const string AT_DELIVERY = 'https://schema.oihana.xyz/ShippingChargeTiming#AtDelivery' ;

    /**
     * The carriage is settled when the order is placed, and no longer moves.
     */
    public const string AT_ORDER = 'https://schema.oihana.xyz/ShippingChargeTiming#AtOrder' ;
}
