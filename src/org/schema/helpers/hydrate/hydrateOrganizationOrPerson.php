<?php

namespace org\schema\helpers\hydrate;

use ReflectionException;

use oihana\reflect\Reflection;
use oihana\reflect\exceptions\HydrationException;

use org\schema\constants\Schema;
use org\schema\Organization;
use org\schema\Person;

use function oihana\core\arrays\isIndexed;

/**
 * Hydrate an array definition into an Organization or a Person.
 *
 * `Organization|Person` is a common Schema.org union (e.g. `Order::$customer`,
 * `Order::$seller`, `Invoice::$broker`/`$provider`, `Event::$funder`...) that
 * {@see Reflection::hydrate()} cannot resolve from the property type alone : the
 * declared union always resolves to its first class member, so a `Person` payload
 * silently comes out as an (empty-ish) `Organization`. This helper reads the
 * payload's JSON-LD `@type` instead : `Person` gives a {@see Person} (or
 * `$personClass`), anything else — `Organization` itself, or one of its many
 * subtypes (`Corporation`, `LocalBusiness`, `GovernmentOrganization`...) — gives
 * an {@see Organization} (or `$organizationClass`), which stays the safe default
 * when `@type` is absent or unrecognized.
 *
 * `$organizationClass`/`$personClass` let a caller pin the target to a business
 * subtype instead of the plain Schema.org class — e.g.
 * `xyz\oihana\schema\organizations\Customer` (extends `Organization`) or
 * `xyz\oihana\schema\people\CustomerEmployee` (extends `Person`) — as long as
 * each stays a subclass of the type it replaces, so the property's declared
 * union type still holds.
 *
 * Handles both a single definition and an indexed list of definitions.
 *
 * 🔑 **A bare reference survives inside a list**, exactly as it does on its own : a list of
 * unresolved handles comes back as it stands, and only an entry that *was* an array and
 * resolved to nothing is dropped. The keys stay gap-free — a filtered list left with holes
 * serializes as a JSON object, and a consumer walking the value gets something it cannot walk.
 *
 * @param mixed  $init              Single Organization/Person data, a list of such data, or any other value.
 * @param string $organizationClass The class hydrated when the payload is not a Person. Must extend {@see Organization}.
 * @param string $personClass       The class hydrated when the payload's `@type` says Person. Must extend {@see Person}.
 *
 * @return mixed
 *
 * @throws HydrationException
 * @throws ReflectionException
 */
function hydrateOrganizationOrPerson( mixed $init = null , string $organizationClass = Organization::class , string $personClass = Person::class ) :mixed
{
    if( !is_array( $init ) )
    {
        return $init ;
    }

    if( isIndexed( $init ) )
    {
        $entities = array_map
        (
            fn( $entity ) => hydrateOrganizationOrPerson( $entity , $organizationClass , $personClass ) ,
            $init
        );

        // A scalar entry is an unresolved reference and is kept as it stands ; only an entry that
        // WAS an array and gave nothing is dropped. `array_values` closes the gaps it leaves.
        $filtered = array_values( array_filter( $entities , fn( $entity ) => $entity instanceof Organization || $entity instanceof Person || is_scalar( $entity ) ) ) ;

        return count( $filtered ) > 0 ? $filtered : null ;
    }

    static $reflection = null ;

    $reflection ??= new Reflection() ;

    $type = $init[ Schema::AT_TYPE ] ?? null ;

    $class = is_string( $type ) && strtolower( $type ) === 'person'
           ? $personClass
           : $organizationClass ;

    return $reflection->hydrate( $init , $class ) ;
}
