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

}