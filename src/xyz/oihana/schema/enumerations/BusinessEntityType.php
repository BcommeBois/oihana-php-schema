<?php

namespace xyz\oihana\schema\enumerations;

use org\schema\enumerations\BusinessEntityType as SchemaBusinessEntityType ;

/**
 * A extended custom business entity type of the product's offers.
 *
 * @see https://schema.org/BusinessEntityType
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\enumerations
 * @since   1.3.0
 */
class BusinessEntityType extends SchemaBusinessEntityType
{
    /**
     * A non-professional buyer acquiring supplies for home construction or repair work.
     */
    public const string DIY_PROJECT = 'https://schema.oihana.xyz#DiyProject' ;

    /**
     * An individual customer acquiring supplies.
     */
    public const string INDIVIDUAL = 'https://schema.oihana.xyz#Individual' ;

    /**
     * Intercompany transactions are billed at internal rates.
     */
    public const string INTERCOMPANY = 'https://schema.oihana.xyz#Intercompany' ;

    /**
     * The reference price for professional buyers.
     */
    public const string PROFESSIONAL = 'https://schema.oihana.xyz#Professional' ;

    /**
     * The public rate for one-time buyers.
     */
    public const string PUBLIC = 'https://schema.oihana.xyz#Public' ;

    /**
     * A reseller of products.
     */
    public const string RESELLER = 'https://schema.oihana.xyz#Reseller';

    /**
     * A wholesale customers benefit from volume discounts.
     */
    public const string WHOLESALE = 'https://schema.oihana.xyz#Wholesale' ;
}