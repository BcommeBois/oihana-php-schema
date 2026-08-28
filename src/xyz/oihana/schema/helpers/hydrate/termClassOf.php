<?php

namespace xyz\oihana\schema\helpers\hydrate;

use org\schema\constants\Prop;
use org\schema\DefinedTerm;

use xyz\oihana\schema\thesaurus\ThesaurusTerm;

/**
 * Answer the class one term property is hydrated into.
 *
 * A hydrator that resolves several vocabularies takes **one** `$termClass` parameter rather
 * than one per property, and reads it through this helper. The parameter accepts two forms,
 * and the short one is the common case :
 *
 * - a **class name** — every term of the entity is hydrated into it ;
 * - a **map** — `[ Prop::DEFAULT => …, '<property>' => … ]`, where each key names a property
 *   and {@see Prop::DEFAULT} covers everything left unnamed.
 *
 * The two forms are the same statement at two levels of detail, so a caller only writes a map
 * the day one family stops answering what the others answer. Anything a map does not name —
 * no key for the property, no {@see Prop::DEFAULT} — falls back to {@see ThesaurusTerm}, the
 * class the house families serve.
 *
 * 🔑 **A nested branch is not a class, and is never answered as one.** A map may carry a sub-map
 * under a property that holds a whole entity rather than a term — `report`, for instance,
 * whose own terms belong to their own families. Such an entry is not a class name, so it is
 * never returned here : the hydrator that owns that property reads the branch itself and
 * hands it down. This helper answers classes, and only classes.
 *
 * ⚠️ **A map is keyed by property, not by family.** Two entities of one hierarchy may declare
 * the same property name over two different families — a meeting's `tags` and its report's
 * `tags` are not the same vocabulary. One flat map cannot tell them apart, which is exactly
 * what the nested branch is for : the inner entity inherits the outer map until the caller
 * gives it one of its own.
 *
 * @param class-string<DefinedTerm>|array<string,class-string<DefinedTerm>|array<string,class-string<DefinedTerm>>> $termClass
 *        A class name, or a map of property name to class name.
 * @param string $property The property whose class is asked for.
 *
 * @return class-string<DefinedTerm>
 *
 * @example
 * ```php
 * termClassOf( ProductCategoryTerm::class , 'mood' ) ; // ProductCategoryTerm — the short form
 *
 * $map =
 * [
 *     Prop::DEFAULT               => ThesaurusTerm::class ,
 *     VisitReport::MOOD           => MoodTerm::class ,
 *     Appointment::REPORT => [ VisitReport::MOOD => ReportMoodTerm::class ] , // a branch
 * ];
 *
 * termClassOf( $map , VisitReport::MOOD    ) ; // MoodTerm
 * termClassOf( $map , VisitReport::OUTCOME ) ; // ThesaurusTerm — through Prop::DEFAULT
 * termClassOf( $map , Appointment::REPORT ) ; // ThesaurusTerm — a branch is not a class
 * ```
 */
function termClassOf( string|array $termClass , string $property ) :string
{
    if( is_string( $termClass ) )
    {
        return $termClass ;
    }

    $class = $termClass[ $property ] ?? null ;

    // A nested branch is a map, not a class name : it belongs to whoever owns that property,
    // and it leaves this one unnamed — which is precisely what the default key covers.
    if( !is_string( $class ) )
    {
        $class = $termClass[ Prop::DEFAULT ] ?? null ;
    }

    return is_string( $class ) ? $class : ThesaurusTerm::class ;
}
