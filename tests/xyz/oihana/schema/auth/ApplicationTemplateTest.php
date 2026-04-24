<?php

namespace tests\xyz\oihana\schema\auth ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use xyz\oihana\schema\auth\ApplicationTemplate;
use xyz\oihana\schema\auth\Scope;


class ApplicationTemplateTest extends TestCase
{
    public function testDefaults(): void
    {
        $template = new ApplicationTemplate();

        $this->assertNull( $template->color        ?? null );
        $this->assertNull( $template->protected    ?? null );
        $this->assertNull( $template->scopes       ?? null );
        $this->assertNull( $template->scopesCount  ?? null );
        $this->assertNull( $template->selfService  ?? null );
        $this->assertNull( $template->system       ?? null );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( 'https://schema.oihana.xyz' , ApplicationTemplate::CONTEXT );
    }

    /**
     * @throws ReflectionException
     */
    public function testHydration(): void
    {
        $template = new ApplicationTemplate
        ([
            'color'       => '#ff0000' ,
            'protected'   => true ,
            'scopesCount' => 3 ,
            'selfService' => true ,
            'system'      => false ,
        ]);

        $this->assertSame( '#ff0000' , $template->color );
        $this->assertTrue ( $template->protected );
        $this->assertSame( 3 , $template->scopesCount );
        $this->assertTrue ( $template->selfService );
        $this->assertFalse( $template->system );
    }

    public function testScopesAssignment(): void
    {
        $template = new ApplicationTemplate();

        $template->scopes = [ new Scope() , new Scope() ];

        $this->assertCount( 2 , $template->scopes );
        $this->assertContainsOnlyInstancesOf( Scope::class , $template->scopes );
    }
}
