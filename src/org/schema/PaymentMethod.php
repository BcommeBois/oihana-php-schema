<?php

namespace org\schema;

use org\schema\enumerations\PaymentMethodType;

/**
 * A payment method is a standardized procedure for transferring the monetary amount for a purchase.
 *
 * Payment methods are characterized by the legal and technical structures used, and by the organization or group carrying out the transaction.
 *
 * @see https://schema.org/PaymentMethod
 */
class PaymentMethod extends Intangible
{
    /**
     * The type of a payment method.
     * - ByBankTransferInAdvance
     * - ByInvoice
     * - Cash
     * - CheckInAdvance
     * - COD
     * - DirectDebit
     * - GoogleCheckout
     * - InStorePrepay
     * - Paypal
     * - PaySwarm
     * - PhoneCarrierPayment
     * @var null|string|DefinedTerm|PaymentMethodType
     */
    public null|string|DefinedTerm|PaymentMethodType $paymentMethodType ;
}


