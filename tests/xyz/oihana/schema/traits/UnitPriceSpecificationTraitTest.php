<?php

namespace tests\xyz\oihana\schema\traits ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use org\iso\Iso8601Format;
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

    /**
     * The default format is the ISO 8601 calendar date — dates written in any
     * other shape are ignored unless the format is passed explicitly.
     *
     * @throws ReflectionException
     */
    public function testTheDefaultFormatIsTheIso8601Date(): void
    {
        $host = new UnitPriceSpecificationHost() ;

        $this->assertSame( 'Y-m-d' , Iso8601Format::DATE ) ;

        $basic = new UnitPriceSpecification([ 'validFrom' => '20250601' ]) ;

        $this->assertNull( $host->getLastUnitPriceSpecification([ $basic ]) ) ;
        $this->assertSame( $basic , $host->getLastUnitPriceSpecification( [ $basic ] , 'validFrom' , Iso8601Format::DATE_BASIC ) ) ;
    }

    /**
     * The compared property is configurable — here the end of the validity
     * window rather than its start.
     *
     * @throws ReflectionException
     */
    public function testComparesOnTheGivenPropertyName(): void
    {
        $host = new UnitPriceSpecificationHost() ;

        $first  = new UnitPriceSpecification([ 'validFrom' => '2025-06-01' , 'validThrough' => '2025-06-30' ]) ;
        $second = new UnitPriceSpecification([ 'validFrom' => '2025-01-01' , 'validThrough' => '2025-12-31' ]) ;

        $this->assertSame( $first  , $host->getLastUnitPriceSpecification([ $first , $second ]) ) ;
        $this->assertSame( $second , $host->getLastUnitPriceSpecification( [ $first , $second ] , 'validThrough' ) ) ;
    }
}
