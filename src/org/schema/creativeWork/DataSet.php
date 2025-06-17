<?php

namespace org\schema\creativeWork ;

use org\schema\creativeWork\enumerations\MeasurementMethodEnum;
use org\schema\creativeWork\medias\DataDownload;
use org\schema\DefinedTerm;
use org\schema\Property;
use org\schema\PropertyValue;
use org\schema\StatisticalVariable;

/**
 * All or part of a Dataset in downloadable form.
 * @see https://schema.org/DataSet
 */
class DataSet extends MediaObject
{
    /**
     * A downloadable form of this dataset, at a specific location, in a specific format. This property can be repeated if different variations are available. There is no expectation that different downloadable distributions must contain exactly equivalent information (see also DCAT on this point). Different distributions might include or exclude different subsets of the entire dataset, for example.
     * @var DataDownload|null
     */
    public null|DataDownload $distribution ;

    /**
     * A data catalog which contains this dataset.
     * Inverse property: dataset
     * @var null|array|DataCatalog
     */
    public null|array|DataCatalog $includedInDataCatalog ;

    /**
     * The International Standard Serial Number (ISSN) that identifies this serial publication. You can repeat this property to identify different formats of, or the linking ISSN (ISSN-L) for, this serial publication.
     * @var string|null
     */
    public ?string $issn ;

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

    /**
     * The variableMeasured property can indicate (repeated as necessary) the variables that are measured in some dataset, either described as text or as pairs of identifier and description using PropertyValue, or more explicitly as a StatisticalVariable.
     * @var string|Property|PropertyValue|StatisticalVariable|null
     */
    public null|string|Property|PropertyValue|StatisticalVariable $variableMeasured ;
}


