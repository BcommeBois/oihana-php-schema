<?php

namespace tests\org\schema\traits ;

use ReflectionClass;

use org\schema\constants\Schema;
use org\schema\Observation;
use org\schema\StatisticalVariable;
use org\schema\traits\MeasurementTechniqueTrait;
use org\schema\traits\MeasurementVariableTrait;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Guards the pair that shares the measurement vocabulary.
 *
 * An Observation and the StatisticalVariable it observes name the same five
 * terms, and named them twice — the Observation had none of them at all. A term
 * declared once cannot drift between the two, which is the whole reason the
 * trait exists rather than a second copy of the block.
 */
class MeasurementVariableTraitTest extends TestCase
{
    /**
     * The three terms the trait declares of its own — the technique and its
     * method come from {@see MeasurementTechniqueTrait}, and are guarded there.
     *
     * @return array<string, array{0: string}>
     */
    public static function provideOwnProperties(): array
    {
        return
        [
            Schema::MEASURED_PROPERTY       => [ Schema::MEASURED_PROPERTY       ] ,
            Schema::MEASUREMENT_DENOMINATOR => [ Schema::MEASUREMENT_DENOMINATOR ] ,
            Schema::MEASUREMENT_QUALIFIER   => [ Schema::MEASUREMENT_QUALIFIER   ] ,
        ] ;
    }

    /**
     * The five terms a user of the trait ends up carrying, composition included.
     *
     * @return array<string, array{0: string}>
     */
    public static function provideProperties(): array
    {
        return self::provideOwnProperties() +
        [
            Schema::MEASUREMENT_METHOD    => [ Schema::MEASUREMENT_METHOD    ] ,
            Schema::MEASUREMENT_TECHNIQUE => [ Schema::MEASUREMENT_TECHNIQUE ] ,
        ] ;
    }

    #[DataProvider( 'provideOwnProperties' )]
    public function testTheTraitDeclaresTheProperty( string $property ): void
    {
        $this->assertTrue
        (
            ( new ReflectionClass( MeasurementVariableTrait::class ) )->hasProperty( $property ) ,
            sprintf( 'MeasurementVariableTrait must declare %s.' , $property )
        ) ;
    }

    public function testTheTraitComposesTheTechniquePair(): void
    {
        $this->assertContains
        (
            MeasurementTechniqueTrait::class ,
            ( new ReflectionClass( MeasurementVariableTrait::class ) )->getTraitNames() ,
            'The technique and its method are declared once, for every type that carries a measure.'
        ) ;
    }

    #[DataProvider( 'provideProperties' )]
    public function testBothUsersCarryTheProperty( string $property ): void
    {
        $this->assertObjectHasProperty( $property , new Observation()          ) ;
        $this->assertObjectHasProperty( $property , new StatisticalVariable()  ) ;
    }

    #[DataProvider( 'provideProperties' )]
    public function testTheTermsAcceptRawArrayValues( string $property ): void
    {
        $payload = [ $property => [ Schema::NAME => 'anything' ] ] ;

        $this->assertIsArray( ( new Observation        ( $payload ) )->{ $property } ) ;
        $this->assertIsArray( ( new StatisticalVariable( $payload ) )->{ $property } ) ;
    }

    public function testBothUsersComposeTheTrait(): void
    {
        $this->assertContains( MeasurementVariableTrait::class , class_uses( Observation::class          ) ) ;
        $this->assertContains( MeasurementVariableTrait::class , class_uses( StatisticalVariable::class  ) ) ;
    }

    /**
     * The terms are declared once, so they must also be *named* once : a second
     * copy of the constants is the drift the trait was meant to end.
     */
    public function testTheTermsAreNamedByTheirOwnConstantsTrait(): void
    {
        $trait = 'org\\schema\\constants\\traits\\MeasurementVariable' ;

        $this->assertTrue( trait_exists( $trait ) , sprintf( '%s must exist.' , $trait ) ) ;

        $this->assertSame
        (
            array_keys( self::provideOwnProperties() ) ,
            array_values( ( new ReflectionClass( $trait ) )->getConstants() )
        ) ;
    }
}
