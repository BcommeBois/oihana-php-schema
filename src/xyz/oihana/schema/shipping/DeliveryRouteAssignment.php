<?php

namespace xyz\oihana\schema\shipping;

use org\schema\DefinedTerm;
use org\schema\enumerations\DayOfWeek;
use org\schema\Schedule;
use org\schema\StructuredValue;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\constants\traits\shipping\DeliveryRouteAssignment as DeliveryRouteAssignmentTrait;
use xyz\oihana\schema\places\Site;
use xyz\oihana\schema\thesaurus\DeliveryRouteTerm;

/**
 * The assignment of a delivery route to one address : which route serves it, on
 * which of its days, in which order, and within which hours.
 *
 * A {@see DeliveryRouteTerm} describes a circuit in the abstract — the days it
 * runs, the warehouse it leaves from. This class is the other half : it says
 * that *this* address is one of the stops, and adds what only the pairing knows.
 * A site therefore carries a list of assignments ({@see Site::$deliveryRoute}),
 * one per route serving it, since a same address is commonly visited by more
 * than one circuit.
 *
 * ### Only the reference is stored
 *
 * `route` holds the route reference as read — its bare code — or the resolved
 * {@see DeliveryRouteTerm} once the reference data has been joined. That is the
 * shape {@see Site::$deliveryMethod} already follows, and it is what keeps a
 * renamed route from having to be rewritten everywhere it is assigned : the
 * label lives in the thesaurus, never in a copy.
 *
 * A business document is the deliberate exception — it freezes the identity of
 * the route it names, so a quote keeps saying what was chosen even after the
 * thesaurus has moved on.
 *
 * ### The days, and the narrower ones
 *
 * `byDay` uses the {@see DayOfWeek} vocabulary of {@see Schedule::$byDay}, like
 * the route itself, but says something narrower : the days the route serves
 * *this* address. It is always a subset of {@see DeliveryRouteTerm::$byDay} —
 * a circuit running Monday, Wednesday and Friday may only stop here on Friday.
 *
 * ### Times and weeks
 *
 * `startTime` and `endTime` bound the hours the address can be served, as
 * `HH:MM` strings. Both are optional and independent : an address open from
 * eight in the morning with no closing constraint states a start and no end.
 *
 * `weekFrom` and `weekThrough` bound the assignment in the year, as ISO week
 * numbers (1 to 53), for a stop that only exists part of the year — a seasonal
 * site, a construction site with an end date. Both null means all year round.
 *
 * ### Example
 *
 * ```php
 * use org\schema\enumerations\DayOfWeek;
 * use xyz\oihana\schema\shipping\DeliveryRouteAssignment;
 *
 * $assignment = new DeliveryRouteAssignment
 * ([
 *     DeliveryRouteAssignment::ROUTE      => '01D' ,
 *     DeliveryRouteAssignment::BY_DAY     => [ DayOfWeek::FRIDAY ] ,
 *     DeliveryRouteAssignment::POSITION   => 12 ,
 *     DeliveryRouteAssignment::START_TIME => '08:00' ,
 * ]);
 * ```
 *
 * @see DeliveryRouteTerm
 * @see Site::$deliveryRoute
 * @see DayOfWeek
 *
 * @package  xyz\oihana\schema\shipping
 * @category Shipping
 * @author   Marc Alcaraz (ekameleon)
 * @since    1.4.0
 */
class DeliveryRouteAssignment extends StructuredValue
{
    use DeliveryRouteAssignmentTrait ;

    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * The days of the week the route serves this address, as {@see DayOfWeek}
     * constants.
     *
     * Always a subset of the days the route runs — see
     * {@see DeliveryRouteTerm::$byDay}.
     *
     * @var null|array|string|DayOfWeek
     */
    public null|array|string|DayOfWeek $byDay ;

    /**
     * The latest time of day the address can be served, as an `HH:MM` string.
     *
     * `null` means no closing constraint.
     *
     * @var null|string|int
     */
    public null|string|int $endTime ;

    /**
     * The rank of this address in the order the route visits its stops.
     *
     * `null` means the order is not specified.
     *
     * @var null|int|string
     */
    public null|int|string $position ;

    /**
     * The route serving this address.
     *
     * Holds the raw route reference as read from the source — its bare code — or
     * the resolved {@see DeliveryRouteTerm} once the reference data has been
     * joined. Same shape as {@see Site::$deliveryMethod}.
     *
     * @var null|array|string|DefinedTerm
     */
    public null|array|string|DefinedTerm $route ;

    /**
     * The earliest time of day the address can be served, as an `HH:MM` string.
     *
     * `null` means no opening constraint.
     *
     * @var null|string|int
     */
    public null|string|int $startTime ;

    /**
     * The ISO week number (1 to 53) the assignment starts on.
     *
     * `null` means it holds from the start of the year.
     *
     * @var int|null
     */
    public ?int $weekFrom ;

    /**
     * The ISO week number (1 to 53) the assignment ends on, inclusive.
     *
     * `null` means it holds to the end of the year.
     *
     * @var int|null
     */
    public ?int $weekThrough ;
}
