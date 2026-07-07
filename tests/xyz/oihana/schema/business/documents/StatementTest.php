<?php

namespace tests\xyz\oihana\schema\business\documents ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\Reflection;

use org\schema\MonetaryAmount;

use xyz\oihana\schema\business\documents\AgingSummary;
use xyz\oihana\schema\business\documents\BusinessDocument;
use xyz\oihana\schema\business\documents\Statement;
use xyz\oihana\schema\business\documents\StatementEntry;
use xyz\oihana\schema\constants\Oihana;

class StatementTest extends TestCase
{
    public function testIsBusinessDocument(): void
    {
        $this->assertInstanceOf( BusinessDocument::class , new Statement() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( Oihana::SCHEMA , Statement::CONTEXT );
    }

    public function testTraitConstants(): void
    {
        $this->assertSame( 'agingSummary'   , Statement::AGING_SUMMARY   );
        $this->assertSame( 'billingPeriod'  , Statement::BILLING_PERIOD  );
        $this->assertSame( 'closingBalance' , Statement::CLOSING_BALANCE );
        $this->assertSame( 'entries'        , Statement::ENTRIES         );
        $this->assertSame( 'openingBalance' , Statement::OPENING_BALANCE );
        $this->assertSame( 'totalCredit'    , Statement::TOTAL_CREDIT    );
        $this->assertSame( 'totalDebit'     , Statement::TOTAL_DEBIT     );

        $this->assertSame( Oihana::BILLING_PERIOD , Statement::BILLING_PERIOD );
    }

    public function testDefaults(): void
    {
        $statement = new Statement() ;

        $this->assertNull( $statement->agingSummary   ?? null );
        $this->assertNull( $statement->billingPeriod  ?? null );
        $this->assertNull( $statement->closingBalance ?? null );
        $this->assertNull( $statement->entries        ?? null );
        $this->assertNull( $statement->openingBalance ?? null );
        $this->assertNull( $statement->totalCredit    ?? null );
        $this->assertNull( $statement->totalDebit     ?? null );
    }

    /**
     * @throws ReflectionException
     */
    public function testReflectionHydratesNestedValueObjects(): void
    {
        $statement = new Reflection()->hydrate
        (
            [
                Statement::OPENING_BALANCE => [ 'value' => 0 , 'currency' => 'EUR' ] ,
                Statement::CLOSING_BALANCE => [ 'value' => 100 , 'currency' => 'EUR' ] ,
                Statement::TOTAL_DEBIT     => [ 'value' => 100 , 'currency' => 'EUR' ] ,
                Statement::AGING_SUMMARY   =>
                [
                    AgingSummary::CURRENT => [ 'value' => 80 , 'currency' => 'EUR' ] ,
                    AgingSummary::OVER_90 => [ 'value' => 20 , 'currency' => 'EUR' ] ,
                ] ,
                Statement::ENTRIES         =>
                [
                    [ StatementEntry::DATE => '2026-01-15' , StatementEntry::AMOUNT => [ 'value' => 100 , 'currency' => 'EUR' ] ] ,
                ] ,
            ],
            Statement::class
        );

        $this->assertInstanceOf( MonetaryAmount::class , $statement->openingBalance ) ;
        $this->assertInstanceOf( MonetaryAmount::class , $statement->closingBalance ) ;
        $this->assertInstanceOf( MonetaryAmount::class , $statement->totalDebit ) ;
        $this->assertInstanceOf( AgingSummary::class , $statement->agingSummary ) ;
        $this->assertInstanceOf( MonetaryAmount::class , $statement->agingSummary->current ) ;
        $this->assertInstanceOf( StatementEntry::class , $statement->entries[ 0 ] ) ;
        $this->assertInstanceOf( MonetaryAmount::class , $statement->entries[ 0 ]->amount ) ;
    }

    public function testInheritsBusinessDocumentProperties(): void
    {
        $statement = new Statement([ BusinessDocument::CURRENCY => 'EUR' ]) ;

        $this->assertSame( 'EUR' , $statement->currency ) ;
    }
}
