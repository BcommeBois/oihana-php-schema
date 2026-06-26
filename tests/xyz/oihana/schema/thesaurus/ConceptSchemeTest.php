<?php

namespace tests\xyz\oihana\schema\thesaurus ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\Reflection;

use org\schema\creativeWork\DefinedTermSet;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\thesaurus\Concept;
use xyz\oihana\schema\thesaurus\ConceptScheme;

class ConceptSchemeTest extends TestCase
{
    public function testDefaults(): void
    {
        $scheme = new ConceptScheme();

        $this->assertNull( $scheme->hasTopConcept  ?? null );
        $this->assertNull( $scheme->hasDefinedTerm ?? null );
    }

    public function testIsDefinedTermSet(): void
    {
        $this->assertInstanceOf( DefinedTermSet::class , new ConceptScheme() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( 'https://schema.oihana.xyz' , ConceptScheme::CONTEXT );
    }

    public function testConstants(): void
    {
        $this->assertSame( 'hasTopConcept' , ConceptScheme::HAS_TOP_CONCEPT );
        $this->assertSame( 'hasTopConcept' , Oihana::HAS_TOP_CONCEPT );
    }

    public function testHasTopConceptViaConstructorIsLeftRaw(): void
    {
        $scheme = new ConceptScheme
        ([
            ConceptScheme::HAS_TOP_CONCEPT => [ [ 'name' => 'Wines' , 'termCode' => 'WINE' ] ] ,
        ]);

        $this->assertIsArray( $scheme->hasTopConcept[ 0 ] );
    }

    /**
     * @throws ReflectionException
     */
    public function testHasTopConceptViaReflectionIsHydratedIntoConcepts(): void
    {
        $scheme = new Reflection()->hydrate
        (
            [
                'name'                         => 'Product categories' ,
                ConceptScheme::HAS_TOP_CONCEPT =>
                [
                    [ 'name' => 'Wines'   , 'termCode' => 'WINE'   ] ,
                    [ 'name' => 'Spirits' , 'termCode' => 'SPIRIT' ] ,
                ],
            ],
            ConceptScheme::class
        );

        $this->assertCount( 2 , $scheme->hasTopConcept );
        $this->assertInstanceOf( Concept::class , $scheme->hasTopConcept[ 0 ] );
        $this->assertSame( 'Wines' , $scheme->hasTopConcept[ 0 ]->name );
    }
}
