<?php

namespace xyz\oihana\schema\helpers\hydrate\appointments;

use ReflectionException;

use xyz\oihana\schema\appointments\Appointment;
use xyz\oihana\schema\appointments\FollowUp;

use function oihana\core\arrays\isIndexed;

use function org\schema\helpers\hydrate\hydrateDefinedTerm;
use function org\schema\helpers\hydrate\hydrateOrganizationOrPerson;

/**
 * Hydrate an array definition with the FollowUp class.
 *
 * Handles both a single follow-up array and an array of follow-ups — the second is the
 * usual shape, a report commonly carrying « call them back » beside « send the quote ».
 *
 * Each nested reference is hydrated only when the raw value is an array — when there is
 * something to hydrate. The helper's answer is then written as is, `null` included : an
 * array that resolves to nothing becomes `null`, never a leftover raw array. Anything
 * that is not an array — an unresolved string reference, an already typed instance — is
 * left untouched.
 *
 * 🚨 **`result` is built flat, and deliberately so.** It names the meeting booked to
 * honour the promise ; that meeting has a report, that report has follow-ups, and each of
 * those may name a meeting in turn. Going down through
 * {@see hydrateAppointment()} would follow that chain for as long as the data
 * holds, and only the data would stop it. What is named here is a **reference** — a
 * meeting to open, not a document to unfold — so it is typed one level and no further :
 * its own nested references stay raw, and a consumer that needs them asks for that
 * meeting on its own.
 *
 * 🔑 **An empty list is kept as an empty list**, where the rest of the family answers
 * `null` — the {@see \xyz\oihana\schema\helpers\hydrate\documents\hydrateAdjustment()}
 * rule. « This report has no follow-up » is an answer, and it is not the answer « nothing
 * here was readable » : a consumer mapping over the value deserves the empty list it can
 * map over. A non-empty list that hydrates to nothing keeps the family's `null`.
 *
 * 🔑 **A bare reference survives inside a list**, exactly as it does on its own : a list of
 * unresolved handles comes back as it stands, and only an entry that *was* an array and
 * resolved to nothing is dropped. The keys stay gap-free — a filtered list left with holes
 * serializes as a JSON object, and a consumer walking the value gets something it cannot walk.
 *
 * @param mixed $init Single follow-up data or array of follow-up data.
 *
 * @return mixed
 *
 * @throws ReflectionException
 *
 * @example
 * ```php
 * $followUps = hydrateFollowUp
 * ([
 *     [
 *         'followUpType'  => [ 'id' => 'CALL_BACK' , 'name' => 'Call back' ] ,
 *         'scheduledTime' => '2026-09-15' ,
 *         'agent'         => [ '@type' => 'Person' , 'name' => 'Jane Doe' ] ,
 *     ] ,
 * ]) ;
 *
 * $followUps[ 0 ]->followUpType instanceof DefinedTerm ; // true
 * ```
 */
function hydrateFollowUp( mixed $init = null ) :mixed
{
    if( !is_array( $init ) )
    {
        return $init ;
    }

    if( isIndexed( $init ) )
    {
        if( count( $init ) === 0 )
        {
            return $init ;
        }

        $followUps = array_map
        (
            fn( $followUp ) => hydrateFollowUp( $followUp ) ,
            $init
        );

        // A scalar entry is an unresolved reference and is kept as it stands ; only an entry that
        // WAS an array and gave nothing is dropped. `array_values` closes the gaps it leaves.
        $filtered = array_values( array_filter( $followUps , fn( $followUp ) => $followUp instanceof FollowUp || is_scalar( $followUp ) ) ) ;

        return count( $filtered ) > 0 ? $filtered : null ;
    }

    $followUp = new FollowUp( $init ) ;

    // ------- followUpType

    $followUpType = $followUp->followUpType ?? null ;
    if( is_array( $followUpType ) )
    {
        $followUp->followUpType = hydrateDefinedTerm( $followUpType ) ;
    }

    // ------- agent

    $agent = $followUp->agent ?? null ;
    if( is_array( $agent ) )
    {
        $followUp->agent = hydrateOrganizationOrPerson( $agent ) ;
    }

    // ------- result
    // One level, never the deep helper : see the note above.

    $result = $followUp->result ?? null ;
    if( is_array( $result ) )
    {
        if( isIndexed( $result ) )
        {
            $meetings = array_map
            (
                fn( $meeting ) => is_array( $meeting ) ? new Appointment( $meeting ) : $meeting ,
                $result
            );

            // A scalar entry is an unresolved reference and is kept as it stands ; only an entry that
            // WAS an array and gave nothing is dropped. `array_values` closes the gaps it leaves.
            $filtered = array_values( array_filter( $meetings , fn( $meeting ) => $meeting instanceof Appointment || is_scalar( $meeting ) ) ) ;

            $followUp->result = count( $filtered ) > 0 ? $filtered : null ;
        }
        else
        {
            $followUp->result = new Appointment( $result ) ;
        }
    }

    return $followUp ;
}
