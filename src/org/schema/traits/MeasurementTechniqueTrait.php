<?php

namespace org\schema\traits;

use org\schema\creativeWork\enumerations\MeasurementMethodEnum;
use org\schema\DefinedTerm;

/**
 * The pair that says how a figure was measured : the technique, and the method
 * that refines it.
 *
 * Schema.org publishes them together on everything that carries a measure — a
 * Dataset, its catalogue, its download, an Observation, a StatisticalVariable, a
 * PropertyValue — and declaring them once is what keeps the six readings of the
 * same two terms identical.
 */
trait MeasurementTechniqueTrait
{
    /**
     * A subproperty of measurementTechnique that can be used for specifying specific methods, in particular via MeasurementMethodEnum.
     * @var string|array|DefinedTerm|MeasurementMethodEnum|null
     */
    public null|string|array|DefinedTerm|MeasurementMethodEnum $measurementMethod ;

    /**
     * A technique, method or technology used in an Observation, StatisticalVariable or Dataset (or DataDownload, DataCatalog), corresponding to the method used for measuring the corresponding variable(s) (for datasets, described using variableMeasured; for Observation, a StatisticalVariable). Often but not necessarily each variableMeasured will have an explicit representation as (or mapping to) an property such as those defined in Schema.org, or other RDF vocabularies and "knowledge graphs".
     * In that case the subproperty of variableMeasured called measuredProperty is applicable.
     * @var string|array|DefinedTerm|MeasurementMethodEnum|null
     */
    public null|string|array|DefinedTerm|MeasurementMethodEnum $measurementTechnique ;
}
