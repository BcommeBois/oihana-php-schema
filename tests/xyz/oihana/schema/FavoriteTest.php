<?php

namespace tests\xyz\oihana\schema ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use org\schema\Intangible;
use org\schema\Thing;
use org\schema\constants\Schema;

use xyz\oihana\schema\Favorite;
use xyz\oihana\schema\constants\Oihana;

class FavoriteTest extends TestCase
{
    public function testIsIntangible(): void
    {
        $favorite = new Favorite();

        $this->assertInstanceOf( Intangible::class , $favorite );
        $this->assertInstanceOf( Thing::class      , $favorite );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( Oihana::SCHEMA              , Favorite::CONTEXT );
        $this->assertSame( 'https://schema.oihana.xyz' , Favorite::CONTEXT );
    }

    public function testEdgeAndTypeDefaultToNull(): void
    {
        $favorite = new Favorite();

        $this->assertNull( $favorite->_from          ?? null );
        $this->assertNull( $favorite->_to            ?? null );
        $this->assertNull( $favorite->additionalType ?? null );
    }

    /**
     * @throws ReflectionException
     */
    public function testConstructorHydratesEdgeAndType(): void
    {
        $favorite = new Favorite
        ([
            Schema::_FROM           => 'users/72488862' ,
            Schema::_TO             => 'products/105997' ,
            Schema::ADDITIONAL_TYPE => 'products' ,
        ]);

        $this->assertSame( 'users/72488862'  , $favorite->_from );
        $this->assertSame( 'products/105997' , $favorite->_to );
        $this->assertSame( 'products'        , $favorite->additionalType );
    }

    /**
     * The edge constants resolve through the refactored
     * {@see \org\schema\constants\traits\ArangoDB} / {@see \org\schema\constants\traits\Edge} traits.
     */
    public function testArangoDbConstantValues(): void
    {
        $this->assertSame( '_from' , Schema::_FROM );
        $this->assertSame( '_to'   , Schema::_TO );
        $this->assertSame( '_id'   , Schema::_ID );
        $this->assertSame( '_key'  , Schema::_KEY );
        $this->assertSame( '_rev'  , Schema::_REV );
    }

    /**
     * @throws ReflectionException
     */
    public function testJsonSerializeExposesContextTypeAndEdge(): void
    {
        $favorite = new Favorite
        ([
            Schema::ADDITIONAL_TYPE => 'products' ,
            Schema::_FROM           => 'users/72488862' ,
            Schema::_TO             => 'products/105997' ,
            Schema::CREATED         => '2026-06-27T14:30:00+02:00' ,
        ]);

        $data = $favorite->jsonSerialize();

        $this->assertSame( 'Favorite'                  , $data[ Schema::AT_TYPE ] );
        $this->assertSame( 'https://schema.oihana.xyz' , $data[ Schema::AT_CONTEXT ] );

        $this->assertSame( 'products'                   , $data[ Schema::ADDITIONAL_TYPE ] );
        $this->assertSame( 'users/72488862'             , $data[ Schema::_FROM ] );
        $this->assertSame( 'products/105997'            , $data[ Schema::_TO ] );
        $this->assertSame( '2026-06-27T14:30:00+02:00'  , $data[ Schema::CREATED ] );
    }
}
