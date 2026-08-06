<?php

namespace xyz\oihana\schema\constants\traits\shipping;

/**
 * The property name constants of the {@see \xyz\oihana\schema\shipping\DeliveryRouteAssignment} class.
 *
 * Four of the seven keys duplicate values already exposed elsewhere in the
 * library — `byDay` by
 * {@see \xyz\oihana\schema\constants\traits\thesaurus\DeliveryRouteTermTrait},
 * `position` by {@see \xyz\oihana\schema\constants\traits\places\Site},
 * `startTime` and `endTime` by {@see \org\schema\constants\traits\Action} among
 * others — which is intentional : a constant repeated with an identical value
 * composes without conflict, and each entity keeps a self-contained vocabulary.
 *
 * `route`, `weekFrom` and `weekThrough` are specific to this class. The two week
 * bounds are deliberately not named `validFrom` / `validThrough` : those carry
 * dates across the library, whereas these carry ISO week numbers, and reusing
 * the name would make the type a lie.
 *
 * @package xyz\oihana\schema\constants\traits\shipping
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.4.0
 */
trait DeliveryRouteAssignment
{
    const string BY_DAY       = 'byDay' ;
    const string END_TIME     = 'endTime' ;
    const string POSITION     = 'position' ;
    const string ROUTE        = 'route' ;
    const string START_TIME   = 'startTime' ;
    const string WEEK_FROM    = 'weekFrom' ;
    const string WEEK_THROUGH = 'weekThrough' ;
}
