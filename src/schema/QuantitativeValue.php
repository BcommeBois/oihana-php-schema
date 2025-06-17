<?php

namespace org\schema;

use org\schema\traits\ValueTrait;

class QuantitativeValue extends StructuredValue
{
    /**
     * A property-value pair representing an additional characteristic of the entity,
     * e.g. a product feature or another characteristic for which there is no matching property in schema.org.
     * @var array|null|object
     */
    public array|null|object $additionalProperty ;

    use ValueTrait ;
}