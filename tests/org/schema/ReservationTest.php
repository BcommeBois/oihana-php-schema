<?php

namespace tests\org\schema ;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;

use oihana\reflect\Reflection;

use org\schema\constants\Schema;
use org\schema\constants\traits\FlightReservation as FlightReservationProperties;
use org\schema\constants\traits\FoodEstablishmentReservation as FoodEstablishmentReservationProperties;
use org\schema\constants\traits\LodgingReservation as LodgingReservationProperties;
use org\schema\constants\traits\RentalCarReservation as RentalCarReservationProperties;
use org\schema\constants\traits\Reservation as ReservationProperties;
use org\schema\constants\traits\ReservationPackage as ReservationPackageProperties;
use org\schema\constants\traits\TaxiReservation as TaxiReservationProperties;
use org\schema\enumerations\ReservationStatusType;
use org\schema\Event;
use org\schema\Intangible;
use org\schema\Organization;
use org\schema\Person;
use org\schema\Place;
use org\schema\ProgramMembership;
use org\schema\Reservation;
use org\schema\reservations\BoatReservation;
use org\schema\reservations\BusReservation;
use org\schema\reservations\EventReservation;
use org\schema\reservations\FlightReservation;
use org\schema\reservations\FoodEstablishmentReservation;
use org\schema\reservations\LodgingReservation;
use org\schema\reservations\RentalCarReservation;
use org\schema\reservations\ReservationPackage;
use org\schema\reservations\TaxiReservation;
use org\schema\reservations\TrainReservation;
use org\schema\Ticket;

class ReservationTest extends TestCase
{
    public function testIsIntangible(): void
    {
        $this->assertInstanceOf( Intangible::class , new Reservation() );
    }

    /**
     * @return array<int,array{0:string}>
     */
    public static function provideSubTypes(): array
    {
        return
        [
            [ BoatReservation::class              ] ,
            [ BusReservation::class               ] ,
            [ EventReservation::class             ] ,
            [ FlightReservation::class            ] ,
            [ FoodEstablishmentReservation::class ] ,
            [ LodgingReservation::class           ] ,
            [ RentalCarReservation::class         ] ,
            [ ReservationPackage::class           ] ,
            [ TaxiReservation::class              ] ,
            [ TrainReservation::class             ] ,
        ];
    }

    /**
     * Every sub-type inherits the whole reservation vocabulary : a consumer reading
     * `reservationId` or `underName` does it once, whatever it booked.
     */
    #[DataProvider( 'provideSubTypes' )]
    public function testSubTypesExtendReservation( string $class ): void
    {
        $reservation = new $class() ;

        $this->assertInstanceOf( Reservation::class , $reservation );
        $this->assertNull( $reservation->reservationId ?? null );
    }

    /**
     * @throws ReflectionException
     */
    public function testConstructorCopiesTheProperties(): void
    {
        $reservation = new Reservation
        ([
            Schema::BOOKING_TIME        => '2026-08-14T10:00:00+02:00' ,
            Schema::MODIFIED_TIME       => '2026-08-15T11:30:00+02:00' ,
            Schema::PRICE_CURRENCY      => 'EUR' ,
            Schema::RESERVATION_ID      => 'RES-42' ,
            Schema::RESERVATION_STATUS  => ReservationStatusType::RESERVATION_CONFIRMED ,
            Schema::TOTAL_PRICE         => 149.90 ,
            Schema::UNDER_NAME          => new Person([ Schema::NAME => 'Ada Lovelace' ] ) ,
        ]);

        $this->assertSame( '2026-08-14T10:00:00+02:00' , $reservation->bookingTime       );
        $this->assertSame( '2026-08-15T11:30:00+02:00' , $reservation->modifiedTime      );
        $this->assertSame( 'EUR'                       , $reservation->priceCurrency     );
        $this->assertSame( 'RES-42'                    , $reservation->reservationId     );
        $this->assertSame( 149.90                      , $reservation->totalPrice        );
        $this->assertInstanceOf( Person::class         , $reservation->underName         );

        $this->assertTrue( ReservationStatusType::includes( $reservation->reservationStatus ) );
    }

    /**
     * What is reserved is a `Thing` : a flight, an event, a restaurant, a rental
     * car. Narrowing it to the parties of the booking would leave the only
     * property saying *what* was booked unable to carry it.
     */
    public function testReservationForAcceptsAnyThing(): void
    {
        $event = new EventReservation([ Schema::RESERVATION_FOR => new Event([ Schema::NAME => 'Oihana Conference 2026' ] ) ] );
        $place = new LodgingReservation([ Schema::RESERVATION_FOR => new Place([ Schema::NAME => 'Hotel Etchea'   ] ) ] );

        $this->assertInstanceOf( Event::class , $event->reservationFor );
        $this->assertInstanceOf( Place::class , $place->reservationFor );

        $this->assertSame( 'Oihana Conference 2026' , $event->reservationFor->name );
        $this->assertSame( 'Hotel Etchea'           , $place->reservationFor->name );
    }

    /**
     * The ticket property is named `reservedTicket`, the spelling schema.org
     * publishes : a payload written with any other key is dropped on the way in.
     */
    public function testReservedTicketCarriesTheTicket(): void
    {
        $reservation = new Reservation([ Schema::RESERVED_TICKET => new Ticket([ Schema::TICKET_NUMBER => 'T-1' ] ) ] );

        $this->assertInstanceOf( Ticket::class , $reservation->reservedTicket );
        $this->assertSame( 'T-1' , $reservation->reservedTicket->ticketNumber );
        $this->assertSame( 'reservedTicket' , Schema::RESERVED_TICKET );
    }

