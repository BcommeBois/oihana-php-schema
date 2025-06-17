<?php

namespace org\schema;

use org\schema\enumerations\DayOfWeek;

/**
 * ServicePeriod represents a duration with some constraints about cutoff time and business days. This is used e.g. in shipping for handling times or transit time.
 * @see https://schema.org/ServicePeriod
 */
class ServicePeriod extends StructuredValue
{
    /**
     * Days of the week when the merchant typically operates, indicated via opening hours markup.
     */
    public null|array|OpeningHoursSpecification|DayOfWeek $businessDays ;

    /**
     * Order cutoff time allows merchants to describe the time after which they will no longer process orders received on that day.
     * For orders processed after cutoff time, one day gets added to the delivery time estimate. This property is expected to be most typically used via the ShippingRateSettings publication pattern.
     * The time is indicated using the ISO-8601 Time format, e.g. "23:30:00-05:00" would represent 6:30 pm Eastern Standard Time (EST) which is 5 hours behind Coordinated Universal Time (UTC).
     * @var int|String|null
     */
    public null|int|String $cutoffTime ;

    /**
     * The duration of the item (movie, audio recording, event, etc.) in ISO 8601 duration format.
     * @var string|int|Duration|QuantitativeValue|null
     */
    public null|string|int|Duration|QuantitativeValue $duration ;
}


