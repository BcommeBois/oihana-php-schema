<?php

namespace tests\xyz\oihana\schema\people ;

use PHPUnit\Framework\TestCase;

use org\schema\ContactPoint;
use org\schema\Person as SchemaPerson;
use org\schema\PropertyValue;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\constants\PersonAdditionalProperty;
use xyz\oihana\schema\people\Person;

class PersonTest extends TestCase
{
    public function testIsSchemaPerson(): void
    {
        $this->assertInstanceOf( SchemaPerson::class , new Person() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( Oihana::SCHEMA , Person::CONTEXT );
    }

    public function testMagicSetHandlesAdditionalProperties(): void
    {
        $person = new Person() ;

        $person->isQuoteRecipient = '1' ;

        $this->assertCount( 1 , $person->additionalProperty ) ;
        $this->assertInstanceOf( PropertyValue::class , $person->additionalProperty[0] ) ;
        $this->assertSame( PersonAdditionalProperty::IS_QUOTE_RECIPIENT , $person->additionalProperty[0]->propertyID ) ;
        $this->assertTrue( $person->additionalProperty[0]->value ) ;
    }

    public function testMagicSetHandlesContactPointProperties(): void
    {
        $person = new Person() ;

        $person->mobile = '0601020304' ;

        $this->assertIsArray( $person->contactPoint ) ;
        $this->assertInstanceOf( ContactPoint::class , $person->contactPoint[0] ) ;
        $this->assertSame( '0601020304' , $person->contactPoint[0]->telephone ) ;
    }

    public function testSetAdditionalPropertiesRejectsNonStringOrUnknownValues(): void
    {
        $person = new Person() ;

        $this->assertFalse( $person->setAdditionalProperties( PersonAdditionalProperty::IS_QUOTE_RECIPIENT , 1 ) ) ;
        $this->assertFalse( $person->setAdditionalProperties( 'unknownProperty' , '1' ) ) ;
        $this->assertNull( $person->additionalProperty ) ;
    }
}
