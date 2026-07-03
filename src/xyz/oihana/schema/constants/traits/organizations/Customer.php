<?php

namespace xyz\oihana\schema\constants\traits\organizations ;

/**
 * The enumeration of all the customer properties constants.
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\constants\traits\organizations
 * @since   1.3.0
 */
trait Customer
{
    /**
     * The companies assigned to the customer.
     */
    public const string ASSIGNED_COMPANY = 'assignedCompany' ;

    /**
     * The point of sale assigned to the customer (warehouse, shop, etc.).
     */
    public const string ASSIGNED_POS = 'assignedPOS' ;

    /**
     * The seller(s) assigned to the customer.
     */
    public const string ASSIGNED_SELLER = 'assignedSeller' ;

    /**
     * The credit status of the customer.
     */
    public const string CREDIT_STATUS = 'creditStatus' ;

    /**
     * The payment terms of the customer.
     */
    public const string PAYMENT_TERMS = 'paymentTerms' ;

    /**
     * The default price segmentation of the customer.
     */
    public const string PRICE_SEGMENTATION = 'priceSegmentation' ;

    /**
     * The seller relation key — graph-side filter on the `seller_has_customer`
     * edge (by Arango `_key`), distinct from the embedded `assignedSeller` join.
     */
    public const string SELLER = 'seller' ;

    /**
     * The unloading method of the customer.
     */
    public const string UNLOADING_METHOD = 'unloadingMethod' ;

    // -------- Additional Properties

    /**
     * Determines whether an acknowledging receipt should be generated for the customer.
     */
    public const string GENERATE_ACKNOWLEDGING_RECEIPT = "generateAcknowledgingReceipt" ;

    /**
     * Specifies that invoices should be issued every X days.
     */
    public const string INVOICE_ISSUE_INTERVAL = "invoiceIssueInterval" ;

    /**
     * Specifies the common information to include in the customer's order picking list.
     */
    public const string ORDER_PICKING_LIST_INFO = 'orderPickingListInfo' ;

    /**
     * Indicates whether the customer's order reference should be displayed.
     */
    public const string ORDER_SHOW_IDENTIFIER = 'orderShowIdentifier' ;

    /**
     * Determines whether the invoice should be printed and sent by mail.
     */
    public const string PRINT_AND_MAIL_INVOICE = "printAndMailInvoice" ;

    /**
     * Indicates if the customer's applications should be displayed.
     */
    public const string SHOW_APPLICATIONS = "showApplications" ;
}