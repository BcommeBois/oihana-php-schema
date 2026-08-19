<?php

namespace tests\xyz\oihana\schema\statistics ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\Reflection;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\organizations\Company;
use xyz\oihana\schema\statistics\CompanyStatistics;
use xyz\oihana\schema\statistics\ObservationSeries;
use xyz\oihana\schema\statistics\Statistics;

class CompanyStatisticsTest extends TestCase
{
    public function testIsAStatisticsRecord(): void
    {
        $this->assertInstanceOf( Statistics::class , new CompanyStatistics() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( Oihana::SCHEMA , CompanyStatistics::CONTEXT );
    }

    /**
     * @throws ReflectionException
     */
    public function testReflectionNamesTheCompany(): void
    {
        $statistics = new Reflection()->hydrate
        (
            [ CompanyStatistics::ABOUT => [ 'id' => '501' , 'name' => 'Timber & Panels' ] ],
            CompanyStatistics::class
        );

        $this->assertInstanceOf( Company::class , $statistics->about );
        $this->assertSame( '501' , $statistics->about->id );
    }

    /**
     * A subsidiary adds no term of its own, so naming the subject a company keeps
     * everything a stored row carried — including what kind of company it is.
     *
     * @throws ReflectionException
     */
    public function testASubsidiaryRowKeepsItsKind(): void
    {
        $statistics = new Reflection()->hydrate
        (
            [
                CompanyStatistics::ABOUT =>
                [
                    'id'             => '501' ,
                    'name'           => 'Timber & Panels' ,
                    'additionalType' => 'https://schema.oihana.xyz/Subsidiary' ,
                ],
            ],
            CompanyStatistics::class
        );

        $this->assertInstanceOf( Company::class , $statistics->about );
        $this->assertSame( 'https://schema.oihana.xyz/Subsidiary' , $statistics->about->additionalType );
    }

    /**
     * The subject is the perimeter : assignedCompany would repeat it, so it stays
     * out of the served document.
     */
    public function testTheSubjectIsThePerimeter(): void
    {
        $statistics = new CompanyStatistics
        ([
            CompanyStatistics::ABOUT   => '501' ,
            CompanyStatistics::YEAR    => 2025 ,
            CompanyStatistics::REVENUE => new ObservationSeries([ Oihana::VALUE => 24000000 ]) ,
        ]);

        $document = json_decode( json_encode( $statistics ) , true );

        $this->assertSame( 'CompanyStatistics' , $document[ '@type' ] );
        $this->assertSame( '501' , $document[ CompanyStatistics::ABOUT ] );
        $this->assertArrayNotHasKey( CompanyStatistics::ASSIGNED_COMPANY , $document );
    }

    public function testItCarriesTheTradingMeasures(): void
    {
        $statistics = new CompanyStatistics() ;

        $this->assertNull( $statistics->revenue     ?? null );
        $this->assertNull( $statistics->grossMargin ?? null );
        $this->assertNull( $statistics->weight      ?? null );
    }
}
