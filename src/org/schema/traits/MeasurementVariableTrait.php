<?php

namespace org\schema\traits;

use org\schema\DefinedTerm;
use org\schema\Enumeration;
use org\schema\Property;
use org\schema\StatisticalVariable;

/**
 * Measurement variable metric properties.
 *
 * The pair every measured thing carries — the technique and its method — comes
 * from {@see MeasurementTechniqueTrait} ; what follows is what a *variable* adds
 * to it : the property it measures, the denominator it is a ratio of, and the
 * qualification the figure is read under.
 */
trait MeasurementVariableTrait
{
    use MeasurementTechniqueTrait ;

    /**
     * The measuredProperty of an Observation, typically via its StatisticalVariable. There are various kinds of applicable Property: a schema.org property, a property from other RDF-compatible systems, e.g. W3C RDF Data Cube, Data Commons, Wikidata, or schema.org extensions such as GS1's.
     * @var string|Property|array|null
     */
    public null|string|array|Property $measuredProperty ;

    /**
     * Identifies the denominator variable when an observation represents a ratio or percentage.
     * @var null|array|StatisticalVariable
     */
    public null|array|StatisticalVariable $measurementDenominator;

    /**
     * Provides additional qualification to an observation. For example, a GDP observation measures the Nominal value.
     * @var string|array|DefinedTerm|Enumeration|null
     */
    public null|string|array|DefinedTerm|Enumeration $measurementQualifier ;
}
