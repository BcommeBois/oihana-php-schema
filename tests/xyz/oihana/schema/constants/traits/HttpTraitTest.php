<?php

namespace tests\xyz\oihana\schema\constants\traits ;

use PHPUnit\Framework\TestCase;

use xyz\oihana\schema\constants\traits\HttpTrait;
use xyz\oihana\schema\constants\traits\http\UserAgentInfoTrait;

class HttpTraitTest extends TestCase
{
    public function testComposition(): void
    {
        $traits = class_uses( HttpTrait::class );

        $this->assertContains( UserAgentInfoTrait::class , $traits );
    }

    public function testComposesAllHttpTraits(): void
    {
        $host = new class { use HttpTrait; };
        $class = $host::class;

        // UserAgentInfoTrait
        $this->assertSame( 'browser'        , constant( "$class::BROWSER"         ) );
        $this->assertSame( 'browserVersion' , constant( "$class::BROWSER_VERSION" ) );
        $this->assertSame( 'deviceType'     , constant( "$class::DEVICE_TYPE"     ) );
        $this->assertSame( 'isBot'          , constant( "$class::IS_BOT"          ) );
        $this->assertSame( 'os'             , constant( "$class::OS"              ) );
        $this->assertSame( 'osVersion'      , constant( "$class::OS_VERSION"      ) );
        $this->assertSame( 'raw'            , constant( "$class::RAW"             ) );
    }
}
