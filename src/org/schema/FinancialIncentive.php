<?php

namespace org\schema;

use org\schema\enumerations\IncentiveType;
use org\schema\enumerations\status\IncentiveStatus;
use org\schema\services\LoanOrCredit;

/**
 * An event happening at a certain time and location, such as a concert, lecture, or festival. Repeated events may be structured as separate Event objects.
 * @see https://schema.org/FinancialIncentive
 */
class FinancialIncentive extends Intangible
{
    /**
     * The geographic area where a service or offered item is provided.
     */
    public null|string|Place|GeoShape $areaServed ;

    /**
     * The supplier of the incentivized item/service for which the incentive is valid for such as a utility company, merchant, or contractor.
     * @var Organization|null
     */
    public null|Organization $eligibleWithSupplier ;

    /**
     * Describes the amount that can be redeemed from this incentive.
     * @var QuantitativeValue|UnitPriceSpecification|LoanOrCredit|null
     */
    public null|QuantitativeValue|UnitPriceSpecification|LoanOrCredit $incentiveAmount ;

    /**
     * The status of the incentive (active, on hold, retired, etc.).
     * Example:
     * - IncentiveStatusActive
     * - IncentiveStatusInDevelopment
     * - IncentiveStatusOnHold
     * - IncentiveStatusRetired
     * @var null|string|IncentiveStatus|DefinedTerm
     */
    public null|string|IncentiveStatus|DefinedTerm $incentiveStatus ;

    /**
     * The type of incentive offered (tax credit/rebate, tax deduction, tax waiver, subsidies, etc.).
     * Example:
     * - IncentiveTypeLoan
     * - IncentiveTypeRebateOrSubsidy
     * - IncentiveTypeTaxCredit
     * - IncentiveTypeTaxDeduction
     * - IncentiveTypeTaxWaiver
     * @var null|string|IncentiveType|DefinedTerm
     */
    public null|string|IncentiveType|DefinedTerm $incentiveType ;

    /**
     * The type or specific product(s) and/or service(s) being incentivized.
     * DefinedTermSets are used for product and service categories such as the United Nations Standard Products and Services Code:
     * ```
     * {
     *   "@type": "DefinedTerm",
     *   "inDefinedTermSet": "https://www.unspsc.org/",
     *   "termCode": "261315XX",
     *   "name": "Photovoltaic module"
     * }
     * ```
     * @var array|DefinedTerm|Product|null
     */
    public null|array|DefinedTerm|Product $incentivizedItem ;

    /**
     * Income limit for which the incentive is applicable for (Optional).
     * @var string|null|MonetaryAmount
     */
    public null|string|MonetaryAmount $incomeLimit ;

    /**
     * The service provider, service operator, or service performer; the goods producer.
     * Another party (a seller) may offer those services or goods on behalf of the provider.
     * A provider may also serve as the seller.
     * @var null|Organization|Person
     */
    public null|Organization|Person $provider ;

    /**
     * The maximum price the item can have and still qualify for this offer (Optional).
     * @var null
     */
    public null $purchasePriceLimit ;

    /**
     * The type of purchase the consumer must make in order to qualify for this incentive (Optional).
     * Example :
     * - PurchaseTypeLease
     * - PurchaseTypeNewPurchase
     * - PurchaseTypeTradeIn
     * - PurchaseTypeUsedPurchase
     * @var string|DefinedTerm|Enumeration|null
     */
    public null|string|DefinedTerm|Enumeration $purchaseType ;

    /**
     * The types of expenses that are covered by the incentive (Optional).
     * For example some incentives are only for the goods (tangible items) but the services (labor) are excluded.
     * Example :
     * - IncentiveQualifiedExpenseTypeGoodsOnly
     * - IncentiveQualifiedExpenseTypeGoodsOrServices
     * - IncentiveQualifiedExpenseTypeServicesOnly
     * - IncentiveQualifiedExpenseTypeUtilityBill
     * @var string|DefinedTerm|Enumeration|null
     */
    public null|string|DefinedTerm|Enumeration $qualifiedExpense ;

    /**
     * The date when the item becomes valid.
     */
    public null|string|int $validFrom ;

    /**
     * The end of the validity of offer, price specification, or opening hours data.
     */
    public null|string|int $validThrough ;
}


