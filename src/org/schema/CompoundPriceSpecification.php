<?php

namespace org\schema;

/**
 * TA compound price specification is one that bundles multiple prices that all apply in combination
 * for different dimensions of consumption. Use the name property of the attached unit price specification
 * for indicating the dimension of a price component (e.g. "electricity" or "final cleaning").
 * @see https://schema.org/UnitPriceSpecification
 */
class CompoundPriceSpecification extends PriceSpecification
{
    /**
     * This property links to all UnitPriceSpecification nodes that apply in parallel for the CompoundPriceSpecification node.
     * @var null|array|UnitPriceSpecification
     * @see
     */
    public null|array|UnitPriceSpecification $priceComponent ;

    /**
     * Defines the type of a price specified for an offered product,
     * for example a list price, a (temporary) sale price or a manufacturer suggested retail price.
     * If multiple prices are specified for an offer the priceType property can be used to identify the type of each such specified price.
     * The value of priceType can be specified as a value from enumeration PriceTypeEnumeration or as a free form text string for price types that are not already predefined in PriceTypeEnumeration.
     * Example:
     * - ActivationFee
     * - CleaningFee
     * - DistanceFee
     * - Downpayment
     * - Installment
     * - Subscription
     * @var string|Enumeration|DefinedTerm|null
     * @see https://schema.org/PriceTypeEnumeration
     */
    public null|string|Enumeration|DefinedTerm $priceType ;
}