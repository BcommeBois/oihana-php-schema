<?php

namespace xyz\oihana\schema\enumerations;

use org\schema\enumerations\PriceTypeEnumeration ;

/**
 * Enumerates different price components that together make up the total price for an offered product.
 * @see https://schema.org/PriceTypeEnumeration
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\enumerations
 * @since   1.3.0
 */
class PriceType extends PriceTypeEnumeration
{
    /**
     * Represents the schema.org URL for "Buying price discounted" constant.
     * Note : Prix d'achat avec les variations (remise et plus-values) et le taux de marge appliqués (fr)
     */
    public const string BUYING_DISCOUNTED = 'https://schema.oihana.xyz/BuyingPriceDiscounted' ;

    /**
     * Represents the schema.org URL for "Buying price reference" constant.
     * Note : Prix d'achat de référence chez le fournisseur (fr)
     */
    public const string BUYING_REFERENCE = 'https://schema.oihana.xyz/BuyingPriceReference' ;

    /**
     * Represents the schema.org URL for "Buying price reference with margin" constant.
     * Note : Prix d'achat de référence avec la marge d'achat appliquée. (fr)
     */
    public const string BUYING_WITH_MARGIN = 'https://schema.oihana.xyz/BuyingPriceWithMargin' ;

    /**
     * Represents the schema.org URL for "Cost of Goods Sold" constant.
     * Note: Prix de revient final du produit (fr)
     */
    public const string COGS = 'https://schema.oihana.xyz/COGS' ;

    /**
     * Represents the schema.org URL for "Loaded rate" constant.
     * The "loaded rate" refers to a comprehensive pricing structure that includes not only the base cost
     * of a product or service but also all additional associated costs : taxes, fees, shipping, handling, insurance, etc.
     * Ex: loaded rate = ( buying price + extra charges ) / storage rate
     * Note: Prix chargé du produit (fr)
     */
    public const string LOADED_RATE = 'https://schema.oihana.xyz/LoadedRate' ;

    /**
     * Represents the schema.org URL for "Selling price forced" constant.
     * Note : Prix de vente fixé pour calculer un prix de vente sans frais, etc. (T4 fixé) (fr)
     */
    public const string SELLING_FORCED = 'https://schema.oihana.xyz/SellingPriceForced' ;

    /**
     * Represents the schema.org URL for "Selling price reference" constant.
     * Note : Prix de vente de référence pour les clients (T4) (fr)
     */
    public const string SELLING_REFERENCE = 'https://schema.oihana.xyz/SellingPriceReference' ;
}