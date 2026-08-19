<?php

namespace tests\org\schema ;

use ReflectionException;

use org\schema\constants\Schema;
use org\schema\ConstraintNode;
use org\schema\Intangible;
use org\schema\Property;
use org\schema\Thing;

use PHPUnit\Framework\TestCase;

class ConstraintNodeTest extends TestCase
{
    public function testExtendsIntangible()
    {
        $node = new ConstraintNode() ;

        $this->assertInstanceOf( Intangible::class , $node ) ;
        $this->assertInstanceOf( Thing::class      , $node ) ;
    }

    public function testConstructorWithNoArguments()
    {
        $node = new ConstraintNode() ;

        $this->assertObjectHasProperty( Schema::CONSTRAINT_PROPERTY , $node ) ;
        $this->assertNull( $node->constraintProperty ?? null , 'The constraintProperty property must be null by default' ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testConstructorInitializesProperties()
    {
        $node = new ConstraintNode
        ([
            Schema::CONSTRAINT_PROPERTY => 'gender' ,
            Schema::NUM_CONSTRAINTS     => 1        ,
        ]) ;

        $this->assertSame( 'gender' , $node->constraintProperty ) ;
        $this->assertSame( 1        , $node->numConstraints     ) ;
    }

    /**
     * A node constrains more than one property as often as it constrains one,
     * and a constraint read back from a store is a raw row rather than a Property.
     *
     * @throws ReflectionException
     */
    public function testConstraintPropertyAcceptsRawArrayValues()
    {
        $node = new ConstraintNode
        ([
            Schema::CONSTRAINT_PROPERTY => [ 'gender' , 'age' ] ,
            Schema::NUM_CONSTRAINTS     => 2 ,
        ]) ;

        $this->assertSame( [ 'gender' , 'age' ] , $node->constraintProperty ) ;
        $this->assertSame( 2 , $node->numConstraints ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testConstraintPropertyAcceptsAProperty()
    {
        $property = new Property([ Schema::NAME => 'gender' ]) ;

        $node = new ConstraintNode([ Schema::CONSTRAINT_PROPERTY => $property ]) ;

        $this->assertSame( $property , $node->constraintProperty ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testJsonSerializeIncludesContextAndType()
    {
        $node = new ConstraintNode([ Schema::CONSTRAINT_PROPERTY => 'gender' ]) ;

        $data = $node->jsonSerialize() ;

        $this->assertArrayHasKey( Schema::AT_TYPE    , $data ) ;
        $this->assertArrayHasKey( Schema::AT_CONTEXT , $data ) ;

        $this->assertSame( 'ConstraintNode' , $data[ Schema::AT_TYPE ] ) ;
        $this->assertSame( Thing::CONTEXT   , $data[ Schema::AT_CONTEXT ] ) ;
        $this->assertSame( 'gender'         , $data[ Schema::CONSTRAINT_PROPERTY ] ) ;
    }

    public function testGetSchemaTypeReturnsRootUri()
    {
        $this->assertSame( 'https://schema.org/ConstraintNode' , ConstraintNode::getSchemaType() ) ;
    }
}
