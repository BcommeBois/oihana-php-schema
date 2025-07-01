<?php

namespace org\schema\traits;

use JsonSerializable;
use org\schema\constants\Prop;
use PHPUnit\Framework\TestCase;
use ReflectionException;

class MockThing implements JsonSerializable
{
    use ThingTrait;

    public ?string $name = '' ;
    public int     $age  = 0  ;
}

class ThingTraitTest extends TestCase
{
    public function testConstructorWithNoArguments()
    {
        $thing = new MockThing();

        $this->assertEmpty( $thing->name );
        $this->assertEquals(0 , $thing->age ) ;
    }

    public function testConstructorInitializesProperties()
    {
        $thing = new MockThing( ['name' => 'Alice' , 'age' => 30 ] );
        $this->assertSame('Alice' , $thing->name);
        $this->assertSame(30      , $thing->age);
    }

    public function testConstructorIgnoresUnknownProperties()
    {
        $thing = new MockThing( ['unknown' => 'value', 'name' => 'Bob'] );

        $this->assertSame('Bob', $thing->name);
        $this->assertObjectNotHasProperty('unknown', $thing ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testJsonSerializeIncludesContextAndType()
    {
        $thing = new MockThing( ['name' => 'Carol', 'age' => 25 ] );

        $data = $thing->jsonSerialize();

        // echo var_dump($data) . PHP_EOL;

        $this->assertArrayHasKey(Prop::AT_TYPE    , $data ) ;
        $this->assertArrayHasKey(Prop::AT_CONTEXT , $data ) ;

        $this->assertEquals('Carol' , $data[ Prop::NAME ] ) ;
        $this->assertEquals(25      , $data[ 'age' ]  ) ;

        $this->assertEquals('MockThing', $data[ Prop::AT_TYPE ] ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testJsonSerializeOmitsNullsIfCompressed()
    {
        $thing = new MockThing( [ 'name' => null, 'age' => 40 ] );

        $data = $thing->jsonSerialize();

        $this->assertArrayNotHasKey('name' , $data ) ;
        $this->assertEquals(40 , $data['age'] );
    }
}

