<?php

namespace org\schema\creativeWork\web ;

use org\schema\creativeWork\WebPage;
use org\schema\Duration;
use org\schema\QuantitativeValue;

/**
 * A RealEstateListing is a listing that describes one or more real-estate Offers (whose businessFunction is typically to lease out, or to sell).
 * The RealEstateListing type itself represents the overall listing, as manifested in some WebPage.
 * @see https://schema.org/RealEstateListing
 */
class RealEstateListing extends WebPage
{
    /**
     * Publication date of an online listing.
     * @var string|null|int
     */
    public null|string|int $datePosted ;

    /**
     * Length of the lease for some Accommodation, either particular to some Offer or in some cases intrinsic to the property.
     * @var int|Duration|QuantitativeValue|null
     */
    public null|int|Duration|QuantitativeValue $leaseLength ;
}


