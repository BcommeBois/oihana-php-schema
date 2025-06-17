<?php

namespace org\schema\creativeWork\medias ;

use org\schema\creativeWork\enumerations\MeasurementMethodEnum;
use org\schema\creativeWork\MediaObject;
use org\schema\DefinedTerm;

/**
 * All or part of a Dataset in downloadable form.
 * @see https://schema.org/DataDownload
 */
class DataDownload extends MediaObject
{
    /**
     * A subproperty of measurementTechnique that can be used for specifying specific methods, in particular via MeasurementMethodEnum.
     * @var string|DefinedTerm|MeasurementMethodEnum|null
     */
    public null|string|DefinedTerm|MeasurementMethodEnum $measurementMethod ;

    /**
     * A technique, method or technology used in an Observation, StatisticalVariable or Dataset (or DataDownload, DataCatalog), corresponding to the method used for measuring the corresponding variable(s) (for datasets, described using variableMeasured; for Observation, a StatisticalVariable). Often but not necessarily each variableMeasured will have an explicit representation as (or mapping to) an property such as those defined in Schema.org, or other RDF vocabularies and "knowledge graphs".
     * In that case the subproperty of variableMeasured called measuredProperty is applicable.
     * @var string|DefinedTerm|MeasurementMethodEnum|null
     */
    public null|string|DefinedTerm|MeasurementMethodEnum $measurementTechnique ;
}


