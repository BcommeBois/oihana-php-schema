<?php

namespace tests\xyz\oihana\schema\business\documents ;

use PHPUnit\Framework\TestCase;

use org\schema\StructuredValue;

use xyz\oihana\schema\business\documents\ProofOfDelivery;
use xyz\oihana\schema\constants\Oihana;

class ProofOfDeliveryTest extends TestCase
{
    public function testIsStructuredValue(): void
    {
        $this->assertInstanceOf( StructuredValue::class , new ProofOfDelivery() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( Oihana::SCHEMA , ProofOfDelivery::CONTEXT );
    }

    public function testTraitConstants(): void
    {
        $this->assertSame( 'date'            , ProofOfDelivery::DATE             );
        $this->assertSame( 'discrepancyNote' , ProofOfDelivery::DISCREPANCY_NOTE );
        $this->assertSame( 'signatory'       , ProofOfDelivery::SIGNATORY        );

        $this->assertSame( Oihana::DATE , ProofOfDelivery::DATE );
    }

    public function testDefaults(): void
    {
        $proof = new ProofOfDelivery() ;

        $this->assertNull( $proof->date            ?? null );
        $this->assertNull( $proof->discrepancyNote ?? null );
        $this->assertNull( $proof->signatory        ?? null );
    }

    public function testConstructorHydratesScalarProperties(): void
    {
        $proof = new ProofOfDelivery
        ([
            ProofOfDelivery::SIGNATORY        => 'Jane Doe' ,
            ProofOfDelivery::DATE             => '2026-01-20' ,
            ProofOfDelivery::DISCREPANCY_NOTE => '2 units missing' ,
        ]);

        $this->assertSame( 'Jane Doe' , $proof->signatory ) ;
        $this->assertSame( '2026-01-20' , $proof->date ) ;
        $this->assertSame( '2 units missing' , $proof->discrepancyNote ) ;
    }
}
