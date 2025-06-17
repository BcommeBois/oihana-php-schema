<?php

namespace org\schema;

use DateTime;

/**
 * A structured value providing information about when a certain organization or person owned a certain product.
 * @see https://schema.org/OwnershipInfo
 */
class OwnershipInfo extends StructuredValue
{
    /**
     * The organization or person from which the product was acquired.
     * @var Organization|Person|null
     */
    public null|Organization|Person $acquiredFrom ;

    /**
     * The date and time of obtaining the product.
     */
    public null|string|int|DateTime $ownedFrom ;

    /**
     * The date and time of giving up ownership on the product.
     */
    public null|string|int|DateTime $ownedThrough ;

    /**
     * The product that this structured value is referring to.
     * @var Product|Service|null
     */
    public null|Product|Service $typeOfGood ;
}


