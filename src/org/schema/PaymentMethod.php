<?php

namespace org\schema;

use org\schema\enumerations\PaymentMethodType;

/**
 * An event happening at a certain time and location, such as a concert, lecture, or festival. Repeated events may be structured as separate Event objects.
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


