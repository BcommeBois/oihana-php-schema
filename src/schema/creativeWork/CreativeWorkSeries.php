<?php

namespace org\schema\creativeWork ;

use DateTime;
use org\schema\CreativeWork;

/**
 * A picture or diagram made with a pencil, pen, or crayon rather than paint.
 * @see https://schema.org/CreativeWorkSeries
 */
class CreativeWorkSeries extends CreativeWork
{
    /**
     * The end date and time of the item (in ISO 8601 date format).
     * @var string|int|DateTime|null
     */
    public null|string|int|DateTime $endDate ;

    /**
     * The International Standard Serial Number (ISSN) that identifies this serial publication. You can repeat this property to identify different formats of, or the linking ISSN (ISSN-L) for, this serial publication.
     * @var string|null
     */
    public null|string $issn ;

    /**
     * The start date and time of the item (in ISO 8601 date format).
     * @var string|int|DateTime|null
     */
    public null|string|int|DateTime $startDate ;
}


