<?php

namespace org\schema;

use org\schema\enumerations\status\OrderStatus;

/**
 * An order item is a line of an order.
 * It includes the quantity and shipping details of a bought offer.
 * @see https://schema.org/OrderItem
 */
class OrderItem extends Intangible
{
    /**
     * The delivery of the parcel related to this order or order item.
     * @var ParcelDelivery|null
     */
    public ?ParcelDelivery $orderDelivery ;

    /**
     * The identifier of the order item.
     * @var string|int|null
     */
    public null|string|int $orderItemNumber ;

    /**
     * The current status of the order item.
     * @var null|string|DefinedTerm|OrderStatus
     */
    public null|string|DefinedTerm|OrderStatus $orderItemStatus ;

    /**
     * The identifier of the order item.
     * @var null|int|float|QuantitativeValue
     */
    public null|int|float|QuantitativeValue $orderQuantity ;

    /**
     * The item ordered.
     * @var array|Product|Service|OrderItem|null
     */
    public null|array|Product|Service|OrderItem $orderedItem ;
}