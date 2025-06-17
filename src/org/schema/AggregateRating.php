<?php

namespace org\schema ;

/**
 * The average rating based on multiple ratings or reviews.
 * @see https://schema.org/AggregateRating
 */
class AggregateRating extends Rating
{
    /**
     * The item that is being reviewed/rated.
     * @var null|Thing
     */
    public ?Thing $itemReviewed ;

    /**
     * The count of total number of ratings.
     * @var ?int
     */
    public ?int $ratingCount ;

    /**
     * The count of total number of reviews.
     * @var ?int
     */
    public ?int $reviewCount ;
}


