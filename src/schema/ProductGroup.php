<?php

namespace org\schema;

class ProductGroup extends Product
{
    /**
     * Indicates a Product that is a member of this ProductGroup (or ProductModel).
     * Inverse property: isVariantOf
     * @var null|array|Product
     */
    public null|array|Product $hasVariant ;

    /**
     * Indicates a textual identifier for a ProductGroup.
     * @var null|string
     */
    public ?string $productGroupID ;

    /**
     * Indicates the property or properties by which the variants in a ProductGroup vary, e.g. their size, color etc. Schema.org properties can be referenced by their short name e.g. "color"; terms defined elsewhere can be referenced with their URIs.
     * @var null|string|DefinedTerm
     */
    public null|string|DefinedTerm $variesBy ;
}