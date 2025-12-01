<?php

namespace org\schema\organizations;

use org\schema\Organization;

/**
 * A particular physical business or branch of an organization. Examples of LocalBusiness include a restaurant,
 * a particular branch of a restaurant chain, a branch of a bank, a medical practice, a club, a bowling alley, etc.
 *
 * @see https://schema.org/LocalBusiness
 */
class LocalBusiness extends Organization
{
    /**
     * The currency accepted.
     *
     * Use standard formats: ISO 4217 currency format,
     * e.g. "USD"; Ticker symbol for cryptocurrencies,
     * e.g. "BTC";
     * well known names for Local Exchange Trading Systems (LETS) and other currency types, e.g. "Ithaca HOUR".
     *
     * @var string|null
     */
    public null|string $currencyAccepted ;

    /**
     * The general opening hours for a business. Opening hours can be specified as a weekly time range, starting with days, then times per day.
     *
     * Multiple days can be listed with commas ',' separating each day. Day or time ranges are specified using a hyphen '-'.
     * @var string|null
     */
    public null|string $openingHours ;

    /**
     * Cash, Credit Card, Cryptocurrency, Local Exchange Tradings System, etc.
     * @var null|string|array
     */
    public null|string|array $paymentAccepted ;

    /**
     * The price range of the business, for example $$$.
     * @var null|string|array
     */
    public null|string|array $priceRange ;

}