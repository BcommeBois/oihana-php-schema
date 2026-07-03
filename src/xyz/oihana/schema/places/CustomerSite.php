<?php

namespace xyz\oihana\schema\places;

use xyz\oihana\schema\places\Site;
use xyz\oihana\schema\traits\SiteTrait;

/**
 * Represents a physical location or operational site specifically associated with a Customer.
 *
 * A CustomerSite is a specialized extension of a Site used to define the specific
 * points of presence for a client. It encompasses various logistical and
 * administrative roles such as delivery points, job sites (construction), or
 * billing offices.
 *
 * Functional roles are typically identified via the additionalProperty collection
 * (e.g., isBillingAddress, isConstructionSite, isDeliveryAddress).
 *
 * @see https://schema.org/Place
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\places
 * @since   1.3.0
 */
class CustomerSite extends Site
{
    use SiteTrait ;
}