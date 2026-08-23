<?php

namespace xyz\oihana\schema\helpers\hydrate\appointments;

use ReflectionException;

use xyz\oihana\schema\appointments\VisitReport;

use function oihana\core\arrays\isIndexed;

use function org\schema\helpers\hydrate\hydrateDefinedTerm;
use function org\schema\helpers\hydrate\hydrateOrganizationOrPerson;

use function xyz\oihana\schema\helpers\hydrate\hydrateCustomerEmployee;

/**
 * Hydrate an array definition with the VisitReport class.
 *
 * Handles both a single report array and an array of reports.
 *
 * Each nested reference is hydrated only when the raw value is an array — when there is
 * something to hydrate. The helper's answer is then written as is, `null` included : an
 * array that resolves to nothing becomes `null`, never a leftover raw array, so the report
 * answers the same thing as the nested helper called on its own. Anything that is not an
 * array — an unresolved string reference, an already typed instance — is left untouched.
 *
 * What it resolves :
 *
 * - `attendee` — the people actually met, as {@see \xyz\oihana\schema\people\CustomerEmployee},
 *   one or several ;
 * - `followUp` — what comes next, through {@see hydrateFollowUp()} ;
 * - `mood` and `outcome` — one term each, how it felt and what it produced ;
 * - `tags` and `topics` — several terms each ;
 * - `author` — whoever wrote it, an `Organization|Person` union that only the payload's
 *   `@type` can settle.
 *
 * 🔑 **An empty `followUp` list stays an empty list.** « This report has no follow-up » is
 * an answer worth serving, and a reader walking the value deserves a list to walk —
 * {@see hydrateFollowUp()} holds that rule, and this helper writes back whatever it
 * answers.
 *
 * 🔑 **A bare reference survives inside a list**, exactly as it does on its own : a list of
 * unresolved handles comes back as it stands, and only an entry that *was* an array and
 * resolved to nothing is dropped. The keys stay gap-free — a filtered list left with holes
 * serializes as a JSON object, and a consumer walking the value gets something it cannot walk.
 *
 * @param mixed $init Single report data or array of report data.
 *
 * @return mixed
 *
 * @throws ReflectionException
 *
 * @example
 * ```php
 * $report = hydrateVisitReport
 * ([
 *     'mood'     => [ 'id' => 'SATISFIED' , 'name' => 'Satisfied' ] ,
 *     'attendee' => [ [ 'name' => 'Jane Doe' , 'jobTitle' => [ 'id' => 'BUYER' ] ] ] ,
 *     'followUp' => [ [ 'followUpType' => [ 'id' => 'CALL_BACK' ] ] ] ,
 *     'text'     => 'Everything the boxes cannot hold.' ,
 * ]) ;
 *
 * $report->attendee[ 0 ] instanceof CustomerEmployee ; // true
 * $report->followUp[ 0 ] instanceof FollowUp         ; // true
 * ```
 */
function hydrateVisitReport( mixed $init = null ) :mixed
{
    if( !is_array( $init ) )
    {
        return $init ;
    }

    if( isIndexed( $init ) )
    {
        $reports = array_map
        (
            fn( $report ) => hydrateVisitReport( $report ) ,
            $init
        );

        // A scalar entry is an unresolved reference and is kept as it stands ; only an entry that
        // WAS an array and gave nothing is dropped. `array_values` closes the gaps it leaves.
        $filtered = array_values( array_filter( $reports , fn( $report ) => $report instanceof VisitReport || is_scalar( $report ) ) ) ;

        return count( $filtered ) > 0 ? $filtered : null ;
    }

    $report = new VisitReport( $init ) ;

    // ------- attendee

    $attendee = $report->attendee ?? null ;
    if( is_array( $attendee ) )
    {
        $report->attendee = hydrateCustomerEmployee( $attendee ) ;
    }

    // ------- followUp

    $followUp = $report->followUp ?? null ;
    if( is_array( $followUp ) )
    {
        $report->followUp = hydrateFollowUp( $followUp ) ;
    }

    // ------- mood

    $mood = $report->mood ?? null ;
    if( is_array( $mood ) )
    {
        $report->mood = hydrateDefinedTerm( $mood ) ;
    }

    // ------- outcome

    $outcome = $report->outcome ?? null ;
    if( is_array( $outcome ) )
    {
        $report->outcome = hydrateDefinedTerm( $outcome ) ;
    }

    // ------- tags

    $tags = $report->tags ?? null ;
    if( is_array( $tags ) )
    {
        $report->tags = hydrateDefinedTerm( $tags ) ;
    }

    // ------- topics

    $topics = $report->topics ?? null ;
    if( is_array( $topics ) )
    {
        $report->topics = hydrateDefinedTerm( $topics ) ;
    }

    // ------- author

    $author = $report->author ?? null ;
    if( is_array( $author ) )
    {
        $report->author = hydrateOrganizationOrPerson( $author ) ;
    }

    return $report ;
}
