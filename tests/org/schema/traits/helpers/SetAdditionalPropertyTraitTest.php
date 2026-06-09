<?php

namespace tests\org\schema\traits\helpers ;

use org\schema\PropertyValue;
use org\schema\traits\helpers\SetAdditionalPropertyTrait;

use PHPUnit\Framework\TestCase;

/**
 * Host exposing the protected {@see SetAdditionalPropertyTrait::setAdditionalProperty()}
 * through a public wrapper, mirroring how `PlaceTrait` declares the property.
 */
class SetAdditionalPropertyHost
{
    use SetAdditionalPropertyTrait;

    public null|array|PropertyValue $additionalProperty = null ;

    public function add( array $init ): void
    {
        $this->setAdditionalProperty( $init ) ;
    }
}

class SetAdditionalPropertyTraitTest extends TestCase
{
    public function testInitializesArrayWhenNotAlreadyAnArray()
    {
        $host = new SetAdditionalPropertyHost() ;

        $this->assertNull( $host->additionalProperty ) ;

        $host->add( [ 'name' => 'color' , 'value' => 'red' ] ) ;

        $this->assertIsArray( $host->additionalProperty ) ;
        $this->assertCount( 1 , $host->additionalProperty ) ;
        $this->assertInstanceOf( PropertyValue::class , $host->additionalProperty[ 0 ] ) ;
    }

    public function testAppendsToExistingArray()
    {
        $host = new SetAdditionalPropertyHost() ;

        $host->add( [ 'name' => 'a' ] ) ;
        $host->add( [ 'name' => 'b' ] ) ;

        $this->assertCount( 2 , $host->additionalProperty ) ;
        $this->assertContainsOnlyInstancesOf( PropertyValue::class , $host->additionalProperty ) ;
    }
}
