<?php

namespace org\schema\creativeWork ;

use org\schema\CreativeWork;
use org\schema\Organization;
use org\schema\Person;

/**
 * A Claim in Schema.org represents a specific, factually-oriented claim that could be the itemReviewed in a ClaimReview.
 * The content of a claim can be summarized with the text property.
 * Variations on well known claims can have their common identity indicated via sameAs links, and summarized with a name.
 * Ideally, a Claim description includes enough contextual information to minimize the risk of ambiguity or inclarity.
 * In practice, many claims are better understood in the context in which they appear or the interpretations provided by claim reviews.
 */
class Claim extends CreativeWork
{
    /**
     * Indicates an occurrence of a Claim in some CreativeWork.
     * @var CreativeWork|null
     */
    public ?CreativeWork $appearance ;

    /**
     * For a Claim interpreted from MediaObject content, the interpretedAsClaim property can be used to indicate a claim contained,
     * implied or refined from the content of a MediaObject.
     * @var array|Organization|Person|null
     */
    public null|array|Organization|Person $claimInterpreter ;

    /**
     * Indicates the first known occurrence of a Claim in some CreativeWork.
     * @var CreativeWork|null
     */
    public ?CreativeWork $firstAppearance ;
}


