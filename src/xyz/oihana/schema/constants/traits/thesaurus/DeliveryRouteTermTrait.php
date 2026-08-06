<?php

namespace xyz\oihana\schema\constants\traits\thesaurus;

/**
 * The property name constants of the {@see \xyz\oihana\schema\thesaurus\DeliveryRouteTerm} class.
 *
 * `assignedPOS` duplicates the value already exposed by
 * {@see \xyz\oihana\schema\constants\traits\organizations\Customer} — deliberately :
 * a constant repeated with an identical value composes without conflict, and each
 * entity keeps a self-contained vocabulary. Note the spelling, `POS` in capitals,
 * which is the one the library already uses ; any other would be a fatal error the
 * moment both traits meet in {@see \xyz\oihana\schema\constants\Oihana}.
 *
 * `byDay` is declared here and by
 * {@see \xyz\oihana\schema\constants\traits\shipping\DeliveryRouteAssignment},
 * again with the same value : the route says when it runs, the assignment says
 * when it serves one address.
 *
 * @package xyz\oihana\schema\constants\traits\thesaurus
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.4.0
 */
trait DeliveryRouteTermTrait
{
    const string ASSIGNED_POS = 'assignedPOS' ;
    const string BY_DAY       = 'byDay' ;
}
