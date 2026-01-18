<?php

namespace org\schema;

use JsonSerializable;

use oihana\core\options\PrepareOption;
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
     * @inheritdoc
     */
    public function getJsonOptions(): array
    {
        return [ PrepareOption::REDUCE => true ] ;
    }

    /**
     * JSON-LD @context declaration for Schema.org.
     */
    public const string CONTEXT = 'http://purl.org/dc' ;
}


