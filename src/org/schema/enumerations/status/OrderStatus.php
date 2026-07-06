<?php

namespace org\schema\enumerations\status;

use org\schema\enumerations\StatusEnumeration;

/**
 * Enumerated status values for Order.
 * @see https://schema.org/OrderStatus
 */
class OrderStatus extends StatusEnumeration
{
    /**
     * The order has been cancelled.
     */
    public const string ORDER_CANCELLED = 'https://schema.org/OrderCancelled' ;

    /**
     * The order has been delivered.
     */
    public const string ORDER_DELIVERED = 'https://schema.org/OrderDelivered' ;

    /**
     * The order is in transit.
     */
    public const string ORDER_IN_TRANSIT = 'https://schema.org/OrderInTransit' ;

    /**
     * The order has been received but payment is due.
     */
    public const string ORDER_PAYMENT_DUE = 'https://schema.org/OrderPaymentDue' ;

    /**
     * The order is available for pickup.
     */
    public const string ORDER_PICKUP_AVAILABLE = 'https://schema.org/OrderPickupAvailable' ;

    /**
     * There is a problem with the order.
     */
    public const string ORDER_PROBLEM = 'https://schema.org/OrderProblem' ;

    /**
     * The order is being processed.
     */
    public const string ORDER_PROCESSING = 'https://schema.org/OrderProcessing' ;

    /**
     * The order has been returned.
     */
    public const string ORDER_RETURNED = 'https://schema.org/OrderReturned' ;
}