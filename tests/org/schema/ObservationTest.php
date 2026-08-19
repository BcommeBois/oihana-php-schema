<?php

namespace tests\org\schema ;

use ReflectionException;

use org\schema\constants\Schema;
use org\schema\Observation;
use org\schema\Place;
use org\schema\Property;
use org\schema\QuantitativeValue;
use org\schema\StatisticalVariable;
use org\schema\Thing;
use org\schema\traits\MeasurementVariableTrait;

use PHPUnit\Framework\TestCase;

class ObservationTest extends TestCase
{
    public function testExtendsQuantitativeValue()
    {
        $observation = new Observation() ;

        $this->assertInstanceOf( QuantitativeValue::class , $observation ) ;
        $this->assertInstanceOf( Thing::class             , $observation ) ;
    }

    /**
     * An Observation *is* a measured figure : it must carry the unit and the value
     * it inherits from QuantitativeValue, not only its own observation terms.
     */
    public function testCarriesTheQuantitativeValueTerms()
    {
        $observation = new Observation
        ([
            Schema::VALUE     => 68_170_228 ,
            Schema::UNIT_TEXT => 'person'   ,
        ]) ;

        $this->assertSame( 68_170_228 , $observation->value    ) ;
        $this->assertSame( 'person'   , $observation->unitText ) ;
    }

    public function testConstructorWithNoArguments()
    {
        $observation = new Observation() ;

        $this->assertObjectHasProperty( Schema::OBSERVATION_ABOUT , $observation ) ;
        $this->assertNull( $observation->observationAbout ?? null , 'The observationAbout property must be null by default' ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testConstructorInitializesProperties()
    {
        $about = new Place([ Schema::NAME => 'France' ]) ;

        $observation = new Observation
        ([
            Schema::OBSERVATION_ABOUT  => $about        ,
            Schema::OBSERVATION_DATE   => '2026'        ,
            Schema::OBSERVATION_PERIOD => 'P1Y'         ,
            Schema::MEASURED_PROPERTY  => 'population'  ,
        ]) ;

        $this->assertSame( $about       , $observation->observationAbout  ) ;
        $this->assertSame( '2026'       , $observation->observationDate   ) ;
        $this->assertSame( 'P1Y'        , $observation->observationPeriod ) ;
        $this->assertSame( 'population' , $observation->measuredProperty  ) ;
    }

    /**
     * A payload read back from a store holds raw rows, not objects. The unions
     * accept `array` so that construction from such a payload is an assignment
     * rather than a TypeError.
     *
     * @throws ReflectionException
     */
    public function testConstructorAcceptsRawArrayValues()
    {
        $observation = new Observation
        ([
            Schema::MARGIN_OF_ERROR    => [ Schema::VALUE => 0.4 , Schema::UNIT_TEXT => '%' ] ,
            Schema::OBSERVATION_ABOUT  => [ Schema::AT_TYPE => 'Place' , Schema::NAME => 'France' ] ,
            Schema::VARIABLE_MEASURED  => [ Schema::AT_TYPE => 'StatisticalVariable' ] ,
            Schema::MEASURED_PROPERTY  => [ Schema::NAME => 'population' ] ,
        ]) ;

        $this->assertIsArray( $observation->marginOfError    ) ;
        $this->assertIsArray( $observation->observationAbout ) ;
        $this->assertIsArray( $observation->variableMeasured ) ;
        $this->assertIsArray( $observation->measuredProperty ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testConstructorAcceptsTypedValues()
    {
        $variable = new StatisticalVariable() ;

        $observation = new Observation
        ([
            Schema::MARGIN_OF_ERROR   => new QuantitativeValue([ Schema::VALUE => 0.4 ]) ,
            Schema::VARIABLE_MEASURED => $variable ,
            Schema::MEASURED_PROPERTY => new Property() ,
        ]) ;

        $this->assertInstanceOf( QuantitativeValue::class , $observation->marginOfError    ) ;
        $this->assertSame      ( $variable                , $observation->variableMeasured ) ;
        $this->assertInstanceOf( Property::class          , $observation->measuredProperty ) ;
    }

    public function testUsesTheMeasurementVariableTrait()
    {
        $this->assertContains
        (
            MeasurementVariableTrait::class ,
            class_uses( Observation::class ) ,
            'Observation must take its measurement terms from the shared trait.'
        ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testJsonSerializeIncludesContextAndType()
    {
        $observation = new Observation([ Schema::OBSERVATION_DATE => '2026' ]) ;

        $data = $observation->jsonSerialize() ;

        $this->assertArrayHasKey( Schema::AT_TYPE    , $data ) ;
        $this->assertArrayHasKey( Schema::AT_CONTEXT , $data ) ;

        $this->assertSame( 'Observation'  , $data[ Schema::AT_TYPE ] ) ;
        $this->assertSame( Thing::CONTEXT , $data[ Schema::AT_CONTEXT ] ) ;
        $this->assertSame( '2026'         , $data[ Schema::OBSERVATION_DATE ] ) ;
    }

    public function testGetSchemaTypeReturnsRootUri()
    {
        $this->assertSame( 'https://schema.org/Observation' , Observation::getSchemaType() ) ;
    }
}
