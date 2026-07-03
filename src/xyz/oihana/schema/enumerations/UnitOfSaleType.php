<?php

namespace xyz\oihana\schema\enumerations;

use org\schema\enumerations\BusinessEntityType as SchemaBusinessEntityType ;

/**
 * Enumeration of allowed values for the {@see Product::$unitOfSale} property.
 *
 * Each value represents the **level at which the product is sold**:
 * - UNIT    : single item
 * - PACKAGE : grouped items (box, carton, etc.)
 * - PARCEL  : larger aggregation (pallet, parcel of packages)
 *
 * The values are expressed as URIs to be compatible with JSON-LD and Schema.org conventions.
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\enumerations
 * @since   1.3.0
 */
class UnitOfSaleType extends SchemaBusinessEntityType
{
    /**
     * The basic unit of sale of a product (single item).
     */
    public const string UNIT = 'https://schema.oihana.xyz#Unit' ;

    /**
     * The package unit of sale (e.g., box, carton).
     */
    public const string PACKAGE = 'https://schema.oihana.xyz#Package' ;

    /**
     * The parcel unit of sale (e.g., pallet, collection of packages).
     */
    public const string PARCEL = 'https://schema.oihana.xyz#Parcel' ;

}