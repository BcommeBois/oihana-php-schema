<?php

namespace xyz\oihana\schema\enumerations;

use org\schema\Enumeration;

/**
 * The granularity of the place a {@see PricingCondition} is valid at — from a single point of sale up to everywhere.
 *
 * Where {@see PricingTargetScope} answers *who* a condition targets and
 * {@see PricingItemScope} answers *what*, this enumeration answers *where*. A
 * condition is resolved most-specific-first : a `WAREHOUSE` rule outranks a
 * `COMPANY` one, which outranks a `GROUP` one, which outranks the catch-all
 * `ALL`. The value paired with the scope (the warehouse id, the company id…) is
 * carried by the selector's `areaServed`.
 *
 * The value is free : a consumer may use one of the constants below, its own
 * free-text label, or a subclass adding project-specific scopes.
 *
 * | Constant  | Description                                               | Value                                                    |
 * |-----------|-----------------------------------------------------------|----------------------------------------------------------|
 * | ALL       | Everywhere (the catch-all, least specific).               | https://schema.oihana.xyz/PricingAreaScope#All           |
 * | COMPANY   | Every point of sale of a company.                         | https://schema.oihana.xyz/PricingAreaScope#Company       |
 * | GROUP     | Every point of sale of a group.                           | https://schema.oihana.xyz/PricingAreaScope#Group         |
 * | WAREHOUSE | One point of sale / warehouse (the most specific).        | https://schema.oihana.xyz/PricingAreaScope#Warehouse     |
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\enumerations
 * @since   1.4.0
 */
class PricingAreaScope extends Enumeration
{
    /**
     * Everywhere (the catch-all, least specific scope).
     */
    public const string ALL = 'https://schema.oihana.xyz/PricingAreaScope#All' ;

    /**
     * Every point of sale of a company.
     */
    public const string COMPANY = 'https://schema.oihana.xyz/PricingAreaScope#Company' ;

    /**
     * Every point of sale of a group.
     */
    public const string GROUP = 'https://schema.oihana.xyz/PricingAreaScope#Group' ;

    /**
     * One point of sale / warehouse (the most specific scope).
     */
    public const string WAREHOUSE = 'https://schema.oihana.xyz/PricingAreaScope#Warehouse' ;
}