<?php

namespace xyz\oihana\schema\constants\traits\organizations ;

/**
 * The enumeration of all the provider properties constants.
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\constants\traits\organizations
 * @since   1.3.0
 */
trait Provider
{
    public const string AMOUNT_CARRIAGE_PAID          = 'amountCarriagePaid' ;
    public const string CARRIER                       = 'carrier' ;
    public const string CATEGORY                      = 'category' ;
    public const string DELIVERY_METHOD               = 'deliveryMethod' ;
    public const string HAS_ACKNOWLEDGMENT_OF_RECEIPT = 'hasAcknowledgmentOfReceipt' ;
    public const string INVOICE_TYPE                  = 'invoiceType' ;
    public const string MINIMUM_ORDER_VALUE           = 'minimumOrderValue' ;
    public const string PRODUCT_INFO                  = 'productInfo' ;
    public const string PROVIDER_TYPE                 = 'providerType' ;
    public const string SHARE_CAPITAL                 = 'shareCapital' ;
    public const string SHIPPING_DELIVERY_TIME        = 'shippingDeliveryTime' ;
    public const string VALUED_ORDER                  = 'valuedOrder' ;
}