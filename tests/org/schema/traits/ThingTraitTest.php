<?php

namespace tests\org\schema\traits;

use JsonSerializable;
use org\schema\constants\Prop;
use org\schema\constants\Schema;
use org\schema\traits\ThingTrait;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use ReflectionProperty;
use stdClass;

class MockThing implements JsonSerializable
{
    use ThingTrait;

    /**
     * JSON-LD @context declaration for Schema.org.
     */
    public const string CONTEXT = 'https://schema.org' ;

    public ?string   $name        = '' ;
    public int       $age         = 0  ;
    public ?string   $description = null;
    protected string $secret      = 'hidden';
}

class MockEmptyThing implements JsonSerializable
{
    use ThingTrait;

    public const string CONTEXT = 'https://schema.org' ;
}

class ThingTraitTest extends TestCase
{
    public function testConstructorWithNoArguments()
    {
        $thing = new MockThing();

        $this->assertEmpty( $thing->name );
        $this->assertEquals(0 , $thing->age ) ;
        $this->assertNull($thing->description);
    }

    /**
     * @throws ReflectionException
     */
    public function testConstructorInitializesProperties()
    {
        $thing = new MockThing( ['name' => 'Alice' , 'age' => 30 ] );
        $this->assertSame('Alice' , $thing->name);
        $this->assertSame(30      , $thing->age);
    }

    /**
     * @throws ReflectionException
     */
    public function testConstructorWithStdClassObject()
    {
        $init = new stdClass();
        $init->name = 'David';
        $init->age = 35;

        $thing = new MockThing($init);

        $this->assertSame('David', $thing->name);
        $this->assertSame(35, $thing->age);
    }

