<?php

namespace tests\xyz\oihana\schema\auth;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

use xyz\oihana\schema\auth\WebApplication;

class WebApplicationTest extends TestCase
{
    public function testDefaults(): void
    {
        $app = new WebApplication();

        $this->assertNull($app->clientId ?? null);
        $this->assertNull($app->applicationType ?? null);
        $this->assertNull($app->redirectUris ?? null);
        $this->assertNull($app->apiIdentifier ?? null);
        $this->assertNull($app->postLogoutRedirectUris ?? null);
        $this->assertNull($app->active ?? null);
    }

    #[DataProvider('stringProvider')]
    public function testStringProperties(?string $value): void
    {
        $app = new WebApplication();

        $app->clientId        = $value;
        $app->applicationType = $value;
        $app->apiIdentifier   = $value;

        $this->assertSame($value, $app->clientId);
        $this->assertSame($value, $app->applicationType);
        $this->assertSame($value, $app->apiIdentifier);
    }

    #[DataProvider('arrayProvider')]
    public function testArrayProperties(?array $value): void
    {
        $app = new WebApplication();

        $app->redirectUris           = $value;
        $app->postLogoutRedirectUris = $value;

        $this->assertSame($value, $app->redirectUris);
        $this->assertSame($value, $app->postLogoutRedirectUris);
    }

    #[DataProvider('booleanProvider')]
    public function testBooleanProperty(?bool $value): void
    {
        $app = new WebApplication();

        $app->active = $value;

        $this->assertSame($value, $app->active);
    }

    public function testInheritance(): void
    {
        $app = new WebApplication();

        // propriétés héritées de Thing / CreativeWork / SoftwareApplication
        $app->name = 'My App';
        $app->url  = 'https://example.com';
        $app->softwareVersion = '1.0.0';

        $this->assertSame('My App', $app->name);
        $this->assertSame('https://example.com', $app->url);
        $this->assertSame('1.0.0', $app->softwareVersion);
    }

    public function testHydration(): void
    {
        $data =
        [
            'clientId' => 'client-123',
            'applicationType' => 'spa',
            'redirectUris' => ['https://app/callback'],
            'apiIdentifier' => 'commerce-api',
            'postLogoutRedirectUris' => ['https://app/logout'],
            'active' => true,
        ];

        $app = new WebApplication($data);

        $this->assertSame($data['clientId'], $app->clientId);
        $this->assertSame($data['applicationType'], $app->applicationType);
        $this->assertSame($data['redirectUris'], $app->redirectUris);
        $this->assertSame($data['apiIdentifier'], $app->apiIdentifier);
        $this->assertSame($data['postLogoutRedirectUris'], $app->postLogoutRedirectUris);
        $this->assertSame($data['active'], $app->active);
    }

    // --- Data Providers ---

    public static function stringProvider(): array
    {
        return
        [
            [null],
            [''],
            ['test'],
            ['spa'],
            ['native'],
            ['m2m'],
            ['web'],
        ];
    }

    public static function arrayProvider(): array
    {
        return
        [
            [null],
            [[]],
            [['https://example.com/callback']],
            [['https://a.com', 'https://b.com']],
        ];
    }

    public static function booleanProvider(): array
    {
        return
        [
            [null],
            [true],
            [false],
        ];
    }
}