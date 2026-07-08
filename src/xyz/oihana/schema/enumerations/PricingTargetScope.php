<?php

namespace xyz\oihana\schema\enumerations;

use org\schema\Enumeration;

/**
 * The granularity of the buyer a {@see \xyz\oihana\schema\products\PricingCondition}
 * targets — from a single named customer up to every customer.
 *
 * A pricing condition is resolved most-specific-first : an `INDIVIDUAL` rule
 * outranks a `GROUP` one, which outranks a `COMPANY` one, which outranks the
 * catch-all `ALL`. The value paired with the scope (the group id, the company
 * id…) is carried by the selector's `customerId`.
 *
 * The value is free : a consumer may use one of the constants below, its own
 * free-text label, or a subclass adding project-specific scopes.
 *
 * | Constant   | Description                                          | Value                                                    |
 * |------------|------------------------------------------------------|----------------------------------------------------------|
 * | ALL        | Every customer (the catch-all, least specific).      | https://schema.oihana.xyz/PricingTargetScope#All         |
 * | COMPANY    | Every customer attached to a company.                | https://schema.oihana.xyz/PricingTargetScope#Company     |
 * | GROUP      | Every customer attached to a purchasing group.       | https://schema.oihana.xyz/PricingTargetScope#Group       |
 * | INDIVIDUAL | One named customer (the most specific).              | https://schema.oihana.xyz/PricingTargetScope#Individual  |
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\enumerations
 * @since   1.4.0
 */
class PricingTargetScope extends Enumeration
{
    /**
     * Every customer (the catch-all, least specific scope).
     */
    public const string ALL = 'https://schema.oihana.xyz/PricingTargetScope#All' ;

    /**
     * Every customer attached to a company.
     */
    public const string COMPANY = 'https://schema.oihana.xyz/PricingTargetScope#Company' ;

    /**
     * Every customer attached to a purchasing group.
     */
    public const string GROUP = 'https://schema.oihana.xyz/PricingTargetScope#Group' ;

    /**
     * One named customer (the most specific scope).
     */
    public const string INDIVIDUAL = 'https://schema.oihana.xyz/PricingTargetScope#Individual' ;
}
