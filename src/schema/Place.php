<?php

namespace org\schema;

use org\schema\traits\PlaceTrait;

/**
 * Entities that have a somewhat fixed, physical extension.
 * @see https://schema.org/Place
 */
class Place extends Thing
{
    use PlaceTrait ;
}


