<?php

namespace org\schema;

use org\schema\creativeWork\enumerations\MeasurementMethodEnum;

/**
 * StatisticalVariable represents any type of statistical metric that can be measured at a place and time.
 * @see https://schema.org/StatisticalVariable
 */
class StatisticalVariable extends ConstraintNode
{
    /**
     * The measuredProperty of an Observation, typically via its StatisticalVariable. There are various kinds of applicable Property: a schema.org property, a property from other RDF-compatible systems, e.g. W3C RDF Data Cube, Data Commons, Wikidata, or schema.org extensions such as GS1's.
     * @var string|Property|null
     */
    public null|string|Property $measuredProperty ;

    /**
     * Identifies the denominator variable when an observation represents a ratio or percentage.
     * @var StatisticalVariable|null
     */
    public ?StatisticalVariable $measurementDenominator;

    /**
     * A subproperty of measurementTechnique that can be used for specifying specific methods, in particular via MeasurementMethodEnum.
     * @var string|DefinedTerm|MeasurementMethodEnum|null
     */
    public null|string|DefinedTerm|MeasurementMethodEnum $measurementMethod ;

    /**
     * Provides additional qualification to an observation. For example, a GDP observation measures the Nominal value.
     * @var string|DefinedTerm|Enumeration|null
     */
    public null|string|DefinedTerm|Enumeration $measurementQualifier ;

    /**
     * A technique, method or technology used in an Observation, StatisticalVariable or Dataset (or DataDownload, DataCatalog), corresponding to the method used for measuring the corresponding variable(s) (for datasets, described using variableMeasured; for Observation, a StatisticalVariable). Often but not necessarily each variableMeasured will have an explicit representation as (or mapping to) an property such as those defined in Schema.org, or other RDF vocabularies and "knowledge graphs".
     * In that case the subproperty of variableMeasured called measuredProperty is applicable.
     * @var string|DefinedTerm|MeasurementMethodEnum|null
     */
    public null|string|DefinedTerm|MeasurementMethodEnum $measurementTechnique ;

    /**
     * Indicates the populationType common to all members of a StatisticalPopulation or all cases within the scope of a StatisticalVariable.
     * @var Type|null
     */
    public null|Type $populationType ;

    /**
     * Indicates the kind of statistic represented by a StatisticalVariable, e.g. mean, count etc.
     * The value of statType is a property, either from within Schema.org (e.g. median, marginOfError, maxValue, minValue) or from other compatible (e.g. RDF) systems such as DataCommons.org or Wikidata.org.
     * @var string|Property|null
     */
    public null|string|Property $statType ;
}