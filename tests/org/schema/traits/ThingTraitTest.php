<?php

namespace tests\org\schema\traits;

use JsonSerializable;
use oihana\reflect\utils\JsonSerializer;
use org\schema\Person;
use org\schema\PostalAddress;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;
use stdClass;

use PHPUnit\Framework\TestCase;

use oihana\core\options\CompressOption;
use oihana\core\options\ArrayOption;
use org\schema\constants\Prop;
use org\schema\constants\Schema;
use org\schema\Thing;
use org\schema\traits\ThingTrait;

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
    public ?string   $url ;
}

class MockEmptyThing implements JsonSerializable
{
    use ThingTrait;

    public const string CONTEXT = 'https://schema.org' ;
}

class SuperMockThing extends MockThing
{
    public const array JSON_PRIORITY_KEYS =
    [
        'age',
        Schema::AT_TYPE,
        Schema::AT_CONTEXT,
    ];
}

class MockCustomThing implements JsonSerializable
{
    use ThingTrait;

    /**
     * Custom context for Proginov.
     */
    public const string CONTEXT = 'https://schema.custom.com' ;
}

class ThingTraitTest extends TestCase
{
    /// -------------- getSchemaType()

    public function testGetSchemaTypeReturnsCorrectUri()
    {
        // Test avec le contexte standard Schema.org
        $this->assertEquals('https://schema.org/MockThing', MockThing::getSchemaType());
        $this->assertEquals('https://schema.org/MockEmptyThing', MockEmptyThing::getSchemaType());

        // Test avec un contexte personnalisé (Late Static Binding)
        $this->assertEquals('https://schema.custom.com/MockCustomThing', MockCustomThing::getSchemaType());
    }

    public function testGetSchemaTypeHandlesTrailingSlashInContext()
    {
        $anonymous = new class implements JsonSerializable {
            use ThingTrait;
            public const string CONTEXT = 'https://example.com/';
        };

        $this->assertEquals('https://example.com/' . new ReflectionClass($anonymous)->getShortName(), $anonymous::getSchemaType());
    }

    public function testGetSchemaTypeCacheIsIsolatedBetweenClasses()
    {
        $uriThing = MockThing::getSchemaType();
        $uriEmpty = MockEmptyThing::getSchemaType();

        $this->assertNotEquals($uriThing, $uriEmpty);
        $this->assertStringContainsString('MockThing', $uriThing);
        $this->assertStringContainsString('MockEmptyThing', $uriEmpty);
    }

    // -------------- Constructor

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