    public function testProgramMembershipUsedCarriesTheLoyaltyProgram(): void
    {
        $reservation = new Reservation
        ([
            Schema::PROGRAM_MEMBERSHIP_USED => new ProgramMembership
            ([
                Schema::MEMBERSHIP_NUMBER    => 'FF-99' ,
                Schema::HOSTING_ORGANIZATION => new Organization([ Schema::NAME => 'Etchea Airlines' ] ) ,
            ]) ,
        ]);

        $this->assertInstanceOf( ProgramMembership::class , $reservation->programMembershipUsed );
        $this->assertSame( 'FF-99' , $reservation->programMembershipUsed->membershipNumber );
    }

    /**
     * @throws ReflectionException
     */
    public function testHydrationKeepsTheScalarProperties(): void
    {
        $reservation = ( new Reflection() )->hydrate
        (
            [
                Schema::RESERVATION_ID     => 'RES-7' ,
                Schema::PRICE_CURRENCY     => 'EUR' ,
                Schema::TOTAL_PRICE        => '149.90' ,
                Schema::RESERVATION_STATUS => ReservationStatusType::RESERVATION_HOLD ,
            ] ,
            Reservation::class
        );

        $this->assertSame( 'RES-7'  , $reservation->reservationId );
        $this->assertSame( 'EUR'    , $reservation->priceCurrency );
        $this->assertSame( '149.90' , $reservation->totalPrice    );
        $this->assertSame( ReservationStatusType::RESERVATION_HOLD , $reservation->reservationStatus );
    }

    /**
     * A package states the reservations it groups, each of them a reservation in
     * its own right.
     */
    public function testAPackageGroupsItsSubReservations(): void
    {
        $package = new ReservationPackage
        ([
            Schema::SUB_RESERVATION =>
            [
                new FlightReservation([ Schema::RESERVATION_ID => 'FL-1' ] ) ,
                new LodgingReservation([ Schema::RESERVATION_ID => 'LO-1' ] ) ,
            ] ,
        ]);

        $this->assertIsArray( $package->subReservation );
        $this->assertCount( 2 , $package->subReservation );
        $this->assertSame( 'FL-1' , $package->subReservation[ 0 ]->reservationId );
    }

    public function testPropertyNameConstants(): void
    {
        $this->assertSame( 'bookingTime'           , Schema::BOOKING_TIME            );
        $this->assertSame( 'broker'                , Schema::BROKER                  );
        $this->assertSame( 'modifiedTime'          , Schema::MODIFIED_TIME           );
        $this->assertSame( 'priceCurrency'         , Schema::PRICE_CURRENCY          );
        $this->assertSame( 'programMembershipUsed' , Schema::PROGRAM_MEMBERSHIP_USED );
        $this->assertSame( 'provider'              , Schema::PROVIDER                );
        $this->assertSame( 'reservationFor'        , Schema::RESERVATION_FOR         );
        $this->assertSame( 'reservationId'         , Schema::RESERVATION_ID          );
        $this->assertSame( 'reservationStatus'     , Schema::RESERVATION_STATUS      );
        $this->assertSame( 'reservedTicket'        , Schema::RESERVED_TICKET         );
        $this->assertSame( 'totalPrice'            , Schema::TOTAL_PRICE             );
        $this->assertSame( 'underName'             , Schema::UNDER_NAME              );
    }

    /**
     * @return array<int,array{0:string,1:string}>
     */
    public static function providePropertyTraits(): array
    {
        return
        [
            [ ReservationProperties::class                 , Reservation::class                  ] ,
            [ FlightReservationProperties::class            , FlightReservation::class            ] ,
            [ FoodEstablishmentReservationProperties::class , FoodEstablishmentReservation::class ] ,
            [ LodgingReservationProperties::class           , LodgingReservation::class           ] ,
            [ RentalCarReservationProperties::class         , RentalCarReservation::class         ] ,
            [ ReservationPackageProperties::class           , ReservationPackage::class           ] ,
            [ TaxiReservationProperties::class              , TaxiReservation::class              ] ,
        ];
    }

    /**
     * Every constant of a reservation trait names a property that really exists on
     * the class it describes.
     *
     * A constant is only useful as a key nobody has to spell by hand, so one whose
     * value drifts from the property it names writes a key the class silently
     * drops. Reading the trait rather than a hand written list covers a constant
     * added later the day it lands.
     */
    #[DataProvider( 'providePropertyTraits' )]
    public function testEveryConstantNamesAnExistingProperty( string $trait , string $class ): void
    {
        $constants = ( new ReflectionClass( $trait ) )->getConstants() ;

        $this->assertNotEmpty( $constants );

        foreach ( $constants as $constant => $property )
        {
            $this->assertTrue
            (
                property_exists( $class , $property ) ,
                sprintf( '%s has no "%s" property, named by the %s constant.' , $class , $property , $constant )
            );
        }
    }

    /**
     * The other way round : every property of a reservation class is reachable
     * through a `Schema` constant, so nothing has to be spelled by hand.
     */
    #[DataProvider( 'providePropertyTraits' )]
    public function testEveryPropertyHasItsConstant( string $trait , string $class ): void
    {
        $values     = array_values( ( new ReflectionClass( Schema::class ) )->getConstants() ) ;
        $reflection = new ReflectionClass( $class ) ;

        foreach ( $reflection->getProperties() as $property )
        {
            if ( $property->getDeclaringClass()->getName() !== $class )
            {
                continue ;
            }

            $this->assertContains
            (
                $property->getName() ,
                $values ,
                sprintf( 'No Schema constant names the "%s" property of %s.' , $property->getName() , $class )
            );
        }
    }
}
