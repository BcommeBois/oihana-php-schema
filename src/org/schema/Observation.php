<?php

namespace org\schema;

use org\schema\traits\MeasurementVariableTrait;

/**
 * Instances of the class Observation are used to specify observations about an entity at a particular time.
 *
 * The principal properties of an Observation are observationAbout, measuredProperty, statType, [[value] and observationDate and measuredProperty.
 * Some but not all Observations represent a QuantitativeValue.
 *
 * Quantitative observations can be about a StatisticalVariable, which is an abstract specification about which we can make observations that are grounded at a particular location and time.
 *
 * Observations can also encode a subset of simple RDF-like statements (its observationAbout, a StatisticalVariable, defining the measuredPoperty; its observationAbout property indicating the entity the statement is about, and value )
 *
 * In the context of a quantitative knowledge graph, typical properties could include measuredProperty, observationAbout, observationDate, value, unitCode, unitText, measurementMethod.
 *
 * @see https://schema.org/Observation
 */
class Observation extends QuantitativeValue
{
    use MeasurementVariableTrait ;

    /**
     * A margin of error for an Observation.
     * @var null|array|QuantitativeValue
     */
    public null|array|QuantitativeValue $marginOfError ;

    /**
     * The observationAbout property identifies an entity, often a Place, associated with an Observation.
     * @var null|array|Place|Thing
     */
    public null|array|Place|Thing $observationAbout ;

    /**
     * The date of an Observation.
     */
    public null|string $observationDate ;

    /**
     * The length of time an Observation took place over. The format follows P[0-9]*[Y|M|D|h|m|s].
     * For example, P1Y is Period 1 Year, P3M is Period 3 Months, P3h is Period 3 hours.
     */
    public null|string $observationPeriod ;

    /**
     * The variableMeasured property can indicate (repeated as necessary) the variables that are measured in some dataset,
     * either described as text or as pairs of identifier and description using PropertyValue, or more explicitly as a StatisticalVariable.
     * @var null|string|array|Property|PropertyValue|StatisticalVariable
     */
    public null|string|array|Property|PropertyValue|StatisticalVariable $variableMeasured ;
}