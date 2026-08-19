<?php

namespace tests\org\schema\creativeWork ;

use ReflectionException;

use org\schema\constants\Schema;
use org\schema\CreativeWork;
use org\schema\creativeWork\Dataset;
use org\schema\creativeWork\medias\DataDownload;
use org\schema\creativeWork\MediaObject;
use org\schema\StatisticalVariable;
use org\schema\Thing;

use PHPUnit\Framework\TestCase;

class DatasetTest extends TestCase
{
    /**
     * Regression : a Dataset is a body of structured information, not a media
     * file. Extending MediaObject handed it `contentUrl`, `contentSize`,
     * `bitrate` and the rest — terms schema.org does not publish on a Dataset,
     * and which serialized a dataset claiming to be a file. The file it offers
     * hangs off `distribution`, as a DataDownload.
     */
    public function testExtendsCreativeWorkAndNotMediaObject()
    {
        $dataset = new Dataset() ;

        $this->assertInstanceOf( CreativeWork::class , $dataset ) ;
        $this->assertInstanceOf( Thing::class        , $dataset ) ;

        $this->assertNotInstanceOf( MediaObject::class , $dataset ) ;

        $this->assertObjectNotHasProperty( 'contentUrl'  , $dataset ) ;
        $this->assertObjectNotHasProperty( 'contentSize' , $dataset ) ;
        $this->assertObjectNotHasProperty( 'bitrate'     , $dataset ) ;
    }

    public function testConstructorWithNoArguments()
    {
        $dataset = new Dataset() ;

        $this->assertObjectHasProperty( Schema::DISTRIBUTION , $dataset ) ;
        $this->assertNull( $dataset->distribution ?? null , 'The distribution property must be null by default' ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testConstructorInitializesProperties()
    {
        $download = new DataDownload([ Schema::ENCODING_FORMAT => 'text/csv' ]) ;

        $dataset = new Dataset
        ([
            Schema::NAME                  => 'Population, France' ,
            Schema::ISSN                  => '2049-3630'          ,
            Schema::DISTRIBUTION          => $download            ,
            Schema::MEASUREMENT_TECHNIQUE => 'census'             ,
        ]) ;

        $this->assertSame( 'Population, France' , $dataset->name                 ) ;
        $this->assertSame( '2049-3630'          , $dataset->issn                 ) ;
        $this->assertSame( $download            , $dataset->distribution         ) ;
        $this->assertSame( 'census'             , $dataset->measurementTechnique ) ;
    }

    /**
     * A dataset is published in as many formats as it has downloads, and the row
     * a store hands back is raw : a union naming the class alone rejected both.
     *
     * @throws ReflectionException
     */
    public function testConstructorAcceptsRawArrayValues()
    {
        $dataset = new Dataset
        ([
            Schema::DISTRIBUTION           => [ [ Schema::ENCODING_FORMAT => 'text/csv'         ] ,
                                                [ Schema::ENCODING_FORMAT => 'application/json' ] ] ,
            Schema::INCLUDED_IN_DATA_CATALOG => [ Schema::NAME => 'Data Commons' ] ,
            Schema::MEASUREMENT_METHOD       => [ Schema::NAME => 'Survey'       ] ,
            Schema::MEASUREMENT_TECHNIQUE    => [ Schema::NAME => 'Sampling'     ] ,
        ]) ;

        $this->assertCount( 2 , $dataset->distribution ) ;

        $this->assertIsArray( $dataset->includedInDataCatalog ) ;
        $this->assertIsArray( $dataset->measurementMethod     ) ;
        $this->assertIsArray( $dataset->measurementTechnique  ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testVariableMeasuredAcceptsAStatisticalVariable()
    {
        $variable = new StatisticalVariable([ Schema::STAT_TYPE => 'count' ]) ;

        $dataset = new Dataset([ Schema::VARIABLE_MEASURED => $variable ]) ;

        $this->assertSame( $variable , $dataset->variableMeasured ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testJsonSerializeIncludesContextAndType()
    {
        $dataset = new Dataset([ Schema::NAME => 'Population, France' ]) ;

        $data = $dataset->jsonSerialize() ;

        $this->assertArrayHasKey( Schema::AT_TYPE    , $data ) ;
        $this->assertArrayHasKey( Schema::AT_CONTEXT , $data ) ;

        $this->assertSame( 'Dataset'            , $data[ Schema::AT_TYPE    ] ) ;
        $this->assertSame( Thing::CONTEXT       , $data[ Schema::AT_CONTEXT ] ) ;
        $this->assertSame( 'Population, France' , $data[ Schema::NAME       ] ) ;
    }

    public function testGetSchemaTypeReturnsRootUri()
    {
        $this->assertSame( 'https://schema.org/Dataset' , Dataset::getSchemaType() ) ;
    }
}
