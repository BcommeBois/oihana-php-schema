<?php

namespace tests\xyz\oihana\schema\places ;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;

use org\schema\constants\Schema;
use org\schema\PostalAddress;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\places\CustomPostalAddress;

class CustomPostalAddressTest extends TestCase
{
    public function testIsPostalAddress(): void
    {
        $this->assertInstanceOf( PostalAddress::class , new CustomPostalAddress() );
    }

    /**
     * The whole point of the class : a type name of its own, derived from the
     * redefined context rather than hard-coded anywhere.
     */
    public function testSchemaTypeNamesTheHouseType(): void
    {
        $this->assertSame( Oihana::SCHEMA , CustomPostalAddress::CONTEXT );
        $this->assertSame( 'https://schema.oihana.xyz/CustomPostalAddress' , CustomPostalAddress::getSchemaType() );
    }

    /**
     * It does not shadow the type of the address it specializes.
     */
    public function testSchemaTypeDiffersFromPostalAddress(): void
    {
        $this->assertSame( 'https://schema.org/PostalAddress' , PostalAddress::getSchemaType() );
        $this->assertNotSame( PostalAddress::getSchemaType()  , CustomPostalAddress::getSchemaType() );
    }

    /**
     * It exists to name a type, not to carry data : nothing is declared beyond
     * the context constant.
     */
    public function testDeclaresNoPropertyOfItsOwn(): void
    {
        $reflection = new ReflectionClass( CustomPostalAddress::class );

        $own = array_filter
        (
            $reflection->getProperties() ,
            fn( $property ) => $property->getDeclaringClass()->getName() === CustomPostalAddress::class
        );

        $this->assertSame( [] , array_values( $own ) );
    }

    /**
     * The address fields it inherits still behave as they do on a plain
     * {@see PostalAddress} — it is a naming device, not a downgrade.
     * @throws ReflectionException
     */
    public function testStillCarriesTheInheritedAddressFields(): void
    {
        $address = new CustomPostalAddress
        ([
            Schema::STREET_ADDRESS   => 'Chemin du bas, portail vert' ,
            Schema::ADDRESS_LOCALITY => 'Bayonne' ,
        ]);

        $this->assertSame( 'Chemin du bas, portail vert' , $address->streetAddress   );
        $this->assertSame( 'Bayonne'                     , $address->addressLocality );
    }
}
