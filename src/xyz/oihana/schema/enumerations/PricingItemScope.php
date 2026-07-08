<?php

namespace xyz\oihana\schema\enumerations;

use org\schema\Enumeration;

/**
 * The granularity of the item a {@see \xyz\oihana\schema\products\PricingCondition}
 * applies to — from a single product up to every item.
 *
 * A pricing condition is resolved most-specific-first : a `PRODUCT` rule
 * outranks a `CATEGORY` one (itself refined by `categoryLevel` when categories
 * are hierarchical), which outranks a `PROVIDER` one, which outranks the
 * catch-all `ALL`. The value paired with the scope (the product id, the
 * category id, the provider id…) is carried by the selector's `itemId`.
 *
 * The value is free : a consumer may use one of the constants below, its own
 * free-text label, or a subclass adding project-specific scopes.
 *
 * | Constant | Description                                        | Value                                                |
 * |----------|----------------------------------------------------|------------------------------------------------------|
 * | ALL      | Every item (the catch-all, least specific).        | https://schema.oihana.xyz/PricingItemScope#All       |
 * | CATEGORY | Every product of a category (see `categoryLevel`). | https://schema.oihana.xyz/PricingItemScope#Category   |
 * | PRODUCT  | One product (the most specific).                    | https://schema.oihana.xyz/PricingItemScope#Product    |
 * | PROVIDER | Every product supplied by a provider.              | https://schema.oihana.xyz/PricingItemScope#Provider   |
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\enumerations
 * @since   1.4.0
 */
class PricingItemScope extends Enumeration
{
    /**
     * Every item (the catch-all, least specific scope).
     */
    public const string ALL = 'https://schema.oihana.xyz/PricingItemScope#All' ;

    /**
     * Every product of a category (refined by `categoryLevel` when hierarchical).
     */
    public const string CATEGORY = 'https://schema.oihana.xyz/PricingItemScope#Category' ;

    /**
     * One product (the most specific scope).
     */
    public const string PRODUCT = 'https://schema.oihana.xyz/PricingItemScope#Product' ;

    /**
     * Every product supplied by a provider.
     */
    public const string PROVIDER = 'https://schema.oihana.xyz/PricingItemScope#Provider' ;
}
