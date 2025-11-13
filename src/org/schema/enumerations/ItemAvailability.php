<?php

namespace org\schema\enumerations;

use org\schema\Enumeration;

/**
 * A list of possible product availability options.
 * Members:
 * - BackOrder
 * - Discontinued
 * - InStock
 * - InStoreOnly
 * - LimitedAvailability
 * - MadeToOrder
 * - OnlineOnly
 * - OutOfStock
 * - PreOrder
 * - PreSale
 * - Reserved
 * - SoldOut
 * @see https://schema.org/ItemAvailability
 */
class ItemAvailability extends Enumeration
{
    /**
     * Indicates that the item is available on back order.
     */
    public const string BACK_ORDER = 'https://schema.org/BackOrder' ;

    /**
     * Indicates that the item has been discontinued.
     */
    public const string DISCONTINUED = 'https://schema.org/Discontinued' ;

    /**
     * Indicates that the item is in stock.
     */
    public const string IN_STOCK = 'https://schema.org/InStock' ;

    /**
     * Indicates that the item is available only at physical locations.
     */
    public const string IN_STORE_ONLY = 'https://schema.org/InStoreOnly' ;

    /**
     * Indicates that the item has limited availability.
     */
    public const string LIMITED_AVAILABILITY = 'https://schema.org/LimitedAvailability' ;

    /**
     * Indicates that the item is made to order (custom made).
     */
    public const string MADE_TO_ORDER = 'https://schema.org/MadeToOrder' ;

    /**
     * Indicates that the item is available only online.
     */
    public const string ONLINE_ONLY = 'https://schema.org/OnlineOnly' ;

    /**
     * Indicates that the item is out of stock.
     */
    public const string OUT_OF_STOCK = 'https://schema.org/OutOfStock' ;

    /**
     * Indicates that the item is available for pre-order.
     */
    public const string PRE_ORDER = 'https://schema.org/PreOrder' ;

    /**
     * Indicates that the item is available for ordering and delivery before general availability.
     */
    public const string PRE_SALE = 'https://schema.org/PreSale' ;

    /**
     * Indicates that the item is reserved and therefore not available.
     */
    public const string RESERVED = 'https://schema.org/Reserved' ;

    /**
     * Indicates that the item has sold out.
     */
    public const string SOLD_OUT = 'https://schema.org/SoldOut' ;
}