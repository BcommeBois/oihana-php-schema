<?php

namespace org\schema\creativeWork ;

use org\schema\CreativeWork;
use org\schema\traits\MeasurementTechniqueTrait;

/**
 * A collection of datasets.
 *
 * @see https://schema.org/DataCatalog
 */
class DataCatalog extends CreativeWork
{
    use MeasurementTechniqueTrait ;

    /**
     * A dataset contained in this catalog.
     * @var array|null|Dataset
     */
    public null|array|Dataset $dataset ;
}
