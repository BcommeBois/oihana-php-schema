<?php

namespace tests\xyz\oihana\schema\business\documents ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\Reflection;

use org\schema\ParcelDelivery;

use xyz\oihana\schema\business\documents\BusinessDocument;
use xyz\oihana\schema\business\documents\DeliveryLine;
use xyz\oihana\schema\business\documents\DeliveryNote;
use xyz\oihana\schema\business\documents\ProofOfDelivery;
use xyz\oihana\schema\business\documents\PurchaseOrder;
use xyz\oihana\schema\constants\Oihana;

class DeliveryNoteTest extends TestCase
{
    public function testIsBusinessDocument(): void
    {
        $this->assertInstanceOf( BusinessDocument::class , new DeliveryNote() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( Oihana::SCHEMA , DeliveryNote::CONTEXT );
    }

    public function testTraitConstants(): void
    {
        $this->assertSame( 'lines'           , DeliveryNote::LINES             );
        $this->assertSame( 'orderDelivery'   , DeliveryNote::ORDER_DELIVERY    );
        $this->assertSame( 'proofOfDelivery' , DeliveryNote::PROOF_OF_DELIVERY );
        $this->assertSame( 'referencesOrder' , DeliveryNote::REFERENCES_ORDER  );

        $this->assertSame( Oihana::ORDER_DELIVERY , DeliveryNote::ORDER_DELIVERY );
    }

    public function testDefaults(): void
    {
        $note = new DeliveryNote() ;

        $this->assertNull( $note->lines           ?? null );
        $this->assertNull( $note->orderDelivery   ?? null );
        $this->assertNull( $note->proofOfDelivery ?? null );
    }

    /**
     * @throws ReflectionException
     */
    public function testReflectionHydratesOrderDelivery(): void
    {
        $note = new Reflection()->hydrate
        (
            [
                DeliveryNote::ORDER_DELIVERY => [ 'trackingNumber' => 'TRACK-001' ] ,
            ],
            DeliveryNote::class
        );

        $this->assertInstanceOf( ParcelDelivery::class , $note->orderDelivery ) ;
        $this->assertSame( 'TRACK-001' , $note->orderDelivery->trackingNumber ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testReflectionHydratesLinesAndProofOfDelivery(): void
    {
        $note = new Reflection()->hydrate
        (
            [
                DeliveryNote::LINES =>
                [
                    [ DeliveryLine::POSITION => 1 , DeliveryLine::ORDERED_QUANTITY => 100 , DeliveryLine::DELIVERED_QUANTITY => 80 ] ,
                ] ,
                DeliveryNote::PROOF_OF_DELIVERY => [ ProofOfDelivery::SIGNATORY => 'Jane Doe' ] ,
            ],
            DeliveryNote::class
        );

        $this->assertInstanceOf( DeliveryLine::class , $note->lines[ 0 ] ) ;
        $this->assertSame( 80 , $note->lines[ 0 ]->deliveredQuantity ) ;
        $this->assertInstanceOf( ProofOfDelivery::class , $note->proofOfDelivery ) ;
        $this->assertSame( 'Jane Doe' , $note->proofOfDelivery->signatory ) ;
    }

    public function testInheritsBusinessDocumentProperties(): void
    {
        $note = new DeliveryNote([ BusinessDocument::CURRENCY => 'EUR' ]) ;

        $this->assertSame( 'EUR' , $note->currency ) ;
    }

    /**
     * A round trip loads whatever is ready for a customer that day, and what is
     * ready rarely belongs to a single order.
     * @throws ReflectionException
     */
    public function testReferencesSeveralOrders(): void
    {
        $note = ( new Reflection() )->hydrate
        ([
            DeliveryNote::REFERENCES_ORDER =>
            [
                [ 'identifier' => '1150243' ] ,
                [ 'identifier' => '1151368' ] ,
            ],
        ], DeliveryNote::class );

        $this->assertCount( 2 , $note->referencesOrder ) ;
        $this->assertInstanceOf( PurchaseOrder::class , $note->referencesOrder[ 0 ] ) ;
        $this->assertSame( '1151368' , $note->referencesOrder[ 1 ]->identifier ) ;
    }
}
