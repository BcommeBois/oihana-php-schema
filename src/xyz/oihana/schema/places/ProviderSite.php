<?php

namespace xyz\oihana\schema\places;

use xyz\oihana\schema\traits\SiteTrait;

/**
 * Represents a physical location or operational site specifically associated with a Provider (Supplier).
 *
 * A ProviderSite is a specialized extension of a Site used to define vendor-related
 * points of presence. It serves critical logistical roles such as supplier warehouses,
 * pickup locations (point d'enlèvement), return addresses, or provider administrative offices.
 * Functional roles are typically identified via the additionalProperty collection
 * (e.g., isShippingAddress, isDefaultAddress, isBillingAddress).
 *
 * @see https://schema.org/Place
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\places
 * @since   1.3.0
 */
class ProviderSite extends Site
{
    use SiteTrait ;
}