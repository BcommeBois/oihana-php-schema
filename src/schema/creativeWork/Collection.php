<?php

namespace org\schema\creativeWork ;

use org\schema\CreativeWork;
use org\schema\traits\CollectionTrait;

/**
 * A collection of items, e.g. creative works or products.
 * @see https://schema.org/Collection
 */
class Collection extends CreativeWork
{
   use CollectionTrait ;
}


