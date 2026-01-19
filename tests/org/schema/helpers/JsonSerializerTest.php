<?php

namespace org\schema\helpers;

use ArrayObject;
use oihana\core\options\ArrayOption;
use org\schema\constants\Schema;
use org\schema\helpers\SchemaResolver;
use org\schema\Person;
use org\schema\Thing;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use stdClass;

class JsonSerializerTest extends TestCase
{
    /**
     * @throws ReflectionException
     */
    public function testEncodeSerializesSingleThingWithTemporaryOptions()
    {
        $thing = new Person( [ Schema::NAME => 'Alice'] ) ;

        $json = JsonSerializer::encode( $thing , [ ArrayOption::REDUCE => true] , JSON_UNESCAPED_SLASHES );

        $this->assertEquals ( '{"@type":"Person","@context":"https://schema.org","name":"Alice"}' , $json);
        $this->assertEquals ( [] , JsonSerializer::getOptions() , 'Temporary options should reset after encode');
    }

    public function testEncodeSerializesArrayOfThings()
    {
        $things = [
            new class extends Thing
            {
                public const string CONTEXT = 'https://schema.org';
                public null|int|string $name = 'Bob';
            },
            new class extends Thing
            {
                public const string CONTEXT = 'https://schema.org';
                public null|int|string $name = 'Carol';
            },
        ];

        $json = JsonSerializer::encode($things, ['removeNulls' => true]);

        $this->assertStringContainsString('Bob', $json);
        $this->assertStringContainsString('Carol', $json);
        $this->assertEquals([ArrayOption::REDUCE => true], JsonSerializer::getOptions(), 'Temporary options should be applied for array encode');

        JsonSerializer::encode($things);
        $this->assertEquals([], JsonSerializer::getOptions());
    }

    public function testEncodeMaintainsJsonFlags()
    {
        $thing = new class extends Thing
        {
            public const string CONTEXT = 'https://schema.org';

            public null|int|string $name = 'Dave';
        };

        $json = JsonSerializer::encode($thing, [], JSON_UNESCAPED_SLASHES);

        $this->assertStringContainsString('Dave', $json);
        $this->assertStringContainsString('https://schema.org', $json);
    }
}