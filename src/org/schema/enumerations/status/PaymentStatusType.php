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
 *
 * The constants are the values a payment status is written with ; the member
 * classes describe the status itself as an entity. Both name the same terms.
 *
 * @see https://schema.org/PaymentStatusType
 */
class PaymentStatusType extends StatusEnumeration
{
    /**
     * An automatic payment system is in place and will be used.
     */
    public const string PAYMENT_AUTOMATICALLY_APPLIED = 'https://schema.org/PaymentAutomaticallyApplied' ;

    /**
     * The payment has been received and processed.
     */
    public const string PAYMENT_COMPLETE = 'https://schema.org/PaymentComplete' ;

    /**
     * The payee received the payment, but it was declined for some reason.
     */
    public const string PAYMENT_DECLINED = 'https://schema.org/PaymentDeclined' ;

    /**
     * The payment is due, but still within an acceptable time to be received.
     */
    public const string PAYMENT_DUE = 'https://schema.org/PaymentDue' ;

    /**
     * The payment is due and considered late.
     */
    public const string PAYMENT_PAST_DUE = 'https://schema.org/PaymentPastDue' ;
}
