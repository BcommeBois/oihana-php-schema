<?php

namespace tests\xyz\oihana\schema\constants;

use PHPUnit\Framework\TestCase;

use xyz\oihana\schema\constants\CasbinPolicy;

class CasbinPolicyTest extends TestCase
{
    public function testConstantsValues(): void
    {
        $expected =
        [
            'ACTION'  => 'act',
            'DOMAIN'  => 'dom',
            'EFFECT'  => 'eft',
            'OBJECT'  => 'obj',
            'SUBJECT' => 'sub',
        ];

        foreach ($expected as $name => $value)
        {
            $this->assertSame($value, constant(CasbinPolicy::class . "::$name"));
        }
    }

    public function testPolicyStructure(): void
    {
        $policy =
        [
            CasbinPolicy::SUBJECT => 'user:123',
            CasbinPolicy::DOMAIN  => 'commerce-api',
            CasbinPolicy::OBJECT  => '/products',
            CasbinPolicy::ACTION  => 'GET',
            CasbinPolicy::EFFECT  => 'allow',
        ];

        $this->assertArrayHasKey(CasbinPolicy::SUBJECT, $policy);
        $this->assertArrayHasKey(CasbinPolicy::DOMAIN,  $policy);
        $this->assertArrayHasKey(CasbinPolicy::OBJECT,  $policy);
        $this->assertArrayHasKey(CasbinPolicy::ACTION,  $policy);
        $this->assertArrayHasKey(CasbinPolicy::EFFECT,  $policy);

        $this->assertSame('user:123', $policy[CasbinPolicy::SUBJECT]);
        $this->assertSame('commerce-api', $policy[CasbinPolicy::DOMAIN]);
        $this->assertSame('/products', $policy[CasbinPolicy::OBJECT]);
        $this->assertSame('GET', $policy[CasbinPolicy::ACTION]);
        $this->assertSame('allow', $policy[CasbinPolicy::EFFECT]);
    }

    public function testEffectValues(): void
    {
        $this->assertContains('allow', ['allow', 'deny']);
        $this->assertContains('deny',  ['allow', 'deny']);
    }
}