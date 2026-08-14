<?php

namespace tests\org\schema ;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;

use oihana\reflect\Reflection;

use org\schema\constants\Schema;
use org\schema\constants\traits\Ticket as TicketProperties;
use org\schema\Intangible;
use org\schema\Organization;
use org\schema\Person;
use org\schema\Reservation;
use org\schema\Seat;
use org\schema\Ticket;

class TicketTest extends TestCase
{
    public function testIsIntangible(): void
    {
        $this->assertInstanceOf( Intangible::class , new Ticket() );
    }

    public function testConstructorCopiesTheProperties(): void
    {
        $ticket = new Ticket
        ([
            Schema::DATE_ISSUED    => '2026-08-14' ,
            Schema::PRICE_CURRENCY => 'EUR' ,
            Schema::TICKET_NUMBER  => 'T-42' ,
            Schema::TICKET_TOKEN   => 'https://example.org/qr/T-42' ,
            Schema::TOTAL_PRICE    => 149.90 ,
            Schema::ISSUED_BY      => new Organization([ Schema::NAME => 'Etchea Airlines' ] ) ,
            Schema::TICKETED_SEAT  => new Seat( [ 'seatNumber' => '12A' ] ) ,
            Schema::UNDER_NAME     => new Person([ Schema::NAME => 'Ada Lovelace' ] ) ,
        ]);

        $this->assertSame( '2026-08-14'                  , $ticket->dateIssued  );
        $this->assertSame( 'EUR'                         , $ticket->priceCurrency );
        $this->assertSame( 'T-42'                        , $ticket->ticketNumber  );
        $this->assertSame( 'https://example.org/qr/T-42' , $ticket->ticketToken   );
        $this->assertSame( 149.90                        , $ticket->totalPrice    );

        $this->assertInstanceOf( Organization::class , $ticket->issuedBy     );
        $this->assertInstanceOf( Seat::class         , $ticket->ticketedSeat );
        $this->assertInstanceOf( Person::class       , $ticket->underName    );
    }

    /**
     * The structured properties take a raw array as well as an instance.
     *
     * The constructor assigns what it is handed without typing it, so a ticket
     * read from storage — nested arrays, nothing hydrated — has to be assignable
     * as it stands ; a union naming only the class would throw on the way in, on
     * the exact path a stored reservation takes.
     */
    public function testStructuredPropertiesAcceptRawArrays(): void
    {
        $ticket = new Ticket
        ([
            Schema::ISSUED_BY      => [ Schema::NAME => 'Etchea Airlines' ] ,
            Schema::PRICE_CURRENCY => [ 'termCode' => 'EUR' ] ,
            Schema::TICKETED_SEAT  => [ 'seatNumber' => '12A' ] ,
            Schema::TOTAL_PRICE    => [ 'price' => 149.90 , 'priceCurrency' => 'EUR' ] ,
            Schema::UNDER_NAME     => [ Schema::NAME => 'Ada Lovelace' ] ,
        ]);

        $this->assertIsArray( $ticket->issuedBy      );
        $this->assertIsArray( $ticket->priceCurrency );
        $this->assertIsArray( $ticket->ticketedSeat  );
        $this->assertIsArray( $ticket->totalPrice    );
        $this->assertIsArray( $ticket->underName     );
    }

    /**
     * A ticket reached through the reservation that carries it stays a ticket :
     * the property is the whole seam between the two classes.
     *
     * @throws ReflectionException
     */
    public function testAReservationCarriesItsTicket(): void
    {
        $reservation = ( new Reflection() )->hydrate
        (
            [
                Schema::RESERVATION_ID  => 'RES-7' ,
                Schema::RESERVED_TICKET => [ Schema::TICKET_NUMBER => 'T-42' ] ,
            ] ,
            Reservation::class
        );

        $this->assertInstanceOf( Ticket::class , $reservation->reservedTicket );
        $this->assertSame( 'T-42' , $reservation->reservedTicket->ticketNumber );
    }

    public function testPropertyNameConstants(): void
    {
        $this->assertSame( 'dateIssued'    , Schema::DATE_ISSUED    );
        $this->assertSame( 'issuedBy'      , Schema::ISSUED_BY      );
        $this->assertSame( 'priceCurrency' , Schema::PRICE_CURRENCY );
        $this->assertSame( 'ticketNumber'  , Schema::TICKET_NUMBER  );
        $this->assertSame( 'ticketToken'   , Schema::TICKET_TOKEN   );
        $this->assertSame( 'ticketedSeat'  , Schema::TICKETED_SEAT  );
        $this->assertSame( 'totalPrice'    , Schema::TOTAL_PRICE    );
        $this->assertSame( 'underName'     , Schema::UNDER_NAME     );
    }

    /**
     * Every constant of the trait names a property that really exists on the class.
     */
    public function testEveryConstantNamesAnExistingProperty(): void
    {
        $constants = ( new ReflectionClass( TicketProperties::class ) )->getConstants() ;

        $this->assertNotEmpty( $constants );

        foreach ( $constants as $constant => $property )
        {
            $this->assertTrue
            (
                property_exists( Ticket::class , $property ) ,
                sprintf( 'Ticket has no "%s" property, named by the %s constant.' , $property , $constant )
            );
        }
    }
}
