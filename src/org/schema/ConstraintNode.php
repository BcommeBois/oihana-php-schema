<?php

namespace org\schema;

/**
 * The ConstraintNode type is provided to support usecases in which a node in a structured data graph is described with properties which appear to describe a single entity, but are being used in a situation where they serve a more abstract purpose.
 * @see https://schema.org/ConstraintNode
 */
class ConstraintNode extends Intangible
{
    /**
     * Indicates a property used as a constraint.
     * For example, in the definition of a StatisticalVariable.
     * The value is a property, either from within Schema.org or from other compatible (e.g. RDF) systems such as DataCommons.org or Wikidata.org.
     * @var string|Property|null
     */
    public null|string|Property $constraintProperty ;

    /**
     * Indicates the number of constraints property values defined for a particular ConstraintNode such as StatisticalVariable. This helps applications understand if they have access to a sufficiently complete description of a StatisticalVariable or other construct that is defined using properties on template-style nodes.
     * @var int|null
     */
    public ?int $numConstraints;
}