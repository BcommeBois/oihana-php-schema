<?php

namespace tests\xyz\oihana\schema\thesaurus ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use org\schema\DefinedTerm;

use xyz\oihana\schema\thesaurus\ThesaurusTerm;
use xyz\oihana\schema\thesaurus\traits\HasColor;

class ThesaurusTermTest extends TestCase
{
    public function testDefaults(): void
    {
        $term = new ThesaurusTerm();

        $this->assertNull( $term->color            ?? null );
        $this->assertNull( $term->termCode         ?? null );
        $this->assertNull( $term->inDefinedTermSet ?? null );
    }

    public function testColorComesFromHasColorTrait(): void
    {
        $this->assertContains( HasColor::class , class_uses( ThesaurusTerm::class ) );
    }

    public function testIsDefinedTerm(): void
    {
        $this->assertInstanceOf( DefinedTerm::class , new ThesaurusTerm() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( 'https://schema.oihana.xyz' , ThesaurusTerm::CONTEXT );
    }

    public function testPropertyNameConstant(): void
    {
        $this->assertSame( 'color' , ThesaurusTerm::COLOR );
    }

    /**
     * @throws ReflectionException
     */
    public function testHydration(): void
    {
        $term = new ThesaurusTerm
        ([
            'name'               => 'Cabernet Sauvignon' ,
            'termCode'           => 'CAB-SAUV' ,
            ThesaurusTerm::COLOR => '#7B1E3A' ,
        ]);

        $this->assertSame( 'Cabernet Sauvignon' , $term->name );
        $this->assertSame( 'CAB-SAUV'           , $term->termCode );
        $this->assertSame( '#7B1E3A'            , $term->color );
    }

    public function testColorAssignment(): void
    {
        $term = new ThesaurusTerm();

        $term->color = '#22C55E' ;

        $this->assertSame( '#22C55E' , $term->color );
    }
}
