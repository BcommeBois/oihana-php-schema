<?php

namespace xyz\oihana\schema\constants\traits\thesaurus;

/**
 * The property name constants of the {@see \xyz\oihana\schema\thesaurus\DeliveryMethodTerm} class.
 *
 * The three keys duplicate values already exposed elsewhere in the library —
 * `shippingRate` by {@see \org\schema\constants\traits\OfferShippingDetails},
 * `freeShippingThreshold` and `vat` by
 * {@see \xyz\oihana\schema\constants\traits\organizations\Company} — which is
 * intentional : a constant repeated with an identical value composes without
 * conflict, and each entity keeps a self-contained vocabulary.
 *
 * @package xyz\oihana\schema\constants\traits\thesaurus
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.4.0
 */
trait DeliveryMethodTermTrait
{
    const string FREE_SHIPPING_THRESHOLD = 'freeShippingThreshold' ;
    const string SHIPPING_RATE           = 'shippingRate' ;
    const string VAT                     = 'vat' ;
}