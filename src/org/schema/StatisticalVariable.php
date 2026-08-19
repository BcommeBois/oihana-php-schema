<?php

namespace org\schema;

use org\schema\traits\MeasurementVariableTrait;

/**
 * StatisticalVariable represents any type of statistical metric that can be measured at a place and time.
 *
 * The usage pattern for StatisticalVariable is typically expressed using Observation with an explicit populationType,
 * which is a type, typically drawn from Schema.org. Each StatisticalVariable is marked as a ConstraintNode,
 * meaning that some properties (those listed using constraintProperty) serve in this setting solely
 * to define the statistical variable rather than literally describe a specific person, place or thing.
 *
 * For example, a StatisticalVariable MedianHeightPerson_Female representing the median height of women,
 * could be written as follows: the population type is Person;
 * the measuredProperty height; the statType median; the gender Female.
 * It is important to note that there are many kinds of scientific quantitative observation which are not fully,
 * perfectly or unambiguously described following this pattern, or with solely Schema.org terminology.
 *
 * The approach taken here is designed to allow partial, incremental or minimal description of StatisticalVariables,
 * and the use of detailed sets of entity and property IDs from external repositories.
 *
 * The measurementMethod, unitCode and unitText properties can also be used to clarify the specific nature and notation of an observed measurement.
 *
 * @see https://schema.org/StatisticalVariable
 */
class StatisticalVariable extends ConstraintNode
{
    use MeasurementVariableTrait ;

    /**
     * Indicates the populationType common to all members of a StatisticalPopulation or all cases within the scope of a StatisticalVariable.
     * @var Type|array|null
     */
    public null|array|Type $populationType ;

    /**
     * Indicates the kind of statistic represented by a StatisticalVariable, e.g. mean, count etc.
     * The value of statType is a property, either from within Schema.org (e.g. median, marginOfError, maxValue, minValue) or from other compatible (e.g. RDF) systems such as DataCommons.org or Wikidata.org.
     * @var string|array|Property|null
     */
    public null|array|string|Property $statType ;
}