<?php

namespace org\schema ;

/**
 * A review of an item - for example, of a restaurant, movie, or store.
 */
class Review extends CreativeWork
{
    /**
     * The item that is being reviewed/rated.
     * @var Thing|null
     */
    public null|Thing $itemReviewed ;

    /**
     * This Review or Rating is relevant to this part or facet of the itemReviewed.
     * @var string|null
     */
    public null|string $reviewAspect ;

    /**
     * The actual body of the review.
     * @var string|null
     */
    public null|string $reviewBody ;
}


