<?php

namespace tests\xyz\oihana\schema\helpers\hydrate ;

use PHPUnit\Framework\TestCase;

use org\schema\constants\Prop;
use org\schema\DefinedTerm;

use xyz\oihana\schema\appointments\CustomerAppointment;
use xyz\oihana\schema\appointments\VisitReport;
use xyz\oihana\schema\thesaurus\ProductCategoryTerm;
use xyz\oihana\schema\thesaurus\ThesaurusTerm;

use function xyz\oihana\schema\helpers\hydrate\termClassOf;

final class TermClassOfTest extends TestCase
{
    /**
     * The short form, and the common case : one class answers for every property, so the
     * property asked for changes nothing.
     */
    public function testAClassNameAnswersForEveryProperty(): void
    {
        $this->assertSame( DefinedTerm::class , termClassOf( DefinedTerm::class , VisitReport::MOOD ) ) ;
        $this->assertSame( DefinedTerm::class , termClassOf( DefinedTerm::class , VisitReport::TAGS ) ) ;
        $this->assertSame( DefinedTerm::class , termClassOf( DefinedTerm::class , 'whatever' ) ) ;
    }

    /**
     * The long form : a key names a property, {@see Prop::DEFAULT} covers what is left.
     */
    public function testAMapAnswersPerPropertyAndFallsBackOnTheDefaultKey(): void
    {
        $map =
        [
            Prop::DEFAULT     => DefinedTerm::class ,
            VisitReport::MOOD => ProductCategoryTerm::class ,
        ];

        $this->assertSame( ProductCategoryTerm::class , termClassOf( $map , VisitReport::MOOD ) ) ;
        $this->assertSame( DefinedTerm::class         , termClassOf( $map , VisitReport::OUTCOME ) ) ;
        $this->assertSame( DefinedTerm::class         , termClassOf( $map , VisitReport::TOPICS ) ) ;
    }

    /**
     * A map that names nothing useful is not an error : what it does not cover is the house
     * term, which is what the short form would have answered anyway.
     */
    public function testWhatAMapDoesNotNameFallsBackOnTheHouseTerm(): void
    {
        $this->assertSame( ThesaurusTerm::class , termClassOf( [] , VisitReport::MOOD ) ) ;
        $this->assertSame( ThesaurusTerm::class , termClassOf( [ VisitReport::MOOD => DefinedTerm::class ] , VisitReport::OUTCOME ) ) ;
    }

    /**
     * 🔑 **A nested branch is not a class**, and is never answered as one. `report` holds a
     * whole entity rather than a term : the hydrator that owns the property reads the branch
     * itself and hands it down, so this helper steps over it rather than returning an array
     * where a class name is expected.
     */
    public function testANestedBranchIsNeverAnsweredAsAClass(): void
    {
        $map =
        [
            Prop::DEFAULT               => DefinedTerm::class ,
            CustomerAppointment::REPORT => [ VisitReport::MOOD => ProductCategoryTerm::class ] ,
        ];

        $this->assertSame( DefinedTerm::class , termClassOf( $map , CustomerAppointment::REPORT ) ) ;

        // And with no default to fall back on, the house term rather than the branch.
        $branchOnly = [ CustomerAppointment::REPORT => [ VisitReport::MOOD => ProductCategoryTerm::class ] ] ;

        $this->assertSame( ThesaurusTerm::class , termClassOf( $branchOnly , CustomerAppointment::REPORT ) ) ;
    }
}
