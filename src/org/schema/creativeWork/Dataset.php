<?php

namespace org\schema\creativeWork ;

use org\schema\CreativeWork;
use org\schema\creativeWork\medias\DataDownload;
use org\schema\Property;
use org\schema\PropertyValue;
use org\schema\StatisticalVariable;
use org\schema\traits\MeasurementTechniqueTrait;

/**
 * A body of structured information describing some topic(s) of interest.
 *
 * @see https://schema.org/Dataset
 */
class Dataset extends CreativeWork
{
    use MeasurementTechniqueTrait ;

    /**
     * A downloadable form of this dataset, at a specific location, in a specific format. This property can be repeated if different variations are available. There is no expectation that different downloadable distributions must contain exactly equivalent information (see also DCAT on this point). Different distributions might include or exclude different subsets of the entire dataset, for example.
     * @var DataDownload|null|array
     */
    public null|array|DataDownload $distribution ;

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
     * The variableMeasured property can indicate (repeated as necessary) the variables that are measured in some dataset,
     * either described as text or as pairs of identifier and description using PropertyValue, or more explicitly as a StatisticalVariable.
     * @var string|Property|PropertyValue|StatisticalVariable|null|array
     */
    public null|string|Property|PropertyValue|StatisticalVariable|array $variableMeasured ;
}
