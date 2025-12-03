<?php

namespace org\schema\enumerations;

use org\schema\Enumeration;

/**
 * A payment method is a standardized procedure for transferring the monetary amount for a purchase.
 * Payment methods are characterized by the legal and technical structures used, and by the organization or group carrying out the transaction.
 * The following legacy values should be accepted:
 * - http://purl.org/goodrelations/v1#ByBankTransferInAdvance
 * - http://purl.org/goodrelations/v1#ByInvoice
 * - http://purl.org/goodrelations/v1#Cash
 * - http://purl.org/goodrelations/v1#CheckInAdvance
 * - http://purl.org/goodrelations/v1#COD
 * - http://purl.org/goodrelations/v1#DirectDebit
 * - http://purl.org/goodrelations/v1#GoogleCheckout
 * - http://purl.org/goodrelations/v1#PayPal
 * - http://purl.org/goodrelations/v1#PaySwarm
 * @see https://schema.org/PaymentMethod
 */
class PaymentMethodType extends Enumeration
{
    public const string BY_BANK_TRANSFER_IN_ADVANCE = 'http://purl.org/goodrelations/v1#ByBankTransferInAdvance' ;
    public const string BY_INVOICE                  = 'http://purl.org/goodrelations/v1#ByInvoice' ;
    public const string CASH                        = 'http://purl.org/goodrelations/v1#Cash' ;
    public const string CHECK_IN_ADVANCE            = 'http://purl.org/goodrelations/v1#CheckInAdvance' ;
    public const string COD                         = 'http://purl.org/goodrelations/v1#COD' ;
    public const string DIRECT_DEBIT                = 'http://purl.org/goodrelations/v1#DirectDebit' ;
    public const string GOOGLE_CHECKOUT             = 'http://purl.org/goodrelations/v1#GoogleCheckout' ;
    public const string PAY_PAL                     = 'http://purl.org/goodrelations/v1#PayPal' ;
    public const string PAY_SWARM                   = 'http://purl.org/goodrelations/v1#PaySwarm' ;
}