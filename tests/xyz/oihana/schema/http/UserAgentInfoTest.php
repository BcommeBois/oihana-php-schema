<?php

namespace tests\xyz\oihana\schema\http ;

use xyz\oihana\schema\http\UserAgentInfo ;
use xyz\oihana\schema\constants\http\DeviceType ;
use xyz\oihana\schema\constants\Oihana ;

use org\schema\constants\Schema ;

use PHPUnit\Framework\TestCase ;
use ReflectionException ;

class UserAgentInfoTest extends TestCase
{
    public function testConstructorWithNoArgumentsExposesAllPropertiesAsNull()
    {
        $info = new UserAgentInfo() ;

        $this->assertObjectHasProperty( UserAgentInfo::BROWSER         , $info ) ;
        $this->assertObjectHasProperty( UserAgentInfo::BROWSER_VERSION , $info ) ;
        $this->assertObjectHasProperty( UserAgentInfo::OS              , $info ) ;
        $this->assertObjectHasProperty( UserAgentInfo::OS_VERSION      , $info ) ;
        $this->assertObjectHasProperty( UserAgentInfo::DEVICE_TYPE     , $info ) ;
        $this->assertObjectHasProperty( UserAgentInfo::IS_BOT          , $info ) ;
        $this->assertObjectHasProperty( UserAgentInfo::RAW             , $info ) ;

        $this->assertNull( $info->browser        ?? null ) ;
        $this->assertNull( $info->browserVersion ?? null ) ;
        $this->assertNull( $info->os             ?? null ) ;
        $this->assertNull( $info->osVersion      ?? null ) ;
        $this->assertNull( $info->deviceType     ?? null ) ;
        $this->assertNull( $info->isBot          ?? null ) ;
        $this->assertNull( $info->raw            ?? null ) ;
    }

    public function testConstructorInitializesProperties()
    {
        $info = new UserAgentInfo
        ([
            UserAgentInfo::BROWSER         => 'Chrome'  ,
            UserAgentInfo::BROWSER_VERSION => '126.0'   ,
            UserAgentInfo::OS              => 'macOS'   ,
            UserAgentInfo::OS_VERSION      => '14.5'    ,
            UserAgentInfo::DEVICE_TYPE     => DeviceType::DESKTOP ,
            UserAgentInfo::IS_BOT          => false     ,
            UserAgentInfo::RAW             => 'Mozilla/5.0 …' ,
        ]) ;

        $this->assertSame( 'Chrome'         , $info->browser        ) ;
        $this->assertSame( '126.0'          , $info->browserVersion ) ;
        $this->assertSame( 'macOS'          , $info->os             ) ;
        $this->assertSame( '14.5'           , $info->osVersion      ) ;
        $this->assertSame( DeviceType::DESKTOP , $info->deviceType  ) ;
        $this->assertFalse( $info->isBot ) ;
        $this->assertSame( 'Mozilla/5.0 …'  , $info->raw            ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testJsonSerializeIncludesContextAndType()
    {
        $info = new UserAgentInfo
        ([
            UserAgentInfo::BROWSER     => 'Chrome'  ,
            UserAgentInfo::DEVICE_TYPE => DeviceType::DESKTOP ,
        ]) ;

        $data = $info->jsonSerialize() ;

        $this->assertArrayHasKey( Schema::AT_TYPE    , $data ) ;
        $this->assertArrayHasKey( Schema::AT_CONTEXT , $data ) ;

        $this->assertEquals( 'UserAgentInfo'        , $data[ Schema::AT_TYPE    ] ) ;
        $this->assertEquals( UserAgentInfo::CONTEXT , $data[ Schema::AT_CONTEXT ] ) ;

        $this->assertEquals( 'Chrome'           , $data[ UserAgentInfo::BROWSER     ] ) ;
        $this->assertEquals( DeviceType::DESKTOP , $data[ UserAgentInfo::DEVICE_TYPE ] ) ;

        $this->assertArrayNotHasKey
        (
            UserAgentInfo::OS ,
            $data ,
            'Null properties should be omitted from the JSON-LD serialisation' ,
        ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testBotFlagSerializedCorrectly()
    {
        $info = new UserAgentInfo
        ([
            UserAgentInfo::IS_BOT      => true ,
            UserAgentInfo::DEVICE_TYPE => DeviceType::BOT ,
            UserAgentInfo::RAW         => 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)' ,
        ]) ;

        $data = $info->jsonSerialize() ;

        $this->assertTrue( $data[ UserAgentInfo::IS_BOT ] ) ;
        $this->assertSame( DeviceType::BOT , $data[ UserAgentInfo::DEVICE_TYPE ] ) ;
    }
}
