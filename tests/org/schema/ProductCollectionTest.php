<?php

namespace tests\org\schema ;

use org\schema\ProductCollection;
use org\schema\Thing;

use PHPUnit\Framework\TestCase;

class ProductCollectionTest extends TestCase
{
    /**
     * Regression: ProductCollection extends Product (which declares
     * `$funding`) and also uses CreativeWorkTrait (which declared a
     * differently-typed `$funding`). The incompatible composition made the
     * class impossible to load (fatal error). Both declarations are now
     * `null|string|array|Grant`, so the class composes and instantiates.
     */
    public function testProductCollectionLoadsAndSerializes()
    {
        $collection = new ProductCollection() ;

        $this->assertInstanceOf( Thing::class , $collection ) ;
        $this->assertIsArray( $collection->jsonSerialize() ) ;
        $this->assertSame( 'https://schema.org/ProductCollection' , ProductCollection::getSchemaType() ) ;
    }
}
