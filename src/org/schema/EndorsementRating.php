<?php

namespace org\schema ;

/**
 * An EndorsementRating is a rating that expresses some level of endorsement, for example inclusion in a "critic's pick" blog, a "Like" or "+1" on a social network.
 * @see https://schema.org/EndorsementRating
 */
class EndorsementRating extends Rating
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


