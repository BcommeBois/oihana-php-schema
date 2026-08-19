<?php

namespace tests\xyz\oihana\schema\people ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\Reflection;

use org\schema\constants\Schema;
use org\schema\OpeningHoursSpecification;

use xyz\oihana\schema\people\Person;
use xyz\oihana\schema\people\Seller;

class SellerTest extends TestCase
{
    public function testIsAPerson(): void
    {
        $this->assertInstanceOf( Person::class , new Seller() );
    }

    public function testHoursAvailableDefaultsToNull(): void
    {
        $this->assertNull( new Seller()->hoursAvailable ?? null );
    }

    /**
     * The ordinary rhythm and the closures are written the same way : a
     * specification stating days and hours is when meetings are taken, one stating
     * a range of dates and no hours is a week away.
     *
     * @throws ReflectionException
     */
    public function testItStatesTheHoursItTakesAppointmentsIn(): void
    {
        $seller = new Reflection()->hydrate
        (
            [
                Schema::HOURS_AVAILABLE =>
                [
                    [ Schema::DAY_OF_WEEK => [ 'Monday' , 'Tuesday' ] , Schema::OPENS => '08:30' , Schema::CLOSES => '18:00' ] ,
                    [ Schema::VALID_FROM  => '2026-08-10' , Schema::VALID_THROUGH => '2026-08-21' ] ,
                ],
            ],
            Seller::class
        );

        $this->assertIsArray( $seller->hoursAvailable );
        $this->assertCount( 2 , $seller->hoursAvailable );
        $this->assertInstanceOf( OpeningHoursSpecification::class , $seller->hoursAvailable[ 0 ] );
        $this->assertSame( '08:30' , $seller->hoursAvailable[ 0 ]->opens );

        $holiday = $seller->hoursAvailable[ 1 ] ;

        $this->assertSame( '2026-08-10' , $holiday->validFrom    );
        $this->assertSame( '2026-08-21' , $holiday->validThrough );
        $this->assertNull( $holiday->opens ?? null );
    }

    /**
     * Saying nothing is the safe reading : whoever offers a slot has nothing to
     * offer from, rather than the whole clock.
     */
    public function testSayingNothingIsNotAnOpening(): void
    {
        $this->assertNull( new Seller([ Schema::NAME => 'Jane Doe' ] )->hoursAvailable ?? null );
    }
}
