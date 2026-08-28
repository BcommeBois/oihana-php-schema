<?php

namespace tests\xyz\oihana\schema\appointments ;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionProperty;

use org\schema\Event;

use xyz\oihana\schema\appointments\Appointment;
use xyz\oihana\schema\appointments\InternalMeeting;

class InternalMeetingTest extends TestCase
{
    public function testIsAnAppointment(): void
    {
        $meeting = new InternalMeeting() ;

        $this->assertInstanceOf( Appointment::class , $meeting );
        $this->assertInstanceOf( Event::class       , $meeting );
    }

    public function testSchemaType(): void
    {
        $this->assertSame( 'https://schema.oihana.xyz/InternalMeeting' , InternalMeeting::getSchemaType() );
    }

    /**
     * 🔑 The family is told apart by the type a document carries — which is what
     * filters, facets and permissions read — and by nothing else : the class declares
     * no property of its own, and does not need to.
     */
    public function testItAddsNoPropertyOfItsOwn(): void
    {
        $own = array_filter
        (
            new ReflectionClass( InternalMeeting::class )->getProperties() ,
            fn( ReflectionProperty $property ) => $property->getDeclaringClass()->getName() === InternalMeeting::class
        );

        $this->assertSame( [] , $own );
    }
}
