<?php

namespace org\schema ;

/**
 * A rating is an evaluation on a numeric scale, such as 1 to 5 stars.
 * @see https://schema.org/Rating
 */
class Rating extends Intangible
{
    /**
     * The author of this content or rating. Please note that author is special in that HTML 5 provides a special mechanism for indicating authorship via the rel tag.
     * That is equivalent to this and may be used interchangeably.
     * @var null|Organization|Person
     */
    public null|Organization|Person $author ;

    /**
     * The highest value allowed in this rating system.
     * @var null|string|int|float
     */
    public null|string|int|float $bestRating ;

    /**
     * A short explanation (e.g. one to two sentences) providing background context and other information that led to the conclusion expressed in the rating.
     * This is particularly applicable to ratings associated with "fact check" markup using ClaimReview.
     * @var string|null
     */
    public ?string $ratingExplanation ;

    /**
     * The rating for the content.
     * @var string|int|float|null
     */
    public null|string|int|float $ratingValue ;

    /**
     * This Review or Rating is relevant to this part or facet of the itemReviewed.
     * @var string|null
     */
    public ?string $ratingAspect ;

    /**
     * The lowest value allowed in this rating system.
     * @var string|int|float|null
     */
    public null|string|int|float $worstRating ;
}


