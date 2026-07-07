<?php

namespace tests\xyz\oihana\schema\business\documents ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\Reflection;

use org\schema\MonetaryAmount;

use xyz\oihana\schema\business\documents\BusinessDocument;
use xyz\oihana\schema\business\documents\Invoice;
use xyz\oihana\schema\business\documents\RemittanceAdvice;
use xyz\oihana\schema\constants\Oihana;

class RemittanceAdviceTest extends TestCase
{
    public function testIsBusinessDocument(): void
    {
        $this->assertInstanceOf( BusinessDocument::class , new RemittanceAdvice() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( Oihana::SCHEMA , RemittanceAdvice::CONTEXT );
    }

    public function testTraitConstants(): void
    {
        $this->assertSame( 'amountRemitted'    , RemittanceAdvice::AMOUNT_REMITTED   );
        $this->assertSame( 'referencesInvoice' , RemittanceAdvice::REFERENCES_INVOICE );

        $this->assertSame( Oihana::AMOUNT_REMITTED , RemittanceAdvice::AMOUNT_REMITTED );
    }

    public function testDefaults(): void
    {
        $advice = new RemittanceAdvice() ;

        $this->assertNull( $advice->amountRemitted    ?? null );
        $this->assertNull( $advice->referencesInvoice ?? null );
    }

    /**
     * @throws ReflectionException
     */
    public function testReflectionHydratesNestedValueObjects(): void
    {
        $advice = new Reflection()->hydrate
        (
            [
                RemittanceAdvice::AMOUNT_REMITTED => [ 'value' => 240 , 'currency' => 'EUR' ] ,
                RemittanceAdvice::REFERENCES_INVOICE =>
                [
                    [ BusinessDocument::CURRENCY => 'EUR' ] ,
                    [ BusinessDocument::CURRENCY => 'USD' ] ,
                ] ,
            ],
            RemittanceAdvice::class
        );

        $this->assertInstanceOf( MonetaryAmount::class , $advice->amountRemitted ) ;
        $this->assertSame( 240 , $advice->amountRemitted->value ) ;
        $this->assertCount( 2 , $advice->referencesInvoice ) ;
        $this->assertInstanceOf( Invoice::class , $advice->referencesInvoice[ 0 ] ) ;
    }

    public function testInheritsBusinessDocumentProperties(): void
    {
        $advice = new RemittanceAdvice([ BusinessDocument::CURRENCY => 'EUR' ]) ;

        $this->assertSame( 'EUR' , $advice->currency ) ;
    }
}
