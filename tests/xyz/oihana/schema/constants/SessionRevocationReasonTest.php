<?php

namespace tests\xyz\oihana\schema\constants ;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

use xyz\oihana\schema\constants\SessionRevocationReason;

class SessionRevocationReasonTest extends TestCase
{
    public function testConstantsValues(): void
    {
        $expected =
        [
            'ADMIN_REVOKED'    => 'admin_revoked'    ,
            'EMERGENCY_REVOKE' => 'emergency_revoke' ,
            'LOGOUT'           => 'logout'           ,
            'ORPHANED'         => 'orphaned'         ,
            'TOKENS_REVOKED'   => 'tokens_revoked'   ,
            'USER_DELETED'     => 'user_deleted'     ,
            'USER_DISABLED'    => 'user_disabled'    ,
            'USER_REVOKED'     => 'user_revoked'     ,
        ];

        foreach ( $expected as $name => $value )
        {
            $this->assertSame( $value , constant( SessionRevocationReason::class . "::$name" ) );
        }
    }

    #[DataProvider('reasonProvider')]
    public function testValidReasons( string $reason ): void
    {
        $this->assertContains( $reason ,
        [
            SessionRevocationReason::ADMIN_REVOKED    ,
            SessionRevocationReason::EMERGENCY_REVOKE ,
            SessionRevocationReason::LOGOUT           ,
            SessionRevocationReason::ORPHANED         ,
            SessionRevocationReason::TOKENS_REVOKED   ,
            SessionRevocationReason::USER_DELETED     ,
            SessionRevocationReason::USER_DISABLED    ,
            SessionRevocationReason::USER_REVOKED     ,
        ]);
    }

    public static function reasonProvider(): array
    {
        return
        [
            [ SessionRevocationReason::ADMIN_REVOKED    ] ,
            [ SessionRevocationReason::EMERGENCY_REVOKE ] ,
            [ SessionRevocationReason::LOGOUT           ] ,
            [ SessionRevocationReason::ORPHANED         ] ,
            [ SessionRevocationReason::TOKENS_REVOKED   ] ,
            [ SessionRevocationReason::USER_DELETED     ] ,
            [ SessionRevocationReason::USER_DISABLED    ] ,
            [ SessionRevocationReason::USER_REVOKED     ] ,
        ];
    }

    public function testIncludesViaConstantsTrait(): void
    {
        $this->assertTrue ( SessionRevocationReason::includes( 'admin_revoked'    ) );
        $this->assertTrue ( SessionRevocationReason::includes( 'emergency_revoke' ) );
        $this->assertTrue ( SessionRevocationReason::includes( 'logout'           ) );
        $this->assertTrue ( SessionRevocationReason::includes( 'orphaned'         ) );
        $this->assertTrue ( SessionRevocationReason::includes( 'tokens_revoked'   ) );
        $this->assertTrue ( SessionRevocationReason::includes( 'user_deleted'     ) );
        $this->assertTrue ( SessionRevocationReason::includes( 'user_disabled'    ) );
        $this->assertTrue ( SessionRevocationReason::includes( 'user_revoked'     ) );
        $this->assertFalse( SessionRevocationReason::includes( 'unknown'          ) );
    }

    public function testGetConstantValuesViaConstantsTrait(): void
    {
        $values = SessionRevocationReason::getConstantValues();

        $this->assertContains( 'admin_revoked'    , $values );
        $this->assertContains( 'emergency_revoke' , $values );
        $this->assertContains( 'logout'           , $values );
        $this->assertContains( 'orphaned'         , $values );
        $this->assertContains( 'tokens_revoked'   , $values );
        $this->assertContains( 'user_deleted'     , $values );
        $this->assertContains( 'user_disabled'    , $values );
        $this->assertContains( 'user_revoked'     , $values );
    }
}
