<?php

namespace xyz\oihana\schema\enumerations;

use org\schema\enumerations\PriceComponentTypeEnumeration ;

/**
 * Enumerates different price components that together make up the total price for an offered product.
 * @see https://schema.org/PriceComponentTypeEnumeration
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\enumerations
 * @since   1.3.0
 */
class PriceComponentType extends PriceComponentTypeEnumeration
{
    /**
     * Represents the schema.org URL for "Additional Fee" constant.
     * Note : frais additionnels (fr)
     */
    public const string ADDITIONAL_FEE = 'https://schema.oihana.xyz/AdditionalFee' ;

    /**
     * Represents the schema.org URL for "Buying Price Margin" constant.
     * Note : Marge appliquée sur le prix d'achat (fr)
     */
    public const string BUYING_PRICE_MARGIN = 'https://schema.oihana.xyz/BuyingPriceMargin' ;

    /**
     * Represents the schema.org URL for "Buying Variations" constant.
     * Note : Total des variations sur le prix d'achat (remise et plus-values) (fr)
     */
    public const string BUYING_PRICE_VARIATIONS = 'https://schema.oihana.xyz/BuyingPriceVariations' ;

    /**
     * Represents the schema.org URL for "Cost Factor" constant.
     * Used the cost factor to describe a multiplier applied to direct costs to include a portion of indirect costs or overhead expenses.
     * This allows for the calculation of the total cost of a product or service.
     */
    public const string COST_FACTOR = 'https://schema.oihana.xyz/CostFactor' ;

    /**
     * Represents the schema.org URL for "Deposit" constant.
     * Note : consigne (fr)
     */
    public const string DEPOSIT = 'https://schema.oihana.xyz/Deposit' ;

    /**
     * Represents the schema.org URL for "Discount" constant.
     * Note : remise (fr)
     */
    public const string DISCOUNT = 'https://schema.oihana.xyz/Discount' ;

    /**
     * Represents the schema.org URL for "Environmental Fee" constant.
     * Note : éco-participation (fr)
     */
    public const string ENVIRONMENTAL_FEE = 'https://schema.oihana.xyz/EnvironmentalFee' ;

    /**
     * Represents the schema.org URL for "First Extra Fee" constant.
     * Note : frais libres 1 (fr)
     */
    public const string EXTRA_1_FEE = 'https://schema.oihana.xyz/Extra1Fee' ;

    /**
     * Represents the schema.org URL for "Second Extra Fee" constant.
     * Note : frais libres 2 (fr)
     */
    public const string EXTRA_2_FEE = 'https://schema.oihana.xyz/Extra2Fee' ;

    /**
     * Represents the schema.org URL for "Packaging" constant.
     * Note : emballage (fr)
     */
    public const string PACKAGING = 'https://schema.oihana.xyz/Packaging' ;

    /**
     * Represents the schema.org URL for "Price Factor" constant.
     * Note : Taux fixé du prix de vente de reference (T4 fixé)
     */
    public const string PRICE_FACTOR = 'https://schema.oihana.xyz/PriceFactor' ;

    /**
     * Represents the schema.org URL for "Selling Factor" constant.
     * Note : Taux spécifique à appliquer sur un prix de vente (fr)
     */
    public const string SELLING_FACTOR = 'https://schema.oihana.xyz/SellingFactor' ;

    /**
     * Represents the schema.org URL for "Selling Margin" constant.
     * Note : Marge dégagée sur une vente — prix de vente moins prix de revient (fr)
     */
    public const string SELLING_MARGIN = 'https://schema.oihana.xyz/SellingMargin' ;

    /**
     * Represents the schema.org URL for "Selling Reference Factor" constant.
     * Note : Taux du prix de vente de reference (T4) (fr)
     */
    public const string SELLING_REFERENCE_FACTOR = 'https://schema.oihana.xyz/SellingReferenceFactor' ;

    /**
     * Represents the schema.org URL for "Service Fee" constant.
     * Note : frais de service (fr)
     */
    public const string SERVICE_FEE = 'https://schema.oihana.xyz/ServiceFee' ;

    /**
     * Represents the schema.org URL for "Shipping Fee" constant.
     * Note : frais de transport (fr)
     */
    public const string SHIPPING_FEE = 'https://schema.oihana.xyz/ShippingFee' ;

    /**
     * Represents the schema.org URL for "Surcharge" constant.
     * Note : majoration (fr)
     */
    public const string SURCHARGE = 'https://schema.oihana.xyz/Surcharge' ;
}