<?php

namespace tests\org\schema ;

use ReflectionException;

use org\schema\constants\Schema;
use org\schema\ConstraintNode;
use org\schema\DefinedTerm;
use org\schema\Intangible;
use org\schema\Property;
use org\schema\StatisticalVariable;
use org\schema\Thing;
use org\schema\Type;
use org\schema\traits\MeasurementVariableTrait;

use PHPUnit\Framework\TestCase;

class StatisticalVariableTest extends TestCase
{
    public function testExtendsConstraintNode()
    {
        $variable = new StatisticalVariable() ;

        $this->assertInstanceOf( ConstraintNode::class , $variable ) ;
        $this->assertInstanceOf( Intangible::class     , $variable ) ;
        $this->assertInstanceOf( Thing::class          , $variable ) ;
    }

    public function testConstructorWithNoArguments()
    {
        $variable = new StatisticalVariable() ;

        $this->assertObjectHasProperty( Schema::STAT_TYPE , $variable ) ;
        $this->assertNull( $variable->statType ?? null , 'The statType property must be null by default' ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testConstructorInitializesProperties()
    {
        $population = new Type([ Schema::NAME => 'Person' ]) ;

        $variable = new StatisticalVariable
        ([
            Schema::MEASURED_PROPERTY => 'population' ,
            Schema::POPULATION_TYPE   => $population  ,
            Schema::STAT_TYPE         => 'count'      ,
        ]) ;

        $this->assertSame( 'population' , $variable->measuredProperty ) ;
        $this->assertSame( $population  , $variable->populationType   ) ;
        $this->assertSame( 'count'      , $variable->statType         ) ;
    }

    /**
     * A statType or a populationType read back from a store is a raw row, and a
     * union that named only the class rejected it with a TypeError.
     *
     * @throws ReflectionException
     */
    public function testConstructorAcceptsRawArrayValues()
    {
        $variable = new StatisticalVariable
        ([
            Schema::POPULATION_TYPE         => [ Schema::AT_TYPE => 'Class' , Schema::NAME => 'Person' ] ,
            Schema::STAT_TYPE               => [ Schema::NAME => 'median'       ] ,
            Schema::CONSTRAINT_PROPERTY     => [ Schema::NAME => 'gender'       ] ,
            Schema::MEASUREMENT_DENOMINATOR => [ Schema::NAME => 'Count_Person' ] ,
        ]) ;

        $this->assertIsArray( $variable->populationType         ) ;
        $this->assertIsArray( $variable->statType               ) ;
        $this->assertIsArray( $variable->constraintProperty     ) ;
        $this->assertIsArray( $variable->measurementDenominator ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testConstructorAcceptsTypedMeasurementValues()
    {
        $denominator = new StatisticalVariable([ Schema::NAME => 'Count_Person' ]) ;

        $variable = new StatisticalVariable
        ([
            Schema::MEASURED_PROPERTY       => new Property() ,
            Schema::MEASUREMENT_DENOMINATOR => $denominator ,
            Schema::MEASUREMENT_METHOD      => new DefinedTerm([ Schema::NAME => 'Survey' ]) ,
            Schema::MEASUREMENT_QUALIFIER   => new DefinedTerm([ Schema::NAME => 'Nominal' ]) ,
            Schema::MEASUREMENT_TECHNIQUE   => 'sampling' ,
        ]) ;

        $this->assertInstanceOf( Property::class    , $variable->measuredProperty       ) ;
        $this->assertSame      ( $denominator       , $variable->measurementDenominator ) ;
        $this->assertInstanceOf( DefinedTerm::class , $variable->measurementMethod      ) ;
        $this->assertInstanceOf( DefinedTerm::class , $variable->measurementQualifier   ) ;
        $this->assertSame      ( 'sampling'         , $variable->measurementTechnique   ) ;
    }

    public function testUsesTheMeasurementVariableTrait()
    {
        $this->assertContains
        (
            MeasurementVariableTrait::class ,
            class_uses( StatisticalVariable::class ) ,
            'StatisticalVariable must take its measurement terms from the shared trait.'
        ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testJsonSerializeIncludesContextAndType()
    {
        $variable = new StatisticalVariable([ Schema::STAT_TYPE => 'count' ]) ;

        $data = $variable->jsonSerialize() ;

        $this->assertArrayHasKey( Schema::AT_TYPE    , $data ) ;
        $this->assertArrayHasKey( Schema::AT_CONTEXT , $data ) ;

        $this->assertSame( 'StatisticalVariable' , $data[ Schema::AT_TYPE    ] ) ;
        $this->assertSame( Thing::CONTEXT        , $data[ Schema::AT_CONTEXT ] ) ;
        $this->assertSame( 'count'               , $data[ Schema::STAT_TYPE  ] ) ;
    }

    public function testGetSchemaTypeReturnsRootUri()
    {
        $this->assertSame( 'https://schema.org/StatisticalVariable' , StatisticalVariable::getSchemaType() ) ;
    }
}
