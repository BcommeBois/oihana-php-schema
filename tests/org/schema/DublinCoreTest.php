<?php

namespace tests\org\schema ;

use ReflectionException;

use org\schema\DublinCore;

use PHPUnit\Framework\TestCase;

class DublinCoreTest extends TestCase
{
    /**
     * @throws ReflectionException
     */
    public function testConstructWithArray()
    {
        $data = [
            DublinCore::TITLE => 'Test Resource',
            DublinCore::CREATOR => 'John Doe',
            DublinCore::DESCRIPTION => 'A test description',
        ];

        $dc = new DublinCore($data);

        $this->assertSame('Test Resource', $dc->title);
        $this->assertSame('John Doe', $dc->creator);
        $this->assertSame('A test description', $dc->description);
    }

    /**
     * @throws ReflectionException
     */
    public function testJsonSerializeDefaults()
    {
        $dc = new DublinCore
        ([
            DublinCore::TITLE   => 'Sample',
            DublinCore::CREATOR => 'Alice'
        ]);

        $json = $dc->jsonSerialize();

        $this->assertArrayHasKey('@type'    , $json ) ;
        $this->assertArrayHasKey('@context' , $json ) ;
        $this->assertSame(DublinCore::CONTEXT, $json['@context']);
        $this->assertSame('DublinCore' , $json['@type']); // Class short name
        $this->assertSame('Sample'     , $json['title']);
        $this->assertSame('Alice'      , $json['creator']);
    }

    /**
     * @throws ReflectionException
     */
    public function testWithAtContext()
    {
        $dc = new DublinCore();
        $dc->withAtContext('https://example.com/context');

        $json = $dc->jsonSerialize();
        $this->assertSame('https://example.com/context', $json['@context']);
    }

    /**
     * @throws ReflectionException
     */
    public function testWithAtType()
    {
        $dc = new DublinCore();
        $dc->withAtType('CustomType');

        $json = $dc->jsonSerialize();
        $this->assertSame('CustomType', $json['@type']);
    }

    /**
     * @throws ReflectionException
     */
    public function testWithJsonLdMeta()
    {
        $dc = new DublinCore();
        $dc->withJSONLDMeta('CustomType', 'https://example.com/context');

        $json = $dc->jsonSerialize();
        $this->assertSame('CustomType', $json['@type']);
        $this->assertSame('https://example.com/context', $json['@context']);
    }

    /**
     * @throws ReflectionException
     */
    public function testEmptyPropertiesAreExcludedFromJson()
    {
        $dc = new DublinCore();
        $json = $dc->jsonSerialize();

        // Les propriétés null ne doivent pas apparaître
        $this->assertArrayNotHasKey('title', $json);
        $this->assertArrayNotHasKey('creator', $json);
    }

    public function testGetSchemaTypeReturnsRootUri()
    {
        $this->assertEquals('http://purl.org/dc/DublinCore', DublinCore::getSchemaType());
    }
}

