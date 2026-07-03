<?php

namespace tests\xyz\oihana\schema\products ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use org\schema\UnitPriceSpecification;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\products\ExtraPriceSpecification;

class ExtraPriceSpecificationTest extends TestCase
{
    public function testIsUnitPriceSpecification(): void
    {
        $this->assertInstanceOf( UnitPriceSpecification::class , new ExtraPriceSpecification() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( Oihana::SCHEMA , ExtraPriceSpecification::CONTEXT );
    }

    /**
     * @throws ReflectionException
     */
    public function testToUnitPriceSpecificationCopiesThePublicProperties(): void
    {
        $extra = new ExtraPriceSpecification
        ([
            'price'     => 42 ,
            'validFrom' => '2026-01-01' ,
            'extras'    => [ 'segmentation' => 'pro' ]
        ]) ;

        $specification = $extra->toUnitPriceSpecification() ;

        $this->assertInstanceOf( UnitPriceSpecification::class , $specification ) ;
        $this->assertNotInstanceOf( ExtraPriceSpecification::class , $specification ) ;

        $this->assertSame( 42           , $specification->price     ) ;
        $this->assertSame( '2026-01-01' , $specification->validFrom ) ;

        $this->assertObjectNotHasProperty( 'extras' , $specification ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testToUnitPriceSpecificationInjectsTheGivenPrice(): void
    {
        $extra = new ExtraPriceSpecification([ 'price' => 42 ]) ;

        $specification = $extra->toUnitPriceSpecification( 55 ) ;

        $this->assertSame( 55 , $specification->price ) ;
    }
}
