<?php

namespace org\schema ;

use org\schema\constants\Prop;
use PHPUnit\Framework\TestCase;
use ReflectionException;

class AggregateOfferTest extends TestCase
{
    public function testConstructorWithNoArguments()
    {
        $thing = new AggregateOffer();

        $this->assertObjectHasProperty( Prop::NAME , $thing );
        $this->assertNull( $thing->name ?? null , 'The name property must be null by default');
    }

    public function testConstructorInitializesProperties()
    {
        $thing = new AggregateOffer( ['name' => 'Prix public'  ] );
        $this->assertSame('Prix public' , $thing->name );
    }

    /**
     * @throws ReflectionException
     */
    public function testJsonSerializeIncludesContextAndType()
    {
        $thing = new AggregateOffer( ['name' => 'Prix public' ] );

        $data = $thing->jsonSerialize();

        // echo var_dump($data) . PHP_EOL;

        $this->assertArrayHasKey(Prop::AT_TYPE    , $data ) ;
        $this->assertArrayHasKey(Prop::AT_CONTEXT , $data ) ;

        $this->assertEquals('Prix public' , $data[ Prop::NAME ] ) ;

        $this->assertEquals('AggregateOffer', $data[ Prop::AT_TYPE ] ) ;
        $this->assertEquals(Thing::CONTEXT , $data[ Prop::AT_CONTEXT ] ) ;
    }

    public function testJsonEncode()
    {
        $thing = [ new AggregateOffer( ['name' => 'Prix public' ] ) ];

        $json = json_encode($thing , JSON_UNESCAPED_SLASHES );

        $this->assertEquals('[{"@type":"AggregateOffer","@context":"https://schema.org","name":"Prix public"}]' , $json ) ;
    }
}

