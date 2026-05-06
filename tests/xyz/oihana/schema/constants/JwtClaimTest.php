<?php

namespace tests\xyz\oihana\schema\constants ;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

use xyz\oihana\schema\constants\JwtClaim;

class JwtClaimTest extends TestCase
{
    public function testConstantsValues(): void
    {
        $expected =
        [
            'AUDIENCE'   => 'aud' ,
            'EXPIRES_AT' => 'exp' ,
            'ISSUED_AT'  => 'iat' ,
            'ISSUER'     => 'iss' ,
            'SUBJECT'    => 'sub' ,
        ];

        foreach ( $expected as $name => $value )
        {
            $this->assertSame( $value , constant( JwtClaim::class . "::$name" ) );
        }
    }

    #[DataProvider('jwtClaimProvider')]
    public function testValidClaims( string $claim ): void
    {
        $this->assertContains( $claim ,
        [
            JwtClaim::AUDIENCE   ,
            JwtClaim::EXPIRES_AT ,
            JwtClaim::ISSUED_AT  ,
            JwtClaim::ISSUER     ,
            JwtClaim::SUBJECT    ,
        ]);
    }

    public static function jwtClaimProvider(): array
    {
        return
        [
            [ JwtClaim::AUDIENCE   ] ,
            [ JwtClaim::EXPIRES_AT ] ,
            [ JwtClaim::ISSUED_AT  ] ,
            [ JwtClaim::ISSUER     ] ,
            [ JwtClaim::SUBJECT    ] ,
        ];
    }

    public function testIncludesViaConstantsTrait(): void
    {
        $this->assertTrue ( JwtClaim::includes( 'aud'     ) );
        $this->assertTrue ( JwtClaim::includes( 'exp'     ) );
        $this->assertTrue ( JwtClaim::includes( 'iat'     ) );
        $this->assertTrue ( JwtClaim::includes( 'iss'     ) );
        $this->assertTrue ( JwtClaim::includes( 'sub'     ) );
        $this->assertFalse( JwtClaim::includes( 'unknown' ) );
    }

    public function testGetConstantValuesViaConstantsTrait(): void
    {
        $values = JwtClaim::getConstantValues();

        $this->assertContains( 'aud' , $values );
        $this->assertContains( 'exp' , $values );
        $this->assertContains( 'iat' , $values );
        $this->assertContains( 'iss' , $values );
        $this->assertContains( 'sub' , $values );
    }
}
