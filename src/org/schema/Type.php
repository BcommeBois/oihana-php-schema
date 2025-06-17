<?php

namespace org\schema;

/**
 * A class, also often called a 'Type'; equivalent to rdfs:Class.
 * Note : In PHP, use the 'Type' name and not the reserved 'Class' name.
 * @see https://schema.org/Class
 */
class Type extends Intangible
{
    public null|Type|Enumeration|Property $supersededBy ;
}