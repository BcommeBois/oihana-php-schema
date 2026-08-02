<?php

namespace tests\org\schema\helpers\hydrate ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use org\schema\ContactPoint;

use function org\schema\helpers\hydrate\hydrateContactPoint;

final class HydrateContactPointTest extends TestCase
{
    /**
     * @throws ReflectionException
     */
    public function testHydratesAnIndexedArrayOfContactPoints(): void
    {
        $contacts = hydrateContactPoint(
        [
            [ 'telephone' => '05 59 00 00 00' ] ,
            [ 'email'     => 'contact@example.org' ] ,
        ]) ;

        $this->assertIsArray( $contacts ) ;
        $this->assertCount( 2 , $contacts ) ;
        $this->assertContainsOnlyInstancesOf( ContactPoint::class , $contacts ) ;
        $this->assertSame( '05 59 00 00 00'     , $contacts[0]->telephone ) ;
        $this->assertSame( 'contact@example.org' , $contacts[1]->email ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testReturnsNullOnNullEmptyOrAssociativeInput(): void
    {
        $this->assertNull( hydrateContactPoint() ) ;
        $this->assertNull( hydrateContactPoint( [] ) ) ;
        $this->assertNull( hydrateContactPoint( [ 'telephone' => '05 59 00 00 00' ] ) ) ;
    }

    /**
     * A lone instance and an unresolved reference are legal shapes of the
     * `contactPoint` property. The signature used to be `?array`, so callers
     * feeding it straight from that property — as `hydrateCustomer()` does —
     * raised a TypeError on both.
     *
     * @throws ReflectionException
     */
    public function testHandsBackAnythingThatIsNotAnArray(): void
    {
        $contact = new ContactPoint( [ 'telephone' => '05 59 00 00 00' ] ) ;

        $this->assertSame( $contact         , hydrateContactPoint( $contact ) ) ;
        $this->assertSame( 'contact-ref-42' , hydrateContactPoint( 'contact-ref-42' ) ) ;
        $this->assertSame( 42               , hydrateContactPoint( 42 ) ) ;
    }
}
