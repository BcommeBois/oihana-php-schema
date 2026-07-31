<?php

namespace xyz\oihana\schema\thesaurus;

use org\schema\enumerations\DeliveryMethod;

use xyz\oihana\schema\constants\traits\thesaurus\DeliveryMethodTermTrait;
use xyz\oihana\schema\enumerations\ShippingChargeTiming;
use xyz\oihana\schema\organizations\Company;
use xyz\oihana\schema\products\TaxRate;

/**
 * A delivery method : the shipping arrangement an order is fulfilled under —
 * counter pick-up, carrier delivery, free carriage above a threshold, flat-rate
 * carriage, and so on — together with what that arrangement costs.
 *
 * Where the Schema.org {@see DeliveryMethod} enumeration names a handful of
 * universal modes (`DeliveryModePickUp`, `DeliveryModeFreight`…), a back-office
 * keeps its own, longer list, each entry priced by its own commercial terms.
 * This class is that list : a {@see ThesaurusTerm} first — referenced by its
 * `id` from organizations, sites and business documents — enriched with the
 * four values needed to charge carriage.
 *
 * The inherited `identifier` is available to carry the reference of the
 * catalogue item carriage is invoiced through, when the back-office models the
 * charge as a line rather than as a document-level total.
 *
 * ### The carriage rule
 *
 * Carriage is due when the term names a rate **and** the order does not reach
 * the free-carriage threshold :
 *
 * ```php
 * $due = $term->shippingRate !== null
 *     && ( $term->freeShippingThreshold === null || $total < $term->freeShippingThreshold ) ;
 * ```
 *
 * A `null` threshold therefore reads as *never free* — carriage always applies.
 * That is the same convention {@see Company::$freeShippingThreshold} follows for
 * the per-organization override, so the two can be composed with a plain
 * coalesce, the organization winning over the method.
 *
 * ### When the charge is fixed
 *
 * The rule above says *whether* carriage is due ; {@see ShippingChargeTiming}
 * says *when* the figure it resolves to is locked in — at order time, or
 * recomputed at delivery time. `null` leaves the timing unspecified, which a
 * consumer is free to read as whichever of the two its own process defaults to.
 *
 * ### Scalars rather than value objects
 *
 * `shippingRate` and `freeShippingThreshold` are plain numbers, not
 * {@see \org\schema\MonetaryAmount} instances, so a term hydrates straight from
 * a flat table row : a database driver assigns the raw column value to the
 * property before any conversion can take place, and an object type would
 * reject it. The currency is the ledger's own ; a caller building an
 * {@see \xyz\oihana\schema\business\documents\Adjustment} wraps the number at
 * that point — which is also where the amount *actually* applied is known,
 * since a reached threshold produces no adjustment at all.
 *
 * `vat` follows the {@see Company::$vat} shape for the same reason : it holds
 * the raw tax code as read, then the resolved {@see TaxRate} once the reference
 * data has been joined.
 *
 * ### Example
 *
 * ```php
 * use xyz\oihana\schema\thesaurus\DeliveryMethodTerm;
 *
 * $term = new DeliveryMethodTerm
 * ([
 *     'id'                                        => 'F11' ,
 *     'name'                                      => 'Free above 1000, otherwise 39' ,
 *     'identifier'                                => '59483' ,
 *     DeliveryMethodTerm::SHIPPING_RATE           => 39 ,
 *     DeliveryMethodTerm::FREE_SHIPPING_THRESHOLD => 1000 ,
 * ]);
 * ```
 *
 * @see ThesaurusTerm
 * @see DeliveryMethod
 * @see DeliveryMethodTermTrait
 * @see ShippingChargeTiming
 *
 * @package  xyz\oihana\schema\thesaurus
 * @category Thesaurus
 * @author   Marc Alcaraz (ekameleon)
 * @since    1.4.0
 */
class DeliveryMethodTerm extends ThesaurusTerm
{
    use DeliveryMethodTermTrait ;

    /**
     * When the carriage amount is settled. Reuses {@see ShippingChargeTiming}
     * (at order time, or recomputed at delivery time) or a plain free-text
     * label.
     *
     * `null` means the timing is unspecified.
     *
     * @var null|string|ShippingChargeTiming
     */
    public null|string|ShippingChargeTiming $chargeTiming ;

    /**
     * A minimum order value at (or above) which carriage becomes free.
     *
     * `null` means the method has no free-carriage threshold, so
     * {@see DeliveryMethodTerm::$shippingRate} always applies.
     *
     * @var null|float|int
     */
    public null|float|int $freeShippingThreshold ;

    /**
     * The flat carriage charged by this delivery method, expressed in the
     * ledger currency.
     *
     * `null` means the method carries no charge of its own — either it is free
     * by nature (counter pick-up, carriage offered), or the amount is settled
     * outside this term.
     *
     * @var null|float|int
     */
    public null|float|int $shippingRate ;

    /**
     * The tax rate applied to the carriage charge.
     *
     * Holds the raw tax code as read from the source, or the resolved
     * {@see TaxRate} once the reference data has been joined.
     *
     * @var array|TaxRate|string|int|null
     */
    public array|TaxRate|string|int|null $vat ;
}