<?php

namespace tests\fr\ooop\schema ;

use fr\ooop\schema\Pagination;
use fr\ooop\schema\constants\Ooop;

use org\schema\constants\Schema;

use PHPUnit\Framework\TestCase;
use ReflectionException;

class PaginationTest extends TestCase
{
    public function testConstructorWithNoArguments()
    {
        $pagination = new Pagination();

        // Properties should exist and be null by default
        $this->assertObjectHasProperty(Pagination::LIMIT           , $pagination ) ;
        $this->assertObjectHasProperty(Pagination::MAX_LIMIT       , $pagination ) ;
        $this->assertObjectHasProperty(Pagination::MIN_LIMIT       , $pagination ) ;
        $this->assertObjectHasProperty(Pagination::OFFSET          , $pagination ) ;
        $this->assertObjectHasProperty(Pagination::NUMBER_OF_PAGES , $pagination ) ;
        $this->assertObjectHasProperty(Pagination::PAGE            , $pagination ) ;

        $this->assertNull($pagination->limit         ?? null ) ;
        $this->assertNull($pagination->maxLimit      ?? null ) ;
        $this->assertNull($pagination->minLimit      ?? null ) ;
        $this->assertNull($pagination->offset        ?? null ) ;
        $this->assertNull($pagination->numberOfPages ?? null ) ;
        $this->assertNull($pagination->page          ?? null ) ;
    }

    public function testConstructorInitializesProperties()
    {
        $pagination = new Pagination
        ([
            Pagination::LIMIT           => 10 ,
            Pagination::OFFSET          => 20 ,
            Pagination::MAX_LIMIT       => 100 ,
            Pagination::NUMBER_OF_PAGES => 500 ,
            Pagination::PAGE            => 5
        ]);

        $this->assertSame(10,  $pagination->limit);
        $this->assertSame(20,  $pagination->offset);
        $this->assertSame(100, $pagination->maxLimit);
        $this->assertSame(500, $pagination->numberOfPages);
        $this->assertNull($pagination->minLimit ?? null);
    }

    /**
     * @throws ReflectionException
     */
    public function testJsonSerializeIncludesContextAndType()
    {
        $pagination = new Pagination
        ([
            Ooop::LIMIT => 50,
        ]);

        $data = $pagination->jsonSerialize();

        $this->assertArrayHasKey(Schema::AT_TYPE, $data);
        $this->assertArrayHasKey(Schema::AT_CONTEXT, $data);

        $this->assertEquals('Pagination', $data[ Schema::AT_TYPE ]);
        $this->assertEquals(Pagination::CONTEXT, $data[ Schema::AT_CONTEXT ]);

        $this->assertEquals(50, $data[ Ooop::LIMIT ]);
        $this->assertArrayNotHasKey(Ooop::MIN_LIMIT, $data, 'Null properties should be omitted');
    }

    public function testJsonEncode()
    {
        $items = [ new Pagination([ Ooop::LIMIT => 50 ]) ];

        $json = json_encode($items, JSON_UNESCAPED_SLASHES);

        $this->assertEquals('[{"@type":"Pagination","@context":"https://schema.ooop.fr","limit":50}]', $json);
    }
}
