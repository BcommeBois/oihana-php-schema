<?php

namespace xyz\oihana\schema\constants\traits\organizations ;

/**
 * The enumeration of all the company properties constants.
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\constants\traits\organizations
 * @since   1.3.0
 */
trait Company
{
    /**
     * A property-value pair representing an additional characteristic of the entity,
     */
    public const string ADDITIONAL_PROPERTY = 'additionalProperty' ;

    /**
     * A category for the item.
     */
    public const string CATEGORY = 'category' ;

    /**
     * The delivery method of the customer.
     */
    public const string DELIVERY_METHOD = 'deliveryMethod' ;

    /**
     * A minimum order cost above (or at) which the shipping rate becomes free.
     */
    public const string FREE_SHIPPING_THRESHOLD = 'freeShippingThreshold' ;

    /**
     * The industry associated with the job position or Organization.
     */
    public const string INDUSTRY = 'industry' ;

    /**
     * The type of invoice for the customer.
     */
    public const string INVOICE_TYPE = 'invoiceType' ;

    /**
     * The status of the customer.
     */
    public const string STATUS = 'status' ;

    /**
     * The default taxe rate information of the customer.
     */
    public const string VAT = 'vat' ;

    /**
     * The website(s) of the resource.
     */
    public const string WEBSITE = 'website' ;
}