<?php

namespace xyz\oihana\schema\helpers\hydrate\appointments;

use ReflectionException;

use org\schema\DefinedTerm;

use xyz\oihana\schema\appointments\MeetingReport;
use xyz\oihana\schema\thesaurus\ThesaurusTerm;

use function oihana\core\arrays\isIndexed;

use function org\schema\helpers\hydrate\hydrateDefinedTerm;
use function org\schema\helpers\hydrate\hydrateOrganizationOrPerson;

use function xyz\oihana\schema\helpers\hydrate\termClassOf;

/**
 * Hydrate an array definition with the MeetingReport class, or with a subclass of it.
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
 * - `followUp` — what comes next, through {@see hydrateFollowUp()} ;
 * - `tags` and `topics` — several terms each ;
 * - `author` — whoever wrote it, an `Organization|Person` union that only the payload's
 *   `@type` can settle.
 *
 * ⚠️ **`attendee` is deliberately left alone here.** Who sits at a table depends on the kind
 * of meeting — a customer's staff, a colleague — and this helper cannot know. Each family
 * resolves it after this one has run, which is what {@see hydrateVisitReport()} does.
 *
 * 🔑 **An empty `followUp` list stays an empty list.** « This report has no follow-up » is
 * an answer worth serving, and a reader walking the value deserves a list to walk —
 * {@see hydrateFollowUp()} holds that rule, and this helper writes back whatever it
 * answers.
 *
 * 🔑 **The vocabularies are read as the class their family serves.** Those families carry
 * properties {@see DefinedTerm} does not declare — `color` first among them — so hydrating
 * them as a plain `DefinedTerm` dropped those properties silently, and the same term changed
 * shape depending on where it was read. The class is a **parameter rather than a hard-wired
 * name**, in the two forms {@see termClassOf()} reads : one class for every property, or a
 * map naming them one by one.
 *
 * 🔑 **The target class is a parameter too**, so a subclass reuses this whole body rather
 * than copying it : {@see hydrateVisitReport()} asks for its own class here, then resolves
 * what only a visit carries.
 *
 * @param mixed $init Single report data or array of report data.
 * @param class-string<DefinedTerm>|array<string,class-string<DefinedTerm>|array<string,class-string<DefinedTerm>>> $termClass
 *        The class the term properties are hydrated into, or a map naming them one by one.
 *        A map may carry entries this helper does not read. See {@see termClassOf()}.
 * @param class-string<MeetingReport> $class The class to build — a subclass of {@see MeetingReport}, or itself.
 *
 * @return mixed
 *
 * @throws ReflectionException
 *
 * @example
 * ```php
 * $report = hydrateMeetingReport
 * ([
 *     'topics'   => [ [ 'id' => 'ROADMAP' ] ] ,
 *     'followUp' => [ [ 'followUpType' => [ 'id' => 'CALL_BACK' ] ] ] ,
 *     'text'     => 'Everything the boxes cannot hold.' ,
 * ]) ;
 *
 * $report->followUp[ 0 ] instanceof FollowUp      ; // true
 * $report->topics[ 0 ]   instanceof ThesaurusTerm ; // true
 * ```
 */
function hydrateMeetingReport( mixed $init = null , string|array $termClass = ThesaurusTerm::class , string $class = MeetingReport::class ) :mixed
{
    if( !is_array( $init ) )
    {
        return $init ;
    }

    if( isIndexed( $init ) )
    {
        $reports = array_map
        (
            fn( $report ) => hydrateMeetingReport( $report , $termClass , $class ) ,
            $init
        );

        // A scalar entry is an unresolved reference and is kept as it stands ; only an entry that
        // WAS an array and gave nothing is dropped. `array_values` closes the gaps it leaves.
        $filtered = array_values( array_filter( $reports , fn( $report ) => $report instanceof $class || is_scalar( $report ) ) ) ;

        return count( $filtered ) > 0 ? $filtered : null ;
    }

    $report = new $class( $init ) ;

    // ------- followUp

    $followUp = $report->followUp ?? null ;
    if( is_array( $followUp ) )
    {
        $report->followUp = hydrateFollowUp( $followUp ) ;
    }

    // ------- tags

    $tags = $report->tags ?? null ;
    if( is_array( $tags ) )
    {
        $report->tags = hydrateDefinedTerm( $tags , termClassOf( $termClass , MeetingReport::TAGS ) ) ;
    }

    // ------- topics

    $topics = $report->topics ?? null ;
    if( is_array( $topics ) )
    {
        $report->topics = hydrateDefinedTerm( $topics , termClassOf( $termClass , MeetingReport::TOPICS ) ) ;
    }

    // ------- author

    $author = $report->author ?? null ;
    if( is_array( $author ) )
    {
        $report->author = hydrateOrganizationOrPerson( $author ) ;
    }

    return $report ;
}
