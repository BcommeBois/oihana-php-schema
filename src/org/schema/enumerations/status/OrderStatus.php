<?php

namespace org\schema\enumerations\status;

use org\schema\enumerations\StatusEnumeration;

/**
 * Enumerated status values for Order.
 * Members :
 * - OrderCancelled
 * - OrderDelivered
 * - OrderInTransit
 * - OrderPaymentDue
 * - OrderPickupAvailable
 * - OrderProblem
 * - OrderProcessing
 * - OrderReturned
 * @see https://schema.org/OrderStatus
 */
class OrderStatus extends StatusEnumeration
{

}