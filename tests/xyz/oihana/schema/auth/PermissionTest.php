<?php

namespace tests\xyz\oihana\schema\auth ;

use PHPUnit\Framework\TestCase;
use ReflectionException;
use xyz\oihana\schema\auth\Permission;
use xyz\oihana\schema\constants\CasbinPolicy;
use xyz\oihana\schema\constants\Effect;
use xyz\oihana\schema\constants\Oihana;

class PermissionTest extends TestCase
{
    public function testDefaultValues(): void
    {
        $perm = new Permission();

        $this->assertEquals( Effect::ALLOW , $perm->effect ) ;

        $this->assertNull( $perm->action  ?? null , 'Default action should be null'  );
        $this->assertNull( $perm->domain  ?? null , 'Default domain should be null'  );
        $this->assertNull( $perm->object  ?? null , 'Default object should be null'  );
        $this->assertNull( $perm->subject ?? null , 'Default subject should be null' );
    }

    /**
     * @throws ReflectionException
     */
    public function testSetAndGetProperties(): void
    {
        $perm = new Permission
        ([
            Oihana::ACTION  => 'read' ,
            Oihana::DOMAIN  => 'project' ,
            Oihana::OBJECT  => 'places/:id' ,
            Oihana::SUBJECT => 'role:admin' ,
        ]);

        $this->assertSame('read'       , $perm->action  ) ;
        $this->assertSame('project'    , $perm->domain  ) ;
        $this->assertSame('places/:id' , $perm->object  ) ;
        $this->assertSame('role:admin' , $perm->subject ) ;

        // test effect normalization
        $perm->effect = 'allow';
        $this->assertSame(Effect::ALLOW, $perm->effect);

        $perm->effect = 'deny';
        $this->assertSame(Effect::DENY, $perm->effect);

        $perm->effect = 'invalid';
        $this->assertSame(Effect::ALLOW, $perm->effect, 'Invalid effect should fallback to ALLOW');
    }

    /**
     * @throws ReflectionException
     */
    public function testToArray(): void
    {
        $perm = new Permission
        ([
            Oihana::ACTION  => 'read' ,
            Oihana::DOMAIN  => 'project' ,
            Oihana::OBJECT  => 'places/:id' ,
            Oihana::SUBJECT => 'role:admin' ,
        ]);

        $expected = [
            Oihana::SUBJECT => 'role:admin',
            Oihana::DOMAIN  => 'project',
            Oihana::OBJECT  => 'places/:id',
            Oihana::ACTION  => 'read',
            Oihana::EFFECT  => 'allow',
        ];

        $this->assertSame($expected, $perm->toArray());
    }

    /**
     * @throws ReflectionException
     */
    public function testToPolicy(): void
    {
        $perm = new Permission([
            Oihana::ACTION  => 'GET|POST',
            Oihana::DOMAIN  => 'api',
            Oihana::OBJECT  => '/places/*',
            Oihana::SUBJECT => 'role:admin',
        ]);

        $expected =
        [
            CasbinPolicy::SUBJECT => 'role:admin',
            CasbinPolicy::DOMAIN  => 'api',
            CasbinPolicy::OBJECT  => '/places/*',
            CasbinPolicy::ACTION  => 'GET|POST',
            CasbinPolicy::EFFECT  => Effect::ALLOW,
        ];

        $this->assertSame($expected, $perm->toPolicy());
    }

    /**
     * @throws ReflectionException
     */
    public function testJsonSerialize(): void
    {
        $perm = new Permission
        ([
            Oihana::ACTION  => 'GET|POST',
            Oihana::DOMAIN  => 'api',
            Oihana::OBJECT  => '/places/*',
            Oihana::SUBJECT => 'role:admin',
        ]);

        $expected =
        [
            '@type'   =>'Permission',
            '@context'=>'https://schema.oihana.xyz',
            'action'  =>'GET|POST',
            'domain'  =>'api',
            'effect'  =>'allow',
            'object'  =>'/places/*',
            'subject' =>'role:admin',
        ];

        $this->assertSame($expected, $perm->jsonSerialize());
    }
}
