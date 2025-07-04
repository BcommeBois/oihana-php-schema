<?php

namespace org\schema\creativeWork ;

use org\schema\CreativeWork;
use org\schema\Organization;
use org\schema\Person;

/**
 * A quotation. Often but not necessarily from some written work, attributable to a real world author and - if associated with a fictional character - to any fictional Person. Use isBasedOn to link to source/origin. The recordedIn property can be used to reference a Quotation from an Event.
 * @see https://schema.org/
 */
class Quotation extends CreativeWork
{
    /**
     * The (e.g. fictional) character, Person or Organization to whom the quotation is attributed within the containing CreativeWork.
     */
    public null|Organization|Person $spokenByCharacter ;
}


