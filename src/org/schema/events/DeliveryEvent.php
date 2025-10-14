<?php

namespace org\schema\events;

use org\schema\enumerations\DeliveryMethod;
use org\schema\Event;

/**
 * An event involving the delivery of an item.
 * @see https://schema.org/DeliveryEvent
 */
class DeliveryEvent extends Event
{
    /**
     * Password, PIN, or access code needed for delivery (e.g. from a locker).
     * @var string|null
     */
    public ?string $accessCode ;

    /**
     * When the item is available for pickup from the store, locker, etc.
     * @var null|string|int
     */
    public null|string|int $availableFrom ;

    /**
     * After this date, the item will no longer be available for pickup.
     * @var null|string|int
     */
    public null|string|int $availableThrough ;

    /**
     * Method used for delivery or shipping.
     * @var array|DeliveryMethod|null
     */
    public null|array|DeliveryMethod $hasDeliveryMethod ;
}