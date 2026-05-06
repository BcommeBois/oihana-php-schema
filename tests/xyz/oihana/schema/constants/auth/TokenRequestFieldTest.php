<?php

namespace tests\xyz\oihana\schema\constants\auth ;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

use xyz\oihana\schema\constants\auth\TokenRequestField;

class TokenRequestFieldTest extends TestCase
{
    public function testConstantsValues(): void
    {
        $expected =
        [
            'ASSERTION'             => 'assertion'             ,
            'CLIENT_ASSERTION'      => 'client_assertion'      ,
            'CLIENT_ASSERTION_TYPE' => 'client_assertion_type' ,
            'GRANT_TYPE'            => 'grant_type'            ,
            'SCOPE'                 => 'scope'                 ,
        ];

        foreach ( $expected as $name => $value )
        {
            $this->assertSame( $value , constant( TokenRequestField::class . "::$name" ) );
        }
    }

    #[DataProvider('tokenRequestFieldProvider')]
    public function testValidFields( string $field ): void
    {
        $this->assertContains( $field ,
        [
            TokenRequestField::ASSERTION             ,
            TokenRequestField::CLIENT_ASSERTION      ,
            TokenRequestField::CLIENT_ASSERTION_TYPE ,
            TokenRequestField::GRANT_TYPE            ,
            TokenRequestField::SCOPE                 ,
        ]);
    }

    public static function tokenRequestFieldProvider(): array
    {
        return
        [
            [ TokenRequestField::ASSERTION             ] ,
            [ TokenRequestField::CLIENT_ASSERTION      ] ,
            [ TokenRequestField::CLIENT_ASSERTION_TYPE ] ,
            [ TokenRequestField::GRANT_TYPE            ] ,
            [ TokenRequestField::SCOPE                 ] ,
        ];
    }

    public function testIncludesViaConstantsTrait(): void
    {
        $this->assertTrue ( TokenRequestField::includes( 'assertion'             ) );
        $this->assertTrue ( TokenRequestField::includes( 'client_assertion'      ) );
        $this->assertTrue ( TokenRequestField::includes( 'client_assertion_type' ) );
        $this->assertTrue ( TokenRequestField::includes( 'grant_type'            ) );
        $this->assertTrue ( TokenRequestField::includes( 'scope'                 ) );
        $this->assertFalse( TokenRequestField::includes( 'unknown'               ) );
    }

    public function testGetConstantValuesViaConstantsTrait(): void
    {
        $values = TokenRequestField::getConstantValues();

        $this->assertContains( 'assertion'             , $values );
        $this->assertContains( 'client_assertion'      , $values );
        $this->assertContains( 'client_assertion_type' , $values );
        $this->assertContains( 'grant_type'            , $values );
        $this->assertContains( 'scope'                 , $values );
    }
}
