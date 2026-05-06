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
            'ACCESS_TOKEN'  => 'access_token'  ,
            'ASSERTION'     => 'assertion'     ,
            'EXPIRES_AT'    => 'expires_at'    ,
            'EXPIRES_IN'    => 'expires_in'    ,
            'ID_TOKEN'      => 'id_token'      ,
            'REFRESH_TOKEN' => 'refresh_token' ,
            'SCOPE'         => 'scope'         ,
            'TOKEN_TYPE'    => 'token_type'    ,
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
            TokenResponseField::ACCESS_TOKEN  ,
            TokenResponseField::ASSERTION     ,
            TokenResponseField::EXPIRES_AT    ,
            TokenResponseField::EXPIRES_IN    ,
            TokenResponseField::ID_TOKEN      ,
            TokenResponseField::REFRESH_TOKEN ,
            TokenResponseField::SCOPE         ,
            TokenResponseField::TOKEN_TYPE    ,
        ]);
    }

    public static function tokenResponseFieldProvider(): array
    {
        return
        [
            [ TokenResponseField::ACCESS_TOKEN  ] ,
            [ TokenResponseField::ASSERTION     ] ,
            [ TokenResponseField::EXPIRES_AT    ] ,
            [ TokenResponseField::EXPIRES_IN    ] ,
            [ TokenResponseField::ID_TOKEN      ] ,
            [ TokenResponseField::REFRESH_TOKEN ] ,
            [ TokenResponseField::SCOPE         ] ,
            [ TokenResponseField::TOKEN_TYPE    ] ,
        ];
    }

    public function testIncludesViaConstantsTrait(): void
    {
        $this->assertTrue ( TokenResponseField::includes( 'access_token'  ) );
        $this->assertTrue ( TokenResponseField::includes( 'assertion'     ) );
        $this->assertTrue ( TokenResponseField::includes( 'expires_at'    ) );
        $this->assertTrue ( TokenResponseField::includes( 'expires_in'    ) );
        $this->assertTrue ( TokenResponseField::includes( 'id_token'      ) );
        $this->assertTrue ( TokenResponseField::includes( 'refresh_token' ) );
        $this->assertTrue ( TokenResponseField::includes( 'scope'         ) );
        $this->assertTrue ( TokenResponseField::includes( 'token_type'    ) );
        $this->assertFalse( TokenResponseField::includes( 'unknown'       ) );
    }

    public function testGetConstantValuesViaConstantsTrait(): void
    {
        $values = TokenResponseField::getConstantValues();

        $this->assertContains( 'access_token'  , $values );
        $this->assertContains( 'assertion'     , $values );
        $this->assertContains( 'expires_at'    , $values );
        $this->assertContains( 'expires_in'    , $values );
        $this->assertContains( 'id_token'      , $values );
        $this->assertContains( 'refresh_token' , $values );
        $this->assertContains( 'scope'         , $values );
        $this->assertContains( 'token_type'    , $values );
    }
}
