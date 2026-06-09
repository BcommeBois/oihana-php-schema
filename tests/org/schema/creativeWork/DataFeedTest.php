<?php

namespace tests\org\schema\creativeWork ;

use org\schema\creativeWork\DataCatalog;
use org\schema\creativeWork\DataFeed;
use org\schema\creativeWork\Dataset;
use org\schema\Thing;

use PHPUnit\Framework\TestCase;

class DataFeedTest extends TestCase
{
    /**
     * Regression: DataFeed referenced its parent as `DataSet` while the actual
     * class is `Dataset` (the schema.org casing, https://schema.org/Dataset).
     * On a case-sensitive filesystem (Linux CI) the PSR-4 autoloader could not
     * find `DataSet.php` and the class load was a fatal error. The references
     * (DataFeed parent, DataCatalog::$dataset) now match the real class name.
     */
    public function testDataFeedExtendsDatasetAndLoads()
    {
        $feed = new DataFeed() ;

        $this->assertInstanceOf( Dataset::class , $feed ) ;
        $this->assertInstanceOf( Thing::class   , $feed ) ;
        $this->assertSame( 'https://schema.org/DataFeed' , DataFeed::getSchemaType() ) ;
        $this->assertSame( 'https://schema.org/Dataset'  , Dataset::getSchemaType() ) ;
    }

    public function testDataCatalogLoads()
    {
        $catalog = new DataCatalog() ;

        $this->assertInstanceOf( Thing::class , $catalog ) ;
        $this->assertSame( 'https://schema.org/DataCatalog' , DataCatalog::getSchemaType() ) ;
    }
}
