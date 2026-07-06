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

}