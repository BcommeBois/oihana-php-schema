<?php

namespace tests\org\schema\traits\helpers ;

use org\schema\PropertyValue;
use org\schema\traits\helpers\GetAdditionalPropertyTrait;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Host exposing {@see GetAdditionalPropertyTrait}, mirroring how `PlaceTrait`
 * and `PersonTrait` declare the `additionalProperty` property.
 */
class GetAdditionalPropertyHost
{
    use GetAdditionalPropertyTrait;

    public null|array $additionalProperty = null ;
}

class GetAdditionalPropertyTraitTest extends TestCase
{
    public function testGetAdditionalPropertyValueReturnsNullWhenPropertyIsNotAnArray()
    {
        $host = new GetAdditionalPropertyHost() ;

        $this->assertNull( $host->getAdditionalPropertyValue( 'isDefaultAddress' ) ) ;
    }

    public function testGetAdditionalPropertyValueReturnsNullWhenAbsent()
    {
        $host = new GetAdditionalPropertyHost() ;

        $host->additionalProperty = [ [ 'propertyID' => 'isBillingAddress' , 'value' => true ] ] ;

        $this->assertNull( $host->getAdditionalPropertyValue( 'isDefaultAddress' ) ) ;
    }

    public function testGetAdditionalPropertyValueReadsArrayShapedEntries()
    {
        $host = new GetAdditionalPropertyHost() ;

        $host->additionalProperty =
        [
            [ 'propertyID' => 'isBillingAddress' , 'value' => false ] ,
            [ 'propertyID' => 'isDefaultAddress' , 'value' => true  ] ,
        ] ;

        $this->assertTrue( $host->getAdditionalPropertyValue( 'isDefaultAddress' ) ) ;
    }

    public function testGetAdditionalPropertyValueReadsObjectShapedEntries()
    {
        $host = new GetAdditionalPropertyHost() ;

        $billing = new PropertyValue() ;
        $billing->propertyID = 'isBillingAddress' ;
        $billing->value      = false ;

        $default = new PropertyValue() ;
        $default->propertyID = 'isDefaultAddress' ;
        $default->value      = true ;

        $host->additionalProperty = [ $billing , $default ] ;

        $this->assertTrue( $host->getAdditionalPropertyValue( 'isDefaultAddress' ) ) ;
    }

    public function testGetAdditionalPropertyValueIgnoresEntriesOfNeitherShape()
    {
        $host = new GetAdditionalPropertyHost() ;

        $host->additionalProperty = [ 'not-a-property' , 42 ] ;

        $this->assertNull( $host->getAdditionalPropertyValue( 'isDefaultAddress' ) ) ;
    }

    public function testHasAdditionalPropertyFlagIsFalseWhenAbsent()
    {
        $host = new GetAdditionalPropertyHost() ;

        $this->assertFalse( $host->hasAdditionalPropertyFlag( 'isDefaultAddress' ) ) ;
    }

    #[DataProvider( 'truthyValuesProvider' )]
    public function testHasAdditionalPropertyFlagIsTolerantOfTruthyShapes( mixed $value )
    {
        $host = new GetAdditionalPropertyHost() ;

        $host->additionalProperty = [ [ 'propertyID' => 'isDefaultAddress' , 'value' => $value ] ] ;

        $this->assertTrue( $host->hasAdditionalPropertyFlag( 'isDefaultAddress' ) ) ;
    }

    public static function truthyValuesProvider(): array
    {
        return
        [
            'boolean true' => [ true ] ,
            'string "1"'   => [ '1'  ] ,
            'int 1'        => [ 1    ] ,
        ] ;
    }

    public function testHasAdditionalPropertyFlagIsFalseForFalsyValue()
    {
        $host = new GetAdditionalPropertyHost() ;

        $host->additionalProperty = [ [ 'propertyID' => 'isDefaultAddress' , 'value' => false ] ] ;

        $this->assertFalse( $host->hasAdditionalPropertyFlag( 'isDefaultAddress' ) ) ;
    }
}
