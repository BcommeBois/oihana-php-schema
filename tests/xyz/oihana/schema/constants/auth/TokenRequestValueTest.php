<?php

namespace tests\xyz\oihana\schema\constants\auth ;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

use xyz\oihana\schema\constants\auth\TokenRequestValue;

class TokenRequestValueTest extends TestCase
{
    public function testConstantsValues(): void
    {
        $expected =
        [
            'DEFAULT_SCOPE'             => 'openid'                                                       ,
            'GRANT_CLIENT_CREDENTIALS'  => 'client_credentials'                                           ,
            'GRANT_JWT_BEARER'          => 'urn:ietf:params:oauth:grant-type:jwt-bearer'                  ,
            'JWT_BEARER_ASSERTION_TYPE' => 'urn:ietf:params:oauth:client-assertion-type:jwt-bearer'       ,
        ];

        foreach ( $expected as $name => $value )
        {
            $this->assertSame( $value , constant( TokenRequestValue::class . "::$name" ) );
        }
    }

    #[DataProvider('tokenRequestValueProvider')]
    public function testValidValues( string $value ): void
    {
        $this->assertContains( $value ,
        [
            TokenRequestValue::DEFAULT_SCOPE             ,
            TokenRequestValue::GRANT_CLIENT_CREDENTIALS  ,
            TokenRequestValue::GRANT_JWT_BEARER          ,
            TokenRequestValue::JWT_BEARER_ASSERTION_TYPE ,
        ]);
    }

    public static function tokenRequestValueProvider(): array
    {
        return
        [
            [ TokenRequestValue::DEFAULT_SCOPE             ] ,
            [ TokenRequestValue::GRANT_CLIENT_CREDENTIALS  ] ,
            [ TokenRequestValue::GRANT_JWT_BEARER          ] ,
            [ TokenRequestValue::JWT_BEARER_ASSERTION_TYPE ] ,
        ];
    }

    public function testIncludesViaConstantsTrait(): void
    {
        $this->assertTrue ( TokenRequestValue::includes( 'openid'             ) );
        $this->assertTrue ( TokenRequestValue::includes( 'client_credentials' ) );
        $this->assertTrue ( TokenRequestValue::includes( 'urn:ietf:params:oauth:grant-type:jwt-bearer'            ) );
        $this->assertTrue ( TokenRequestValue::includes( 'urn:ietf:params:oauth:client-assertion-type:jwt-bearer' ) );
        $this->assertFalse( TokenRequestValue::includes( 'unknown'            ) );
    }

    public function testGetConstantValuesViaConstantsTrait(): void
    {
        $values = TokenRequestValue::getConstantValues();

        $this->assertContains( 'openid'             , $values );
        $this->assertContains( 'client_credentials' , $values );
        $this->assertContains( 'urn:ietf:params:oauth:grant-type:jwt-bearer'            , $values );
        $this->assertContains( 'urn:ietf:params:oauth:client-assertion-type:jwt-bearer' , $values );
    }
}
