<?php

namespace tests\xyz\oihana\schema\auth ;

use PHPUnit\Framework\TestCase;
use xyz\oihana\schema\auth\Permission;
use xyz\oihana\schema\constants\Oihana;

class PermissionTest extends TestCase
{
    public function testDefaultValues(): void
    {
        $perm = new Permission();

        $this->assertNull($perm->action, 'Default action should be null');
        $this->assertNull($perm->domain, 'Default domain should be null');
        $this->assertNull($perm->subject, 'Default subject should be null');
    }

    public function testSetAndGetProperties(): void
    {
        $perm = new Permission();
        $perm->action = 'read';
        $perm->domain = 'project';
        $perm->subject = 'admin';

        $this->assertSame('read', $perm->action);
        $this->assertSame('project', $perm->domain);
        $this->assertSame('admin', $perm->subject);
    }

    public function testToArray(): void
    {
        $perm = new Permission();
        $perm->action = 'write';
        $perm->domain = 'document';
        $perm->subject = 'user:123';

        $expected = [
            Oihana::SUBJECT => 'user:123',
            Oihana::DOMAIN  => 'document',
            Oihana::ACTION  => 'write',
        ];

        $this->assertSame($expected, $perm->toArray());
    }

    public function provideValidPermissions(): array
    {
        return [
            ['read', 'project', 'admin'],
            ['write', 'document', 'user:123'],
            ['delete', 'api', 'role:editor'],
            [null, null, null],
        ];
    }

    public function testDataProviderPermissions(): void
    {
        foreach ( $this->provideValidPermissions() as [$action, $domain, $subject] )
        {
            $perm = new Permission() ;
            $perm->action  = $action;
            $perm->domain  = $domain;
            $perm->subject = $subject;

            $this->assertSame($action, $perm->action);
            $this->assertSame($domain, $perm->domain);
            $this->assertSame($subject, $perm->subject);

            $array = $perm->toArray();
            $this->assertSame($subject, $array[Oihana::SUBJECT]);
            $this->assertSame($domain, $array[Oihana::DOMAIN]);
            $this->assertSame($action, $array[Oihana::ACTION]);
        }
    }
}
