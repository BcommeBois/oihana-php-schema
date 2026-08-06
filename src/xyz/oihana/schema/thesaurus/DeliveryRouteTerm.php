<?php

namespace xyz\oihana\schema\thesaurus;

use org\schema\enumerations\DayOfWeek;
use org\schema\Schedule;

use xyz\oihana\schema\constants\traits\thesaurus\DeliveryRouteTermTrait;
use xyz\oihana\schema\places\Warehouse;
use xyz\oihana\schema\shipping\DeliveryRouteAssignment;

/**
 * A delivery route : the recurring circuit an own-fleet vehicle runs — leaving a
 * given warehouse, on given days of the week, to serve the addresses assigned to
 * it.
 *
 * Where {@see DeliveryMethodTerm} answers *how* an order travels (counter
 * pick-up, carrier, own fleet) and what that costs, this term answers *on which
 * passage* it travels. The two are orthogonal : a dozen routes may all be run
 * under the single "own fleet" method, and a route carries no charge of its own.
 *
 * A back office maintains this list alongside its other reference data, which is
 * why the class is a {@see ThesaurusTerm} first — referenced by its `id` from
 * sites and business documents, and enriched here with the two values that
 * describe the circuit itself.
 *
 * ### The days a route runs
 *
 * `byDay` holds the days the vehicle is on the road, as {@see DayOfWeek}
 * constants — the vocabulary {@see Schedule::$byDay} uses, so a consumer that
 * already reads schedules reads a route with the same code.
 *
 * An empty list is meaningful and must be kept : a route defined but not yet
 * scheduled runs no day at all, which is not the same thing as `null` (nothing
 * was said about its days).
 *
 * Beware of the distinction with {@see DeliveryRouteAssignment::$byDay}, which
 * is narrower : this property says when the route *runs*, the assignment says
 * when it *serves one given address*. The second is always a subset of the
 * first.
 *
 * ### Where the route departs from
 *
 * `assignedPOS` names the warehouse the vehicle leaves from — the same word, and
 * the same shape, {@see \xyz\oihana\schema\organizations\Customer::$assignedPOS}
 * uses for the point of sale a customer is attached to. It holds the raw
 * warehouse reference as read, or the resolved {@see Warehouse} once the
 * reference data has been joined.
 *
 * `null` means no warehouse is assigned : back offices commonly encode that
 * absence as a zero, which a consumer normalizes away on ingestion rather than
 * letting a route claim to depart from a warehouse that does not exist.
 *
 * ### Example
 *
 * ```php
 * use org\schema\enumerations\DayOfWeek;
 * use xyz\oihana\schema\thesaurus\DeliveryRouteTerm;
 *
 * $route = new DeliveryRouteTerm
 * ([
 *     'id'                             => '01D' ,
 *     'name'                           => 'West coast, midweek' ,
 *     DeliveryRouteTerm::ASSIGNED_POS => '1' ,
 *     DeliveryRouteTerm::BY_DAY       => [ DayOfWeek::WEDNESDAY , DayOfWeek::FRIDAY ] ,
 * ]);
 * ```
 *
 * @see ThesaurusTerm
 * @see DeliveryMethodTerm
 * @see DeliveryRouteAssignment
 * @see DeliveryRouteTermTrait
 * @see DayOfWeek
 *
 * @package  xyz\oihana\schema\thesaurus
 * @category Thesaurus
 * @author   Marc Alcaraz (ekameleon)
 * @since    1.4.0
 */
class DeliveryRouteTerm extends ThesaurusTerm
{
    use DeliveryRouteTermTrait ;

    /**
     * The warehouse the route departs from.
     *
     * Holds the raw warehouse reference as read from the source, or the
     * resolved {@see Warehouse} once the reference data has been joined.
     *
     * `null` means the route is attached to no warehouse.
     *
     * @var null|int|string|array|Warehouse
     */
    public null|int|string|array|Warehouse $assignedPOS ;

    /**
     * The days of the week the route runs, as {@see DayOfWeek} constants.
     *
     * An empty list means the route runs no day — a defined but unscheduled
     * route — whereas `null` means nothing was said about its days.
     *
     * @var null|array|string|DayOfWeek
     */
    public null|array|string|DayOfWeek $byDay ;
}