    /**
     * @throws ReflectionException
     */
    public function testConstructorIgnoresUnknownProperties()
    {
        $thing = new MockThing( ['unknown' => 'value', 'name' => 'Bob'] );

        $this->assertSame('Bob', $thing->name);
        $this->assertObjectNotHasProperty('unknown', $thing ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testConstructorIgnoresProtectedProperties()
    {
        $thing = new MockThing(['secret' => 'attempted_override', 'name' => 'Eve']);

        $this->assertSame('Eve', $thing->name);
        // La propriété protégée ne devrait pas être modifiée
        $reflectionProperty = new ReflectionProperty(MockThing::class, 'secret');
        $this->assertSame('hidden', $reflectionProperty->getValue($thing));
    }

    /**
     * @throws ReflectionException
     */
    public function testJsonSerializeIncludesContextAndType()
    {
        $thing = new MockThing( ['name' => 'Carol', 'age' => 25 ] );

        $data = $thing->jsonSerialize();

        // echo var_dump( $data ) . PHP_EOL;

        $this->assertArrayHasKey(Prop::AT_TYPE    , $data ) ;
        $this->assertArrayHasKey(Prop::AT_CONTEXT , $data ) ;

        $this->assertEquals('Carol' , $data[ Prop::NAME ] ) ;
        $this->assertEquals(25      , $data[ 'age' ]  ) ;

        $this->assertEquals('MockThing', $data[ Prop::AT_TYPE ] ) ;
        $this->assertEquals('https://schema.org', $data[ Prop::AT_CONTEXT ] ) ;
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

    /**
     * @throws ReflectionException
     */
    public function testJsonSerializeWithEmptyStringIsPreserved()
    {
        $thing = new MockThing( [ 'name' => '', 'age' => 0 ] );

        $data = $thing->jsonSerialize();

        // Les chaînes vides et les zéros ne sont pas supprimés par compress()
        $this->assertArrayHasKey('name', $data);
        $this->assertSame('', $data['name']);
        $this->assertArrayHasKey('age', $data);
        $this->assertSame(0, $data['age']);
    }

    /**
     * @throws ReflectionException
     */
    public function testJsonSerializeOnlyIncludesPublicProperties()
    {
        $thing = new MockThing(['name' => 'Frank', 'age' => 50]);

        $data = $thing->jsonSerialize();

        // Vérifier que la propriété protégée 'secret' n'est pas incluse
        $this->assertArrayNotHasKey('secret', $data);

        // Mais que les propriétés publiques le sont
        $this->assertArrayHasKey('name', $data);
        $this->assertArrayHasKey('age', $data);
    }

    /**
     * @throws ReflectionException
     */
    public function testJsonSerializeWithEmptyObject()
    {
        $thing = new MockEmptyThing();

        $data = $thing->jsonSerialize();

        // Doit au minimum contenir @type et @context
        $this->assertArrayHasKey(Prop::AT_TYPE, $data);
        $this->assertArrayHasKey(Prop::AT_CONTEXT, $data);
        $this->assertEquals('MockEmptyThing', $data[Prop::AT_TYPE]);
        $this->assertEquals('https://schema.org', $data[Prop::AT_CONTEXT]);

        // Et seulement ces deux clés
        $this->assertCount(2, $data);
    }

    public function testContextConstant()
    {
        $this->assertEquals('https://schema.org', MockThing::CONTEXT);
    }

    /**
     * @throws ReflectionException
     */
    public function testGetShortNameReturnsCorrectClassName()
    {
        $thing = new MockThing();

        $shortName = $thing->getShortName($thing);

        $this->assertEquals('MockThing', $shortName);
    }

    /**
     * @throws ReflectionException
     */
    public function testGetPublicPropertiesReturnsOnlyPublicProperties()
    {
        $thing = new MockThing();

        $properties = $thing->getPublicProperties($thing);

        $propertyNames = array_map(fn($prop) => $prop->getName(), $properties);

        $this->assertContains('name', $propertyNames);
        $this->assertContains('age', $propertyNames);
        $this->assertContains('description', $propertyNames);
        $this->assertNotContains('secret', $propertyNames); // Propriété protégée
    }

    /**
     * Test de l'intégration complète : construction + sérialisation
     * @throws ReflectionException
     */
    public function testFullWorkflow()
    {
        $thing = new MockThing
        ([
            'name'         => 'Integration Test',
            'age'          => 42,
            'description'  => 'A complete test',
            'unknown_prop' => 'should be ignored'
        ]);

        $this->assertEquals('Integration Test', $thing->name);
        $this->assertEquals(42, $thing->age);
        $this->assertEquals('A complete test', $thing->description);

        $json = $thing->jsonSerialize();

        $expected =
        [
            Schema::AT_TYPE => 'MockThing',
            Schema::AT_CONTEXT => 'https://schema.org',
            'name' => 'Integration Test',
            'age' => 42,
            'description' => 'A complete test'
        ];

        $this->assertEquals($expected, $json);
        $this->assertArrayNotHasKey('unknown_prop', $json);
    }


    /**
     * @throws ReflectionException
     */
    public function testToJsonSchema()
    {
        $thing = new MockThing
        ([
            'name'         => 'Integration Test',
            'age'          => 42,
            'description'  => 'A complete test',
        ]);

        $schema = $thing->toJsonSchema() ; // $strict = true (default)

        $expected =
        [
            'type'                 => 'object' ,
            '$schema'              => 'https://json-schema.org/draft/2020-12/schema' ,
            'title'                => 'MockThing' ,
            'additionalProperties' => false ,
            'properties' =>
            [
                'name' =>
                [
                    "default" => 'Integration Test' ,
                    "oneOf"   =>
                    [
                        [ 'type' => 'null'   ] ,
                        [ 'type' => 'string' ] ,
                    ]
                ] ,
                'age' =>
                [
                    "default" => 42 ,
                    'type'    => 'integer'
                ] ,
                'description' =>
                [
                    "default" => 'A complete test' ,
                    "oneOf"   =>
                    [
                        [ 'type' => 'null'   ] ,
                        [ 'type' => 'string' ] ,
                    ]
                ] ,
            ]
        ];

        $this->assertEquals( $expected , $schema ) ;

        $schema = $thing->toJsonSchema(false );

        $expected =
        [
            'type'       => 'object' ,
            'properties' =>
            [
                'name' =>
                [
                    "default" => 'Integration Test' ,
                    "oneOf"   =>
                    [
                        [ 'type' => 'null'   ] ,
                        [ 'type' => 'string' ] ,
                    ]
                ] ,
                'age' =>
                [
                    "default" => 42 ,
                    'type'    => 'integer'
                ] ,
                'description' =>
                [
                    "default" => 'A complete test' ,
                    "oneOf"   =>
                    [
                        [ 'type' => 'null'   ] ,
                        [ 'type' => 'string' ] ,
                    ]
                ] ,
            ]
        ];

        $this->assertEquals( $expected , $schema ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testJsonSchema()
    {
        $schema = MockThing::jsonSchema();

        $expected =
        [
            'type'                  => 'object' ,
            '$schema'              => 'https://json-schema.org/draft/2020-12/schema' ,
            'title'                => 'MockThing' ,
            'additionalProperties' => false ,
            'properties' =>
            [
                'name' =>
                [
                    "oneOf" =>
                    [
                        [ 'type' => 'null'   ] ,
                        [ 'type' => 'string' ] ,
                    ]
                ] ,
                'age' =>
                [
                    'type' => 'integer'
                ] ,
                'description' =>
                [
                    "oneOf" =>
                    [
                        [ 'type' => 'null'   ] ,
                        [ 'type' => 'string' ] ,
                    ]
                ] ,
            ]
        ];

        $this->assertEquals( $expected , $schema ) ;
    }
}

