<?php

namespace org\schema\enumerations;

use org\schema\Enumeration;

/**
 * Enumerates different price types, for example list price, invoice price, and sale price.
 * @see https://schema.org/PriceTypeEnumeration
 */
class PriceTypeEnumeration extends Enumeration
{
    /**
     * Represents the schema.org URL for "Invoice Price" constant.
     * Note : prix de facturation (fr)
     */
    public const string INVOICE_PRICE = 'https://schema.org/InvoicePrice' ;

    /**
     * Represents the list price of an offered product.
     * Typically the same as the MSRP.
     * Note : prix catalogue (fr)
     */
    public const string LIST_PRICE = 'https://schema.org/ListPrice' ;

    /**
     * Represents the minimum advertised price ("MAP") (as dictated by the manufacturer) of an offered product.
     * Note : prix minimum annoncé par le fabricant (fr)
     */
    public const string MinimumAdvertisedPrice = 'https://schema.org/MinimumAdvertisedPrice' ;

    /**
     * Represents the manufacturer suggested retail price ("MSRP") of an offered product.
     * Note : prix de détail suggéré par le fabricant (fr)
     */
    public const string MSRP = 'https://schema.org/MSRP' ;

    /**
     * Represents the regular price of an offered product. This is usually the advertised price before a temporary sale.
     * Once the sale period ends the advertised price will go back to the regular price.
     * Note : prix normal du produit en dehors des périodes de changement temporaire du tarif (fr)
     */
    public const string REGULAR_PRICE = 'https://schema.org/RegularPrice' ;

    /**
     * Represents the suggested retail price ("SRP") of an offered product.
     * Note : prix de détail suggéré (fr)
     */
    public const string SRP = 'https://schema.org/SRP' ;

    /**
     * Represents a sale price (usually active for a limited period) of an offered product.
     * Note : prix de vente actuel du produit (fr)
     */
    public const string SALE_PRICE = 'https://schema.org/SalePrice' ;

    /**
     * Represents the strikethrough price (the previous advertised price) of an offered product.
     * Note : prix barré (fr)
     */
    public const string STRIKE_THROUGH_PRICE = 'http://schema/org/StrikethroughPrice' ;

}