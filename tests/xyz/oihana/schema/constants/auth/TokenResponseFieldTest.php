<?php

namespace tests\xyz\oihana\schema\constants\auth ;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

use xyz\oihana\schema\constants\auth\TokenResponseField;

class TokenResponseFieldTest extends TestCase
{
    public function testConstantsValues(): void
    {
        $expected =
        [
            'ACCESS_TOKEN' => 'access_token' ,
            'EXPIRES_IN'   => 'expires_in'   ,
            'SCOPE'        => 'scope'        ,
            'TOKEN_TYPE'   => 'token_type'   ,
        ];

        foreach ( $expected as $name => $value )
        {
            $this->assertSame( $value , constant( TokenResponseField::class . "::$name" ) );
        }
    }

    #[DataProvider('tokenResponseFieldProvider')]
    public function testValidFields( string $field ): void
    {
        $this->assertContains( $field ,
        [
            TokenResponseField::ACCESS_TOKEN ,
            TokenResponseField::EXPIRES_IN   ,
            TokenResponseField::SCOPE        ,
            TokenResponseField::TOKEN_TYPE   ,
        ]);
    }

    public static function tokenResponseFieldProvider(): array
    {
        return
        [
            [ TokenResponseField::ACCESS_TOKEN ] ,
            [ TokenResponseField::EXPIRES_IN   ] ,
            [ TokenResponseField::SCOPE        ] ,
            [ TokenResponseField::TOKEN_TYPE   ] ,
        ];
    }

    public function testIncludesViaConstantsTrait(): void
    {
        $this->assertTrue ( TokenResponseField::includes( 'access_token' ) );
        $this->assertTrue ( TokenResponseField::includes( 'expires_in'   ) );
        $this->assertTrue ( TokenResponseField::includes( 'scope'        ) );
        $this->assertTrue ( TokenResponseField::includes( 'token_type'   ) );
        $this->assertFalse( TokenResponseField::includes( 'unknown'      ) );
    }

    public function testGetConstantValuesViaConstantsTrait(): void
    {
        $values = TokenResponseField::getConstantValues();

        $this->assertContains( 'access_token' , $values );
        $this->assertContains( 'expires_in'   , $values );
        $this->assertContains( 'scope'        , $values );
        $this->assertContains( 'token_type'   , $values );
    }
}
