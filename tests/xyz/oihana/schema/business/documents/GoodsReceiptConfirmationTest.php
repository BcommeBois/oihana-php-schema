<?php

namespace tests\xyz\oihana\schema\business\documents ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\Reflection;

use xyz\oihana\schema\business\documents\BusinessDocument;
use xyz\oihana\schema\business\documents\DeliveryNote;
use xyz\oihana\schema\business\documents\GoodsReceiptConfirmation;
use xyz\oihana\schema\business\documents\GoodsReceiptLine;
use xyz\oihana\schema\constants\Oihana;

class GoodsReceiptConfirmationTest extends TestCase
{
    public function testIsBusinessDocument(): void
    {
        $this->assertInstanceOf( BusinessDocument::class , new GoodsReceiptConfirmation() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( Oihana::SCHEMA , GoodsReceiptConfirmation::CONTEXT );
    }

    public function testTraitConstants(): void
    {
        $this->assertSame( 'lines'                  , GoodsReceiptConfirmation::LINES                    );
        $this->assertSame( 'referencesDeliveryNote' , GoodsReceiptConfirmation::REFERENCES_DELIVERY_NOTE );

        $this->assertSame( Oihana::LINES , GoodsReceiptConfirmation::LINES );
    }

    public function testDefaults(): void
    {
        $confirmation = new GoodsReceiptConfirmation() ;

        $this->assertNull( $confirmation->lines                  ?? null );
        $this->assertNull( $confirmation->referencesDeliveryNote ?? null );
    }

    /**
     * @throws ReflectionException
     */
    public function testReflectionHydratesLinesAndReferences(): void
    {
        $confirmation = new Reflection()->hydrate
        (
            [
                GoodsReceiptConfirmation::REFERENCES_DELIVERY_NOTE => [ [ BusinessDocument::CURRENCY => 'EUR' ] ] ,
                GoodsReceiptConfirmation::LINES =>
                [
                    [ GoodsReceiptLine::POSITION => 1 , GoodsReceiptLine::EXPECTED_QUANTITY => 100 , GoodsReceiptLine::RECEIVED_QUANTITY => 98 ] ,
                ] ,
            ],
            GoodsReceiptConfirmation::class
        );

        $this->assertInstanceOf( DeliveryNote::class , $confirmation->referencesDeliveryNote[ 0 ] ) ;
        $this->assertInstanceOf( GoodsReceiptLine::class , $confirmation->lines[ 0 ] ) ;
        $this->assertSame( 98 , $confirmation->lines[ 0 ]->receivedQuantity ) ;
    }

    public function testInheritsBusinessDocumentProperties(): void
    {
        $confirmation = new GoodsReceiptConfirmation([ BusinessDocument::CURRENCY => 'EUR' ]) ;

        $this->assertSame( 'EUR' , $confirmation->currency ) ;
    }
}
