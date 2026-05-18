<?php

namespace tests\com\progress\schema\system ;

use com\progress\schema\constants\Progress ;
use com\progress\schema\system\DataType ;
use org\schema\Thing ;
use PHPUnit\Framework\TestCase ;
use ReflectionException ;

class DataTypeTest extends TestCase
{
    public function testDefaults(): void
    {
        $type = new DataType();

        $this->assertNull( $type->columnLength       ?? null );
        $this->assertNull( $type->dataTypePrecision  ?? null );
        $this->assertNull( $type->dataTypeRadix      ?? null );
        $this->assertNull( $type->typeCode           ?? null );
    }

    public function testIsThing(): void
    {
        $this->assertInstanceOf( Thing::class , new DataType() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( Progress::SCHEMA , DataType::CONTEXT );
    }

    /**
     * @throws ReflectionException
     */
    public function testHydration(): void
    {
        $type = new DataType
        ([
            'name'              => 'INTEGER' ,
            'typeCode'          => 4         ,
            'columnLength'      => 4         ,
            'dataTypePrecision' => 10        ,
            'dataTypeRadix'     => 10        ,
        ]);

        $this->assertSame( 'INTEGER' , $type->name              );
        $this->assertSame( 4         , $type->typeCode          );
        $this->assertSame( 4         , $type->columnLength      );
        $this->assertSame( 10        , $type->dataTypePrecision );
        $this->assertSame( 10        , $type->dataTypeRadix     );
    }
}
