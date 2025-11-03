<?php

namespace tests\xyz\oihana\schema\auth ;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

use xyz\oihana\schema\auth\WebAPI;
use xyz\oihana\schema\constants\JWTAlgorithm;

class WebAPITest extends TestCase
{
    public function testDefaults(): void
    {
        $api = new WebAPI();

        // Constantes
        $this->assertSame(JWTAlgorithm::RS256, WebAPI::DEFAULT_ALGORITHM);
        $this->assertSame(7200, WebAPI::DEFAULT_IMPLICIT_HYBRID_TOKEN_LIFETIME);
        $this->assertSame(86400, WebAPI::DEFAULT_TOKEN_EXPIRATION);

        // Propriétés par défaut
        $this->assertSame(WebAPI::DEFAULT_ALGORITHM, $api->algorithm);
        $this->assertSame(WebAPI::DEFAULT_IMPLICIT_HYBRID_TOKEN_LIFETIME, $api->implicitHybridTokenLifetime);
        $this->assertSame(WebAPI::DEFAULT_TOKEN_EXPIRATION, $api->maximumAccessTokenExpiration);

        $this->assertNull( $api->allowOfflineAccess ?? null );
        $this->assertNull( $api->allowSkipUserConsent ?? null  );
        $this->assertNull( $api->permissions ?? null  );
        $this->assertNull( $api->permissionsCount ?? null );
        $this->assertNull( $api->rbac ?? null );
        $this->assertNull( $api->scopeHasPermission ?? null );
    }

    #[DataProvider('booleanProvider')]
    public function testBooleanProperties(bool $value): void
    {
        $api = new WebAPI();

        $api->allowOfflineAccess = $value;
        $api->allowSkipUserConsent = $value;
        $api->rbac = $value;
        $api->scopeHasPermission = $value;

        $this->assertSame($value, $api->allowOfflineAccess);
        $this->assertSame($value, $api->allowSkipUserConsent);
        $this->assertSame($value, $api->rbac);
        $this->assertSame($value, $api->scopeHasPermission);
    }

    #[DataProvider('permissionsProvider')]
    public function testPermissions(array $permissions): void
    {
        $api = new WebAPI();
        $api->permissions = $permissions;
        $api->permissionsCount = count($permissions);

        $this->assertSame($permissions, $api->permissions);
        $this->assertSame(count($permissions), $api->permissionsCount);
    }

    #[DataProvider('jwtAlgorithmProvider')]
    public function testAlgorithm(string $algorithm): void
    {
        $api = new WebAPI();
        $api->algorithm = $algorithm;

        $this->assertSame($algorithm, $api->algorithm);
        $this->assertContains($api->algorithm, [
            JWTAlgorithm::HS256, JWTAlgorithm::HS384, JWTAlgorithm::HS512,
            JWTAlgorithm::RS256, JWTAlgorithm::RS384, JWTAlgorithm::RS512,
            JWTAlgorithm::PS256, JWTAlgorithm::PS384, JWTAlgorithm::PS512,
        ]);
    }

    #[DataProvider('tokenLifetimeProvider')]
    public function testTokenLifetimes(int $implicit, int $maximum): void
    {
        $api = new WebAPI();
        $api->implicitHybridTokenLifetime = $implicit;
        $api->maximumAccessTokenExpiration = $maximum;

        $this->assertSame($implicit, $api->implicitHybridTokenLifetime);
        $this->assertSame($maximum, $api->maximumAccessTokenExpiration);

        // Implicit lifetime cannot exceed maximum
        $this->assertLessThanOrEqual($api->maximumAccessTokenExpiration, $api->implicitHybridTokenLifetime);
    }

    // --- Data Providers ---

    public static function booleanProvider(): array
    {
        return [
            [true],
            [false],
        ];
    }

    public static function permissionsProvider(): array
    {
        return [
            [[]],
            [['read']],
            [['read', 'write', 'delete']],
        ];
    }

    public static function jwtAlgorithmProvider(): array
    {
        return [
            [ JWTAlgorithm::HS256 ],
            [ JWTAlgorithm::HS384 ],
            [ JWTAlgorithm::HS512 ],
            [ JWTAlgorithm::RS256 ],
            [ JWTAlgorithm::RS384 ],
            [ JWTAlgorithm::RS512 ],
            [ JWTAlgorithm::PS256 ],
            [ JWTAlgorithm::PS384 ],
            [ JWTAlgorithm::PS512 ],
        ];
    }

    public static function tokenLifetimeProvider(): array
    {
        return [
            [3600, 86400],
            [7200, 7200],
            [1800, 3600],
        ];
    }
}
