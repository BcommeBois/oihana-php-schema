<?php

namespace org\schema;

use JsonSerializable;

use org\schema\traits\DublinCoreTrait;
use org\schema\traits\ThingTrait;

use org\schema\constants\traits\DublinCore as DublinCoreSchema;

/**
 * The DCMI Metadata Terms.
 *
 * @see https://www.dublincore.org/specifications/dublin-core/dcmi-terms/
 */
class DublinCore implements JsonSerializable
{
    use ThingTrait ,
        DublinCoreTrait ,
        DublinCoreSchema ;

    /**
     * JSON-LD @context declaration for Schema.org.
     */
    public const string CONTEXT = 'http://purl.org/dc' ;
}


