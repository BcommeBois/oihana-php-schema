<?php

namespace tests\org\schema ;

use org\schema\constants\Prop;
use org\schema\Thing;
use PHPUnit\Framework\TestCase;
use ReflectionException;

class ThingTest extends TestCase
{
    public function testConstructorWithNoArguments()
    {
        $thing = new Thing();

        $this->assertObjectHasProperty( Prop::NAME , $thing );
        $this->assertNull( $thing->name ?? null , 'The name property must be null by default');
    }

    /**
     * @throws ReflectionException
     */
    public function testConstructorInitializesProperties()
    {
        $thing = new Thing( ['name' => 'Alice'  ] );
        $this->assertSame('Alice' , $thing->name );
    }

    /**
     * @throws ReflectionException
     */
    public function testJsonSerializeIncludesContextAndType()
    {
        $thing = new Thing( ['name' => 'Carol' ] );

        $data = $thing->jsonSerialize();

        // echo var_dump($data) . PHP_EOL;

        $this->assertArrayHasKey(Prop::AT_TYPE    , $data ) ;
        $this->assertArrayHasKey(Prop::AT_CONTEXT , $data ) ;

        $this->assertEquals('Carol' , $data[ Prop::NAME ] ) ;

        $this->assertEquals('Thing', $data[ Prop::AT_TYPE ] ) ;
        $this->assertEquals(Thing::CONTEXT , $data[ Prop::AT_CONTEXT ] ) ;
    }

    public function testGetSchemaTypeReturnsRootUri()
    {
        $this->assertEquals('https://schema.org/Thing', Thing::getSchemaType());
    }
}

