<?php

namespace tests\xyz\oihana\schema\traits ;

use PHPUnit\Framework\TestCase;

use org\schema\constants\Schema;
use org\schema\PostalAddress;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\traits\SetPostalAddressTrait;

/**
 * Host exposing the {@see SetPostalAddressTrait} with the `address` property
 * expected by the trait, mirroring how {@see \xyz\oihana\schema\organizations\Company} uses it.
 */
class SetPostalAddressHost
{
    use SetPostalAddressTrait ;

    public mixed $address = null ;
}

class SetPostalAddressTraitTest extends TestCase
{
    // ---- normalizePostalAddress

    public function testNormalizePostalAddressSplitsStreetAddressOnSeparator(): void
    {
        $address = new PostalAddress() ;
        $address->streetAddress = '12 rue des Bois;Bâtiment C;BP 42' ;

        $address = SetPostalAddressHost::normalizePostalAddress( $address ) ;

        $this->assertSame( '12 rue des Bois' , $address->streetAddress       ) ;
        $this->assertSame( 'Bâtiment C'      , $address->extendedAddress     ) ;
        $this->assertSame( 'BP 42'           , $address->postOfficeBoxNumber ) ;
    }

    public function testNormalizePostalAddressNullifiesEmptySegments(): void
    {
        $address = new PostalAddress() ;
        $address->streetAddress = '12 rue des Bois;;' ;

        $address = SetPostalAddressHost::normalizePostalAddress( $address ) ;

        $this->assertSame( '12 rue des Bois' , $address->streetAddress       ) ;
        $this->assertNull( $address->extendedAddress     ) ;
        $this->assertNull( $address->postOfficeBoxNumber ) ;
    }

    public function testNormalizePostalAddressKeepsStreetAddressWithoutSeparator(): void
    {
        $address = new PostalAddress() ;
        $address->streetAddress = '12 rue des Bois' ;

        $address = SetPostalAddressHost::normalizePostalAddress( $address ) ;

        $this->assertSame( '12 rue des Bois' , $address->streetAddress ) ;
    }

    public function testNormalizePostalAddressReturnsNonPostalAddressUnchanged(): void
    {
        $this->assertSame( 'foo' , SetPostalAddressHost::normalizePostalAddress( 'foo' ) ) ;
        $this->assertNull( SetPostalAddressHost::normalizePostalAddress( null ) ) ;
    }

    // ---- setPostalAddressProperty

    public function testSetPostalAddressPropertyCreatesTheAddress(): void
    {
        $host = new SetPostalAddressHost() ;

        $this->assertTrue( $host->setPostalAddressProperty( Schema::STREET_ADDRESS , '12 rue des Bois' ) ) ;

        $this->assertInstanceOf( PostalAddress::class , $host->address ) ;
        $this->assertSame( '12 rue des Bois' , $host->address->streetAddress ) ;
    }

    public function testSetPostalAddressPropertyClearsAnExistingProperty(): void
    {
        $host = new SetPostalAddressHost() ;
        $host->setPostalAddressProperty( Schema::STREET_ADDRESS , '12 rue des Bois' ) ;

        $this->assertTrue( $host->setPostalAddressProperty( Schema::STREET_ADDRESS , null ) ) ;
        $this->assertNull( $host->address->streetAddress ) ;
    }

    public function testSetPostalAddressPropertyIgnoresEmptyValueWithoutAddress(): void
    {
        $host = new SetPostalAddressHost() ;

        $this->assertFalse( $host->setPostalAddressProperty( Schema::STREET_ADDRESS , null ) ) ;
        $this->assertNull( $host->address ) ;
    }

    public function testSetPostalAddressPropertyNullifiesAnInvalidEmail(): void
    {
        $host = new SetPostalAddressHost() ;

        $this->assertTrue( $host->setPostalAddressProperty( Schema::EMAIL , 'not-an-email' ) ) ;

        $this->assertInstanceOf( PostalAddress::class , $host->address ) ;
        $this->assertNull( $host->address->email ) ;
    }

    public function testSetPostalAddressPropertyAcceptsAValidEmail(): void
    {
        $host = new SetPostalAddressHost() ;

        $this->assertTrue( $host->setPostalAddressProperty( Schema::EMAIL , 'contact@example.com' ) ) ;
        $this->assertSame( 'contact@example.com' , $host->address->email ) ;
    }

    // ---- setPostalAddressProperties

    public function testSetPostalAddressPropertiesRedirectsCustomProperties(): void
    {
        $host = new SetPostalAddressHost() ;

        $this->assertTrue( $host->setPostalAddressProperties( Oihana::ADDRESS_EMAIL          , 'contact@example.com' ) ) ;
        $this->assertTrue( $host->setPostalAddressProperties( Oihana::ADDRESS_NAME           , 'Head office'  ) ) ;
        $this->assertTrue( $host->setPostalAddressProperties( Oihana::ADDRESS_ALTERNATE_NAME , 'HQ'           ) ) ;
        $this->assertTrue( $host->setPostalAddressProperties( Oihana::ADDRESS_TELEPHONE      , '0102030405'   ) ) ;
        $this->assertTrue( $host->setPostalAddressProperties( Oihana::ADDRESS_FAX_NUMBER     , '0102030406'   ) ) ;
        $this->assertTrue( $host->setPostalAddressProperties( Oihana::ADDRESS_AREA_SERVED    , 'FR'           ) ) ;

        $this->assertSame( 'contact@example.com' , $host->address->email         ) ;
        $this->assertSame( 'Head office'         , $host->address->name          ) ;
        $this->assertSame( 'HQ'                  , $host->address->alternateName ) ;
        $this->assertSame( '0102030405'          , $host->address->telephone     ) ;
        $this->assertSame( '0102030406'          , $host->address->faxNumber     ) ;
        $this->assertSame( 'FR'                  , $host->address->areaServed    ) ;
    }

    public function testSetPostalAddressPropertiesHandlesDirectProperties(): void
    {
        $host = new SetPostalAddressHost() ;

        $this->assertTrue( $host->setPostalAddressProperties( Schema::STREET_ADDRESS         , '12 rue des Bois' ) ) ;
        $this->assertTrue( $host->setPostalAddressProperties( Schema::ADDRESS_COUNTRY        , 'FR'              ) ) ;
        $this->assertTrue( $host->setPostalAddressProperties( Schema::ADDRESS_LOCALITY       , 'Bayonne'         ) ) ;
        $this->assertTrue( $host->setPostalAddressProperties( Schema::EXTENDED_ADDRESS       , 'Bâtiment C'      ) ) ;
        $this->assertTrue( $host->setPostalAddressProperties( Schema::POSTAL_CODE            , '64100'           ) ) ;
        $this->assertTrue( $host->setPostalAddressProperties( Schema::POST_OFFICE_BOX_NUMBER , 'BP 42'           ) ) ;

        $this->assertSame( '12 rue des Bois' , $host->address->streetAddress   ) ;
        $this->assertSame( 'FR'              , $host->address->addressCountry  ) ;
        $this->assertSame( 'Bayonne'         , $host->address->addressLocality ) ;
        $this->assertSame( '64100'           , $host->address->postalCode      ) ;
    }

    public function testSetPostalAddressPropertiesRejectsUnknownProperty(): void
    {
        $host = new SetPostalAddressHost() ;

        $this->assertFalse( $host->setPostalAddressProperties( 'unknownProperty' , 'value' ) ) ;
        $this->assertNull( $host->address ) ;
    }
}
