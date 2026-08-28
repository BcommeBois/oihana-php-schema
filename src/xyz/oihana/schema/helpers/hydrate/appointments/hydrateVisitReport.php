<?php

namespace xyz\oihana\schema\helpers\hydrate\appointments;

use ReflectionException;

use org\schema\DefinedTerm;

use xyz\oihana\schema\appointments\VisitReport;
use xyz\oihana\schema\thesaurus\ThesaurusTerm;

use function oihana\core\arrays\isIndexed;

use function org\schema\helpers\hydrate\hydrateDefinedTerm;

use function xyz\oihana\schema\helpers\hydrate\hydrateCustomerEmployee;
use function xyz\oihana\schema\helpers\hydrate\termClassOf;

/**
 * Hydrate an array definition with the VisitReport class.
 *
 * Handles both a single report array and an array of reports.
 *
 * 🔑 **What every report carries is resolved by {@see hydrateMeetingReport()}** — the
 * promises, the qualifiers, what was covered, who wrote it — and this helper asks it for a
 * {@see VisitReport} rather than copying its body. What is left here is what only a visit
 * carries :
 *
 * - `attendee` — the people actually met, as {@see \xyz\oihana\schema\people\CustomerEmployee},
 *   one or several. The parent leaves the union wide on purpose : who sits at a table depends
 *   on the kind of meeting, and a visit is the family that knows ;
 * - `mood` and `outcome` — one term each, how it felt and what it produced.
 *
 * 🔑 **The vocabularies are read as the class their family serves**, in the two forms
 * {@see termClassOf()} reads — one class for every property, or a map naming them one by one :
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
 *        The class the term properties are hydrated into, or a map naming them one by one.
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

    $report = hydrateMeetingReport( $init , $termClass , VisitReport::class ) ;

    if( !$report instanceof VisitReport )
    {
        return $report ;
    }

    // ------- attendee

    $attendee = $report->attendee ?? null ;
    if( is_array( $attendee ) )
    {
        $report->attendee = hydrateCustomerEmployee( $attendee ) ;
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

    return $report ;
}
