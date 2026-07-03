<?php

namespace tests\xyz\oihana\schema\traits ;

use PHPUnit\Framework\TestCase;

use org\schema\ContactPoint;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\enumerations\ContactType;
use xyz\oihana\schema\traits\SetContactPointTrait;

/**
 * Host exposing the protected {@see SetContactPointTrait} methods through
 * public wrappers, with the `contactPoint` property expected by the trait.
 */
class SetContactPointHost
{
    use SetContactPointTrait ;

    public null|array|ContactPoint $contactPoint = null ;

    public function set( string $name , mixed $value ): bool
    {
        return $this->setContactPointProperty( $name , $value ) ;
    }

    public function find( ?string $type ): ?ContactPoint
    {
        return $this->findContactPointByType( $type ) ;
    }

    public function validPhone( string $phone ): bool
    {
        return $this->isValidPhoneNumber( $phone ) ;
    }
}

class SetContactPointTraitTest extends TestCase
{
    // ---- setContactPointProperty

    public function testSetTelephoneCreatesAContactPoint(): void
    {
        $host = new SetContactPointHost() ;

        $this->assertTrue( $host->set( Oihana::DEFAULT_TELEPHONE , '0102030405' ) ) ;

        $this->assertCount( 1 , $host->contactPoint ) ;
        $this->assertSame( '0102030405'          , $host->contactPoint[0]->telephone   ) ;
        $this->assertSame( ContactType::DEFAULT  , $host->contactPoint[0]->contactType ) ;
    }

    public function testSetEmailCreatesAContactPoint(): void
    {
        $host = new SetContactPointHost() ;

        $this->assertTrue( $host->set( Oihana::HOME_EMAIL , 'home@example.com' ) ) ;

        $this->assertSame( 'home@example.com' , $host->contactPoint[0]->email       ) ;
        $this->assertSame( ContactType::HOME  , $host->contactPoint[0]->contactType ) ;
    }

    public function testSetFaxNumberCreatesAContactPoint(): void
    {
        $host = new SetContactPointHost() ;

        $this->assertTrue( $host->set( Oihana::DEFAULT_FAX_NUMBER , '0102030406' ) ) ;

        $this->assertSame( '0102030406' , $host->contactPoint[0]->faxNumber ) ;
    }

    public function testSetMobileCreatesAContactPoint(): void
    {
        $host = new SetContactPointHost() ;

        $this->assertTrue( $host->set( Oihana::MOBILE              , '0601020304' ) ) ;
        $this->assertTrue( $host->set( Oihana::MOBILE_PROFESSIONAL , '0605060708' ) ) ;

        $this->assertCount( 2 , $host->contactPoint ) ;
        $this->assertSame( ContactType::MOBILE              , $host->contactPoint[0]->contactType ) ;
        $this->assertSame( ContactType::MOBILE_PROFESSIONAL , $host->contactPoint[1]->contactType ) ;
    }

    public function testSetUpdatesTheExistingContactPointOfTheSameType(): void
    {
        $host = new SetContactPointHost() ;

        $host->set( Oihana::DEFAULT_TELEPHONE , '0102030405'          ) ;
        $host->set( Oihana::DEFAULT_EMAIL     , 'contact@example.com' ) ;

        $this->assertCount( 1 , $host->contactPoint ) ;
        $this->assertSame( '0102030405'          , $host->contactPoint[0]->telephone ) ;
        $this->assertSame( 'contact@example.com' , $host->contactPoint[0]->email     ) ;
    }

    public function testSetRejectsAnEmptyValue(): void
    {
        $host = new SetContactPointHost() ;

        $this->assertFalse( $host->set( Oihana::DEFAULT_TELEPHONE , null ) ) ;
        $this->assertFalse( $host->set( Oihana::DEFAULT_TELEPHONE , ''   ) ) ;
        $this->assertNull( $host->contactPoint ) ;
    }

    public function testSetRejectsAnUnknownPropertyName(): void
    {
        $host = new SetContactPointHost() ;

        $this->assertFalse( $host->set( 'unknownProperty' , '0102030405' ) ) ;
    }

    public function testSetRejectsAnInvalidPhoneNumber(): void
    {
        $host = new SetContactPointHost() ;

        $this->assertFalse( $host->set( Oihana::MOBILE , '.' ) ) ;
        $this->assertNull( $host->contactPoint ) ;
    }

    public function testSetRejectsAnInvalidEmail(): void
    {
        $host = new SetContactPointHost() ;

        $this->assertFalse( $host->set( Oihana::DEFAULT_EMAIL , 'not-an-email' ) ) ;
        $this->assertNull( $host->contactPoint ) ;
    }

    // ---- findContactPointByType

    public function testFindContactPointByTypeReturnsTheMatchingContact(): void
    {
        $host = new SetContactPointHost() ;

        $host->set( Oihana::DEFAULT_TELEPHONE , '0102030405' ) ;
        $host->set( Oihana::MOBILE            , '0601020304' ) ;

        $contact = $host->find( ContactType::MOBILE ) ;

        $this->assertInstanceOf( ContactPoint::class , $contact ) ;
        $this->assertSame( '0601020304' , $contact->telephone ) ;
    }

    public function testFindContactPointByTypeReturnsNullWhenNotFound(): void
    {
        $host = new SetContactPointHost() ;

        $this->assertNull( $host->find( ContactType::MOBILE ) ) ;

        $host->set( Oihana::DEFAULT_TELEPHONE , '0102030405' ) ;

        $this->assertNull( $host->find( ContactType::MOBILE ) ) ;
    }

    // ---- isValidPhoneNumber

    public function testIsValidPhoneNumber(): void
    {
        $host = new SetContactPointHost() ;

        $this->assertTrue ( $host->validPhone( '0601020304' ) ) ;
        $this->assertFalse( $host->validPhone( '.' ) ) ;
        $this->assertFalse( $host->validPhone( ''  ) ) ;
    }
}
