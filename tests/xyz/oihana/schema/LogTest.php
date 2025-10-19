<?php

namespace tests\xyz\oihana\schema ;

use xyz\oihana\schema\Log;
use PHPUnit\Framework\TestCase;

class LogTest extends TestCase
{
    public function testConstructorWithNoArguments()
    {
        $log = new Log();

        $this->assertObjectHasProperty(Log::DATE     , $log ) ;
        $this->assertObjectHasProperty(Log::TIME     , $log ) ;
        $this->assertObjectHasProperty(Log::LEVEL    , $log ) ;
        $this->assertObjectHasProperty(Log::MESSAGE  , $log ) ;

        $this->assertNull($log->date    ?? null ) ;
        $this->assertNull($log->time    ?? null ) ;
        $this->assertNull($log->level   ?? null ) ;
        $this->assertNull($log->message ?? null ) ;
    }
}
