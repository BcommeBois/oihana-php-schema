<?php

namespace tests\xyz\oihana\schema\constants\http ;

use xyz\oihana\schema\constants\http\DeviceType ;
use PHPUnit\Framework\TestCase ;

class DeviceTypeTest extends TestCase
{
    public function testConstantsExposeLowercaseValues()
    {
        $this->assertSame( 'bot'     , DeviceType::BOT     ) ;
        $this->assertSame( 'desktop' , DeviceType::DESKTOP ) ;
        $this->assertSame( 'mobile'  , DeviceType::MOBILE  ) ;
        $this->assertSame( 'tablet'  , DeviceType::TABLET  ) ;
        $this->assertSame( 'unknown' , DeviceType::UNKNOWN ) ;
    }

    public function testIncludesAcceptsKnownValues()
    {
        $this->assertTrue( DeviceType::includes( DeviceType::BOT     ) ) ;
        $this->assertTrue( DeviceType::includes( DeviceType::DESKTOP ) ) ;
        $this->assertTrue( DeviceType::includes( DeviceType::MOBILE  ) ) ;
        $this->assertTrue( DeviceType::includes( DeviceType::TABLET  ) ) ;
        $this->assertTrue( DeviceType::includes( DeviceType::UNKNOWN ) ) ;
    }

    public function testIncludesRejectsUnknownValues()
    {
        $this->assertFalse( DeviceType::includes( 'DESKTOP'   ) ) ; // case-sensitive
        $this->assertFalse( DeviceType::includes( 'wearable'  ) ) ;
        $this->assertFalse( DeviceType::includes( ''          ) ) ;
    }

    public function testEnumsListsAllValues()
    {
        $this->assertEqualsCanonicalizing
        (
            [ 'bot' , 'desktop' , 'mobile' , 'tablet' , 'unknown' ] ,
            DeviceType::enums() ,
        ) ;
    }
}
