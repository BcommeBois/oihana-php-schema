<?php

namespace tests\xyz\oihana\schema\thesaurus ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\Reflection;

use org\schema\DefinedTerm;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\thesaurus\Concept;

class ConceptTest extends TestCase
{
    public function testDefaults(): void
    {
        $concept = new Concept();

        $this->assertNull( $concept->broader            ?? null );
        $this->assertNull( $concept->broaderTransitive  ?? null );
        $this->assertNull( $concept->narrower           ?? null );
        $this->assertNull( $concept->narrowerTransitive ?? null );
    }

    public function testIsDefinedTerm(): void
    {
        $this->assertInstanceOf( DefinedTerm::class , new Concept() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( 'https://schema.oihana.xyz' , Concept::CONTEXT );
    }

    public function testRelationNameConstants(): void
    {
        $this->assertSame( 'broader'            , Concept::BROADER );
        $this->assertSame( 'broaderTransitive'  , Concept::BROADER_TRANSITIVE );
        $this->assertSame( 'narrower'           , Concept::NARROWER );
        $this->assertSame( 'narrowerTransitive' , Concept::NARROWER_TRANSITIVE );
    }

    public function testConstantsAggregatedIntoOihana(): void
    {
        $this->assertSame( 'broader'            , Oihana::BROADER );
        $this->assertSame( 'broaderTransitive'  , Oihana::BROADER_TRANSITIVE );
        $this->assertSame( 'narrower'           , Oihana::NARROWER );
        $this->assertSame( 'narrowerTransitive' , Oihana::NARROWER_TRANSITIVE );
    }

    public function testBroaderAsScalarReference(): void
    {
        $concept = new Concept([ Concept::BROADER => 'categories/100' ]);

        $this->assertSame( 'categories/100' , $concept->broader );
    }

    /**
     * A singular relation given as an associative array is left as-is, so the
     * consumer can rebuild it via `new Concept( $array )`.
     *
     * @throws ReflectionException
     */
    public function testBroaderAsAssociativeArrayIsLeftUntouched(): void
    {
        $concept = new Concept([ Concept::BROADER => [ '_key' => '100' , 'name' => 'Wines' ] ]);

        $this->assertIsArray( $concept->broader );
        $this->assertSame( [ '_key' => '100' , 'name' => 'Wines' ] , $concept->broader );
        $this->assertSame( 'Wines' , new Concept( $concept->broader )->name );
    }

    public function testBroaderAsConceptObject(): void
    {
        $concept = new Concept();

        $concept->broader = new Concept([ 'name' => 'Wines' ]);

        $this->assertInstanceOf( Concept::class , $concept->broader );
        $this->assertSame( 'Wines' , $concept->broader->name );
    }

    /**
     * The lightweight constructor performs a raw assignment : a plural relation
     * given as a list of associative arrays is left untouched (not hydrated).
     */
    public function testNarrowerViaConstructorIsLeftRaw(): void
    {
        $concept = new Concept
        ([
            Concept::NARROWER => [ [ 'name' => 'Merlot' , 'termCode' => 'MERLOT' ] ] ,
        ]);

        $this->assertIsArray( $concept->narrower[ 0 ] );
    }

    /**
     * Through the reflection-based hydration path, the `#[HydrateWith]` attribute
     * turns each element of a plural relation into a base Concept instance — even
     * when the property is declared in the HasSkosRelations trait.
     *
     * @throws ReflectionException
     */
    public function testNarrowerViaReflectionIsHydratedIntoConcepts(): void
    {
        $concept = ( new Reflection() )->hydrate
        (
            [
                Concept::NARROWER =>
                [
                    [ 'name' => 'Cabernet Sauvignon' , 'termCode' => 'CAB-SAUV' ] ,
                    [ 'name' => 'Merlot'             , 'termCode' => 'MERLOT'   ] ,
                ],
            ],
            Concept::class
        );

        $this->assertIsArray( $concept->narrower );
        $this->assertCount( 2 , $concept->narrower );
        $this->assertInstanceOf( Concept::class , $concept->narrower[ 0 ] );
        $this->assertSame( 'Cabernet Sauvignon' , $concept->narrower[ 0 ]->name );
        $this->assertSame( 'MERLOT'             , $concept->narrower[ 1 ]->termCode );
    }
}
