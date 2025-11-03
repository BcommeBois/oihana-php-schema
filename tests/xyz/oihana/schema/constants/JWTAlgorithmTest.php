<?php

namespace tests\xyz\oihana\schema\constants ;

use xyz\oihana\schema\constants\JWTAlgorithm;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class JWTAlgorithmTest extends TestCase
{
    #[DataProvider('symmetricAlgorithmsProvider')]
    public function testIsSymmetric(string $algorithm): void
    {
        $this->assertTrue(JWTAlgorithm::isSymmetric($algorithm));
        $this->assertFalse(JWTAlgorithm::isAsymmetric($algorithm));
    }

    #[DataProvider('asymmetricAlgorithmsProvider')]
    public function testIsAsymmetric(string $algorithm): void
    {
        $this->assertTrue(JWTAlgorithm::isAsymmetric($algorithm));
        $this->assertFalse(JWTAlgorithm::isSymmetric($algorithm));
    }

    public function testNoneIsNeitherSymmetricNorAsymmetric(): void
    {
        $this->assertFalse(JWTAlgorithm::isSymmetric(JWTAlgorithm::NONE));
        $this->assertFalse(JWTAlgorithm::isAsymmetric(JWTAlgorithm::NONE));
    }

    public function testConstantsValues(): void
    {
        $expectedConstants = [
            'HS256' => 'HS256',
            'HS384' => 'HS384',
            'HS512' => 'HS512',
            'NONE'  => 'none',
            'PS256' => 'PS256',
            'PS384' => 'PS384',
            'PS512' => 'PS512',
            'RS256' => 'RS256',
            'RS384' => 'RS384',
            'RS512' => 'RS512',
        ];

        foreach ($expectedConstants as $name => $value) {
            $this->assertSame($value, constant(JWTAlgorithm::class . "::$name"));
        }
    }

    // --- Data Providers ---

    public static function symmetricAlgorithmsProvider(): array
    {
        return [
            [JWTAlgorithm::HS256],
            [JWTAlgorithm::HS384],
            [JWTAlgorithm::HS512],
        ];
    }

    public static function asymmetricAlgorithmsProvider(): array
    {
        return [
            [JWTAlgorithm::RS256],
            [JWTAlgorithm::RS384],
            [JWTAlgorithm::RS512],
            [JWTAlgorithm::PS256],
            [JWTAlgorithm::PS384],
            [JWTAlgorithm::PS512],
        ];
    }
}
