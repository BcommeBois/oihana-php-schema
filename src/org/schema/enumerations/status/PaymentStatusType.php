<?php

namespace org\schema\enumerations\status;

use org\schema\enumerations\StatusEnumeration;

/**
 * A specific payment status. For example, PaymentDue, PaymentComplete, etc.
 * Enumeration members :
 * - PaymentAutomaticallyApplied
 * - PaymentComplete
 * - PaymentDeclined
 * - PaymentDue
 * - PaymentPastDue
 * @see https://schema.org/PaymentStatusType
 */
class PaymentStatusType extends StatusEnumeration
{
    /**
     * The payment has been automatically applied, e.g. from a previously stored payment method.
     */
    public const string PAYMENT_AUTOMATICALLY_APPLIED = 'https://schema.org/PaymentAutomaticallyApplied' ;

    /**
     * The payment has been received and processed.
     */
    public const string PAYMENT_COMPLETE = 'https://schema.org/PaymentComplete' ;

    /**
     * The payee received a rejection from the payment provider.
     */
    public const string PAYMENT_DECLINED = 'https://schema.org/PaymentDeclined' ;

    /**
     * The payment is due but has not yet been received.
     */
    public const string PAYMENT_DUE = 'https://schema.org/PaymentDue' ;

    /**
     * The payment is due and is now past its due date.
     */
    public const string PAYMENT_PAST_DUE = 'https://schema.org/PaymentPastDue' ;
}