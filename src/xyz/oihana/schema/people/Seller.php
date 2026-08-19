<?php

namespace xyz\oihana\schema\people ;

use oihana\reflect\attributes\HydrateWith;

use org\schema\OpeningHoursSpecification;

/**
 * A seller for a subsidiary organization.
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\people
 * @since   1.3.0
 */
class Seller extends Person
{
    /**
     * When this salesperson is available for appointments.
     *
     * The weekly hours they take meetings in, and the periods they take none : a
     * specification stating a range of dates and no hours is a closure — a holiday,
     * a training week — and one stating days and hours is the ordinary rhythm.
     *
     * 🔑 **Silence is not an opening.** Whoever offers a slot needs a positive
     * statement of when it may be offered ; without one, three in the morning is
     * bookable. Saying nothing here means no slot can be proposed, which is the
     * safe reading rather than the permissive one.
     *
     * Reuses the name, the shape and the meaning
     * {@see \org\schema\Service::$hoursAvailable} already carries. schema.org
     * publishes the property on a contact point and on a service, never on a
     * person ; the term is borrowed rather than invented, because a person taking
     * appointments states exactly the same thing.
     *
     * @var null|array|OpeningHoursSpecification
     * @since 1.5.0
     */
    #[HydrateWith(OpeningHoursSpecification::class)]
    public null|array|OpeningHoursSpecification $hoursAvailable ;
}