        $this->assertNull( $data['name'] ) ;
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
                'url' =>
                [
                    "oneOf"  =>
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
                'url' =>
                [
                    "oneOf"  =>
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
                'url' =>
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

    /**
     * @throws ReflectionException
     */
    public function testJsonSerializeRespectsPriorityKeysOrder()
    {
        $thing = new MockThing
        ([
            'name'        => 'Alice',
            'age'         => 30,
            'description' => 'Test',
            'url'         => 'https://example.com',
        ]);

        $data = $thing->jsonSerialize();
        $keys = array_keys( $data );

        $expectedKeys = [ '@type', '@context', 'name' , 'url', 'age' , 'description' ];
        $this->assertSame( $expectedKeys , $keys );
    }

    /**
     * @throws ReflectionException
     */
    public function testJsonSerializePriorityKeysOverrideInSubclass()
    {
        $mock = new SuperMockThing() ;

        $mock->name = 'Bob';
        $mock->age  = 99;

        $data = $mock->jsonSerialize();

        $expectedKeys = [ 'age', '@type', '@context' , 'description', 'name' ];
        $this->assertSame($expectedKeys, array_keys($data));
    }

    /**
     * Test REDUCE_OPTIONS avec array d'options avancées
     * @throws ReflectionException
     */
    public function testJsonSerializeWithReduceOptionsAdvanced()
    {
        $mock = new class extends Thing {

            public const string CONTEXT = 'https://schema.org';

            public function getJsonSerializeOptions(): array
            {
                return
                [
                    ArrayOption::REDUCE =>
                    [
                        CompressOption::CONDITIONS =>
                        [
                            fn($v) => is_null($v),
                            fn($v) => is_string($v) && trim($v) === '',
                        ],
                        CompressOption::EXCLUDES => ['description', 'name'],
                    ]
                ];
            }

            public ?string $email = '';
            public int $age = 0;
        };

        // Initialisation des propriétés héritées de Thing
        $mock->name = '';  // Chaîne vide mais dans excludes
        $mock->description = null;  // null mais dans excludes
        $mock->url = null;  // null et pas dans excludes
        $mock->email = '';  // Chaîne vide et pas dans excludes
        $mock->age = 0;

        $data = $mock->jsonSerialize();

        // 'name' est une chaîne vide MAIS dans excludes, donc gardée
        $this->assertArrayHasKey('name', $data);
        $this->assertEquals('', $data['name']);

        // 'description' est null MAIS dans excludes, donc gardée
        $this->assertArrayHasKey('description', $data);
        $this->assertNull($data['description']);

        // 'url' est null ET pas dans excludes, donc supprimée
        $this->assertArrayNotHasKey('url', $data);

        // 'email' est une chaîne vide ET pas dans excludes, donc supprimée
        $this->assertArrayNotHasKey('email', $data);

        // 'age' à 0 est gardé (pas de condition pour supprimer les 0)
        $this->assertArrayHasKey('age', $data);
        $this->assertEquals(0, $data['age']);

        // Vérifier la présence de @type et @context
        $this->assertArrayHasKey('@type', $data);
        $this->assertArrayHasKey('@context', $data);
    }

    /**
     * Test JsonSerializer::encode() avec Thing
     * @throws ReflectionException
     */
    public function testJsonSerializerEncodeSimpleThing()
    {
        $thing = new Thing([
            'name' => 'Test Thing',
            'id' => 'thing-123',
            'url' => 'https://example.com'
        ]);

        $json = JsonSerializer::encode($thing);

        $this->assertJson($json);

        $decoded = json_decode($json, true);
        $this->assertEquals('Test Thing', $decoded['name']);
        $this->assertEquals('thing-123', $decoded['id']);
        $this->assertEquals('https://example.com', $decoded['url']);
        $this->assertEquals('Thing', $decoded['@type']);
        $this->assertEquals('https://schema.org', $decoded['@context']);
    }

    /**
     * Test JsonSerializer::encode() avec Person
     * @throws ReflectionException
     */
    public function testJsonSerializerEncodeSimplePerson()
    {
        $person = new Person([
            'name' => 'Alice Dupont',
            'email' => 'alice@example.com',
            'givenName' => 'Alice',
            'familyName' => 'Dupont'
        ]);

        $json = JsonSerializer::encode($person);

        $this->assertJson($json);

        $decoded = json_decode($json, true);
        $this->assertEquals('Alice Dupont', $decoded['name']);
        $this->assertEquals('alice@example.com', $decoded['email']);
        $this->assertEquals('Alice', $decoded['givenName']);
        $this->assertEquals('Dupont', $decoded['familyName']);
        $this->assertEquals('Person', $decoded['@type']);
    }

    /**
     * Test JsonSerializer::encode() avec tableau de Things
     * @throws ReflectionException
     */
    public function testJsonSerializerEncodeArrayOfThings()
    {
        $things = [
            new Thing(['name' => 'Thing 1', 'id' => '1']),
            new Thing(['name' => 'Thing 2', 'id' => '2']),
            new Thing(['name' => 'Thing 3', 'id' => '3'])
        ];

        $json = JsonSerializer::encode($things);

        $this->assertJson($json);

        $decoded = json_decode($json, true);
        $this->assertCount(3, $decoded);
        $this->assertEquals('Thing 1', $decoded[0]['name']);
        $this->assertEquals('Thing 2', $decoded[1]['name']);
        $this->assertEquals('Thing 3', $decoded[2]['name']);
    }

    /**
     * Test JsonSerializer::encode() avec ArrayOption::REDUCE pour supprimer les nulls
     * @throws ReflectionException
     */
    public function testJsonSerializerWithReduceOptionRemovesNulls()
    {
        $person = new Person([
            'name' => 'Alice',
            'email' => 'alice@example.com',
            'familyName' => null,
            'givenName' => null
        ]);

        $json = JsonSerializer::encode($person, options:[
            ArrayOption::REDUCE => true
        ]);

        $decoded = json_decode($json, true);

        // Les propriétés avec valeurs non-null doivent être présentes
        $this->assertArrayHasKey('name', $decoded);
        $this->assertArrayHasKey('email', $decoded);

        // Les propriétés nulles ne doivent pas être présentes
        $this->assertArrayNotHasKey('familyName', $decoded);
        $this->assertArrayNotHasKey('givenName', $decoded);
    }

    /**
     * Test JsonSerializer::encode() avec REDUCE préserve les chaînes vides
     * @throws ReflectionException
     */
    public function testJsonSerializerWithReduceOptionPreservesEmptyStrings()
    {
        $thing = new Thing([
            'name' => '',
            'description' => null,
            'url' => 'https://example.com'
        ]);

        $json = JsonSerializer::encode($thing, options:[
            ArrayOption::REDUCE => true
        ]);

        $decoded = json_decode($json, true);

        // Les chaînes vides sont préservées
        $this->assertArrayHasKey('name', $decoded);
        $this->assertEquals('', $decoded['name']);

        // Les null sont supprimés
        $this->assertArrayNotHasKey('description', $decoded);

        // Les valeurs normales sont présentes
        $this->assertArrayHasKey('url', $decoded);
    }

    /**
     * Test JsonSerializer::encode() avec options REDUCE avancées
     * @throws ReflectionException
     */
    public function testJsonSerializerWithReduceAdvancedOptions()
    {
        $person = new Person([
            'name' => '',
            'email' => 'test@example.com',
            'givenName' => null,
            'familyName' => '   ',
            'telephone' => ''
        ]);

        $json = JsonSerializer::encode($person, options: [
            ArrayOption::REDUCE => [
                CompressOption::CONDITIONS => [
                    fn($v) => is_null($v),
                    fn($v) => is_string($v) && trim($v) === '',
                ],
                CompressOption::EXCLUDES => ['name']
            ]
        ]);

        $decoded = json_decode($json, true);

        // 'name' est vide mais dans excludes, donc gardée
        $this->assertArrayHasKey('name', $decoded);
        $this->assertEquals('', $decoded['name']);

        // 'email' a une valeur, donc gardée
        $this->assertArrayHasKey('email', $decoded);

        // 'givenName' est null, donc supprimée
        $this->assertArrayNotHasKey('givenName', $decoded);

        // 'familyName' est une chaîne vide (après trim), donc supprimée
        $this->assertArrayNotHasKey('familyName', $decoded);

        // 'telephone' est vide et pas dans excludes, donc supprimée
        $this->assertArrayNotHasKey('telephone', $decoded);
    }

    /**
     * Test que les options JsonSerializer sont reset après encode
     * @throws ReflectionException
     */
    public function testJsonSerializerOptionsAreResetAfterEncode()
    {
        $thing = new Thing(['name' => 'Test']);

        // Encoder avec des options
        JsonSerializer::encode( $thing, options: [
            ArrayOption::REDUCE => true
        ]);

        // Les options doivent être vides après encode
        $this->assertEmpty( JsonSerializer::getOptions());

        // Un nouvel encode sans options ne devrait pas avoir les anciennes options
        $thing2 = new Thing(['name' => 'Test 2', 'description' => null]);
        $json = JsonSerializer::encode($thing2);

        $decoded = json_decode($json, true);
        // Sans REDUCE, les null sont présents
        $this->assertArrayHasKey('description', $decoded);
    }

    /**
     * Test JsonSerializer::encode() avec objets Thing imbriqués
     * @throws ReflectionException
     */
    public function testJsonSerializerNestedThingObjects()
    {
        $address = new PostalAddress
        ([
            'streetAddress' => '123 Main St',
            'addressLocality' => 'Paris',
            'postalCode' => '75001'
        ]);

        $person = new Person([
            'name' => 'Alice',
            'address' => $address
        ]);

        $json = JsonSerializer::encode($person);
        $decoded = json_decode($json, true);

        $this->assertArrayHasKey('address', $decoded);
        $this->assertIsArray($decoded['address']);
        $this->assertEquals('123 Main St', $decoded['address']['streetAddress']);
        $this->assertEquals('Paris', $decoded['address']['addressLocality']);
        $this->assertEquals('PostalAddress', $decoded['address']['@type']);
    }

    /**
     * Test JsonSerializer::encode() avec JSON_PRETTY_PRINT
     * @throws ReflectionException
     */
    public function testJsonSerializerWithPrettyPrintFlag()
    {
        $thing = new Thing([
            'name' => 'Test Thing',
            'id' => 'thing-123'
        ]);

        $json = JsonSerializer::encode($thing,  JSON_PRETTY_PRINT);

        $this->assertStringContainsString("\n", $json);
        $this->assertStringContainsString('    ', $json);
    }

    /**
     * Test JsonSerializer::encode() avec JSON_UNESCAPED_SLASHES
     * @throws ReflectionException
     */
    public function testJsonSerializerWithUnescapedSlashesFlag()
    {
        $thing = new Thing([
            'url' => 'https://example.com/path/to/resource'
        ]);

        $json = JsonSerializer::encode($thing, JSON_UNESCAPED_SLASHES);

        $this->assertStringContainsString('https://example.com/path/to/resource', $json);
        $this->assertStringNotContainsString('\/', $json);
    }

    /**
     * Test JsonSerializer::encode() avec options et flags combinés
     * @throws ReflectionException
     */
    public function testJsonSerializerCombinedOptionsAndFlags()
    {
        $person = new Person([
            'name' => 'Alice',
            'email' => 'alice@example.com',
            'telephone' => null,
            'url' => 'https://example.com/alice'
        ]);

        $json = JsonSerializer::encode(
            $person,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ,
            [ArrayOption::REDUCE => true],
        );

        $decoded = json_decode($json, true);

        // REDUCE option: null supprimé
        $this->assertArrayNotHasKey('telephone', $decoded);

        // JSON_PRETTY_PRINT: formaté
        $this->assertStringContainsString("\n", $json);

        // JSON_UNESCAPED_SLASHES: slashes non échappés
        $this->assertStringContainsString('https://example.com/alice', $json);
    }

    /**
     * Test que plusieurs appels JsonSerializer::encode() sont indépendants
     * @throws ReflectionException
     */
    public function testJsonSerializerMultipleEncodeCallsAreIndependent()
    {
        $thing1 = new Thing(['name' => 'Thing 1', 'description' => null]);
        $thing2 = new Thing(['name' => 'Thing 2', 'url' => null]);

        // Premier encode avec REDUCE
        $json1 = JsonSerializer::encode($thing1, options: [ArrayOption::REDUCE => true]);
        $decoded1 = json_decode($json1, true);

        // Deuxième encode sans REDUCE
        $json2 = JsonSerializer::encode($thing2);
        $decoded2 = json_decode($json2, true);

        // Premier: description null supprimée
        $this->assertArrayNotHasKey('description', $decoded1);

        // Deuxième: url null présente (pas de REDUCE)
        $this->assertArrayHasKey('url', $decoded2);
        $this->assertNull($decoded2['url']);
    }

    /**
     * @throws ReflectionException
     */
    public function testJsonSerializerHandlesComplexPersonStructure()
    {
        $person = new Person([
            'name' => 'Dr. Alice Dupont',
            'givenName' => 'Alice',
            'familyName' => 'Dupont',
            'honorificPrefix' => 'Dr.',
            'email' => 'alice.dupont@example.com',
            'telephone' => '+33 1 23 45 67 89',
            'jobTitle' => 'Software Engineer',
            'url' => 'https://example.com/alice',
            'birthDate' => '1990-01-15',
            'nationality' => 'French',
            'gender' => 'Female'
        ]);

        $json = JsonSerializer::encode($person, JSON_PRETTY_PRINT);
        $decoded = json_decode($json, true);

        $this->assertEquals('Person', $decoded['@type']);
        $this->assertEquals('Dr. Alice Dupont', $decoded['name']);
        $this->assertEquals('Alice', $decoded['givenName']);
        $this->assertEquals('Dupont', $decoded['familyName']);
        $this->assertEquals('Dr.', $decoded['honorificPrefix']);
        $this->assertEquals('alice.dupont@example.com', $decoded['email']);
        $this->assertEquals('Software Engineer', $decoded['jobTitle']);
        $this->assertEquals('1990-01-15', $decoded['birthDate']);
    }

    /**
     * @throws ReflectionException
     */
    public function testJsonSerializerWithCustomContext()
    {
        $thing = new Thing(['name' => 'Custom Thing']);
        $thing->withAtContext('https://custom-schema.org');

        $json = JsonSerializer::encode($thing);
        $decoded = json_decode($json, true);

        $this->assertEquals('https://custom-schema.org', $decoded['@context']);
    }

    /**
     * @throws ReflectionException
     */
    public function testJsonSerializerWithCustomType()
    {
        $thing = new Thing(['name' => 'Custom Thing']);
        $thing->withAtType('CustomThing');

        $json = JsonSerializer::encode($thing);
        $decoded = json_decode($json, true);

        $this->assertEquals('CustomThing', $decoded['@type']);
    }

    /**
     * @throws ReflectionException
     */
    public function testJsonSerializerArrayOfMixedThingTypes()
    {
        $items = [
            new Thing(['name' => 'Generic Thing']),
            new Person(['name' => 'Alice', 'email' => 'alice@example.com']),
            new Thing(['name' => 'Another Thing'])
        ];

        $json = JsonSerializer::encode($items);
        $decoded = json_decode($json, true);

        $this->assertCount(3, $decoded);
        $this->assertEquals('Thing', $decoded[0]['@type']);
        $this->assertEquals('Person', $decoded[1]['@type']);
        $this->assertEquals('Thing', $decoded[2]['@type']);
    }
}

