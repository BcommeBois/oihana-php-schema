<?php

namespace tests\xyz\oihana\schema\constants\auth ;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

use xyz\oihana\schema\constants\auth\KeyfileType;

class KeyfileTypeTest extends TestCase
{
    public function testConstantsValues(): void
    {
        $expected =
        [
            'APPLICATION'     => 'application'    ,
            'SERVICE_ACCOUNT' => 'serviceaccount' ,
        ];

        foreach ( $expected as $name => $value )
        {
            $this->assertSame( $value , constant( KeyfileType::class . "::$name" ) );
        }
    }

    #[DataProvider('keyfileTypeProvider')]
    public function testValidKeyfileTypes( string $type ): void
    {
        $this->assertContains( $type ,
        [
            KeyfileType::APPLICATION ,
            KeyfileType::SERVICE_ACCOUNT ,
        ]);
    }

    public static function keyfileTypeProvider(): array
    {
        return
        [
            [ KeyfileType::APPLICATION     ] ,
            [ KeyfileType::SERVICE_ACCOUNT ] ,
        ];
    }

    public function testIncludesViaConstantsTrait(): void
    {
        $this->assertTrue ( KeyfileType::includes( 'application'    ) );
        $this->assertTrue ( KeyfileType::includes( 'serviceaccount' ) );
        $this->assertFalse( KeyfileType::includes( 'unknown'        ) );
    }

    public function testGetConstantValuesViaConstantsTrait(): void
    {
        $values = KeyfileType::getConstantValues();

        $this->assertContains( 'application'    , $values );
        $this->assertContains( 'serviceaccount' , $values );
    }
}
