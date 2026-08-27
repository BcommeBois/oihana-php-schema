<?php

namespace xyz\oihana\schema\helpers\hydrate\appointments;

use ReflectionException;

use org\schema\DefinedTerm;

use xyz\oihana\schema\appointments\VisitReport;
use xyz\oihana\schema\thesaurus\ThesaurusTerm;

use function oihana\core\arrays\isIndexed;

use function org\schema\helpers\hydrate\hydrateDefinedTerm;
use function org\schema\helpers\hydrate\hydrateOrganizationOrPerson;

use function xyz\oihana\schema\helpers\hydrate\hydrateCustomerEmployee;
use function xyz\oihana\schema\helpers\hydrate\termClassOf;

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
 * 🔑 **The four vocabularies are read as the class their family serves.** A report's `mood`
 * is a term of a **business** family, administered rather than harvested, and those families
 * carry properties {@see DefinedTerm} does not declare — `color` first among them. Hydrating
 * them as a plain `DefinedTerm` dropped those properties silently, so the same term changed
 * shape depending on whether it was read on its own family or inside a report.
 *
 * The class is a **parameter rather than a hard-wired name**, exactly as
 * {@see \xyz\oihana\schema\helpers\hydrate\hydrateParcelDelivery()} takes its travel terms :
 * naming a business class in an attribute carried by the Schema.org class would reverse the
 * arrow, where this helper lives on the `xyz` side and may know. It takes the two forms
 * {@see termClassOf()} reads — one class for the four properties, or a map naming them one by
 * one :
 *
 * ```php
 * hydrateVisitReport( $raw ) ;                       // the house term, everywhere
 * hydrateVisitReport( $raw , DefinedTerm::class ) ;  // one named class, everywhere
 * hydrateVisitReport( $raw , [ Prop::DEFAULT => ThesaurusTerm::class , VisitReport::MOOD => MoodTerm::class ] ) ;
 * ```
 *
 * 🔑 **A bare reference survives inside a list**, exactly as it does on its own : a list of
 * unresolved handles comes back as it stands, and only an entry that *was* an array and
 * resolved to nothing is dropped. The keys stay gap-free — a filtered list left with holes
 * serializes as a JSON object, and a consumer walking the value gets something it cannot walk.
 *
 * @param mixed $init Single report data or array of report data.
 * @param class-string<DefinedTerm>|array<string,class-string<DefinedTerm>|array<string,class-string<DefinedTerm>>> $termClass
 *        The class the four term properties are hydrated into, or a map naming them one by one.
 *        A map may carry entries this helper does not read — a report hydrated from a meeting
 *        receives the meeting's map as it stands, branches included. See {@see termClassOf()}.
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
 * $report->mood          instanceof ThesaurusTerm    ; // true
 * ```
 */
function hydrateVisitReport( mixed $init = null , string|array $termClass = ThesaurusTerm::class ) :mixed
{
    if( !is_array( $init ) )
    {
        return $init ;
    }

    if( isIndexed( $init ) )
    {
        $reports = array_map
        (
            fn( $report ) => hydrateVisitReport( $report , $termClass ) ,
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
        $report->mood = hydrateDefinedTerm( $mood , termClassOf( $termClass , VisitReport::MOOD ) ) ;
    }

    // ------- outcome

    $outcome = $report->outcome ?? null ;
    if( is_array( $outcome ) )
    {
        $report->outcome = hydrateDefinedTerm( $outcome , termClassOf( $termClass , VisitReport::OUTCOME ) ) ;
    }

    // ------- tags

    $tags = $report->tags ?? null ;
    if( is_array( $tags ) )
    {
        $report->tags = hydrateDefinedTerm( $tags , termClassOf( $termClass , VisitReport::TAGS ) ) ;
    }

    // ------- topics

    $topics = $report->topics ?? null ;
    if( is_array( $topics ) )
    {
        $report->topics = hydrateDefinedTerm( $topics , termClassOf( $termClass , VisitReport::TOPICS ) ) ;
    }

    // ------- author

    $author = $report->author ?? null ;
    if( is_array( $author ) )
    {
        $report->author = hydrateOrganizationOrPerson( $author ) ;
    }

    return $report ;
}
