<?php

namespace tests\com\progress\schema\system ;

use com\progress\schema\constants\Progress ;
use com\progress\schema\system\View ;
use org\schema\Thing ;
use PHPUnit\Framework\TestCase ;
use ReflectionException ;

class ViewTest extends TestCase
{
    public function testDefaults(): void
    {
        $view = new View();

        $this->assertNull( $view->checkOption ?? null );
        $this->assertNull( $view->creator     ?? null );
        $this->assertNull( $view->owner       ?? null );
        $this->assertNull( $view->textLength  ?? null );
        $this->assertNull( $view->viewText    ?? null );
    }

    public function testIsThing(): void
    {
        $this->assertInstanceOf( Thing::class , new View() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( Progress::SCHEMA , View::CONTEXT );
    }

    /**
     * @throws ReflectionException
     */
    public function testHydration(): void
    {
        $view = new View
        ([
            'name'        => 'ActiveCustomers' ,
            'owner'       => 'PUB'             ,
            'creator'     => 'sysprogress'     ,
            'checkOption' => true              ,
            'textLength'  => 64                ,
            'viewText'    => 'CREATE VIEW ActiveCustomers AS SELECT * FROM Customer WHERE Active = TRUE' ,
        ]);

        $this->assertSame( 'ActiveCustomers' , $view->name        );
        $this->assertSame( 'PUB'             , $view->owner       );
        $this->assertSame( 'sysprogress'     , $view->creator     );
        $this->assertTrue (                    $view->checkOption );
        $this->assertSame( 64                , $view->textLength  );
        $this->assertStringContainsString( 'CREATE VIEW' , $view->viewText );
    }
}
