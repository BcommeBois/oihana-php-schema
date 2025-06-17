<?php

namespace org\schema;

/**
 * A MerchantReturnPolicy provides information about product return policies associated with an Organization, Product, or Offer.
 * @see https://schema.org/MerchantReturnPolicy
 */
class MerchantReturnPolicy extends Intangible
{
    /**
     * A property-value pair representing an additional characteristic of the entity, e.g. a product feature or another characteristic for which there is no matching property in schema.org.
     * @var null|array|PropertyValue
     */
    public null|array|PropertyValue $additionalProperty ;

    // TODO complete
}