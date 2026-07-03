<?php

namespace tests\xyz\oihana\schema\traits ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use org\schema\UnitPriceSpecification;

use xyz\oihana\schema\traits\UnitPriceSpecificationTrait;

/**
 * Host exposing the {@see UnitPriceSpecificationTrait}.
 */
class UnitPriceSpecificationHost
{
    use UnitPriceSpecificationTrait ;
}

class UnitPriceSpecificationTraitTest extends TestCase
{
    /**
     * @throws ReflectionException
     */
    public function testReturnsTheLatestSpecification(): void
    {
        $host = new UnitPriceSpecificationHost() ;

        $old    = new UnitPriceSpecification([ 'validFrom' => '2024-01-01' ]) ;
        $latest = new UnitPriceSpecification([ 'validFrom' => '2025-06-01' ]) ;
        $middle = new UnitPriceSpecification([ 'validFrom' => '2025-01-01' ]) ;

        $this->assertSame( $latest , $host->getLastUnitPriceSpecification([ $old , $latest , $middle ]) ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testSkipsEntriesWithoutAValidDate(): void
    {
        $host = new UnitPriceSpecificationHost() ;

        $valid   = new UnitPriceSpecification([ 'validFrom' => '2025-01-01' ]) ;
        $noDate  = new UnitPriceSpecification() ;
        $badDate = new UnitPriceSpecification([ 'validFrom' => 'not-a-date' ]) ;

        $this->assertSame( $valid , $host->getLastUnitPriceSpecification([ $noDate , $valid , $badDate ]) ) ;
    }

    public function testSkipsEntriesThatAreNotUnitPriceSpecifications(): void
    {
        $host = new UnitPriceSpecificationHost() ;

        $this->assertNull( $host->getLastUnitPriceSpecification([ 'foo' , 42 , null ]) ) ;
    }

    public function testReturnsNullWithANullOrEmptyInput(): void
    {
        $host = new UnitPriceSpecificationHost() ;

        $this->assertNull( $host->getLastUnitPriceSpecification( null ) ) ;
        $this->assertNull( $host->getLastUnitPriceSpecification( [] ) ) ;
    }
}
