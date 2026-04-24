<?php

namespace tests\xyz\oihana\schema\auth ;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionException;

use org\schema\actions\InviteAction;

use xyz\oihana\schema\auth\Invitation;

class InvitationTest extends TestCase
{
    public function testDefaults(): void
    {
        $invitation = new Invitation();

        $this->assertNull( $invitation->email        ?? null );
        $this->assertNull( $invitation->redirectUrl  ?? null );
        $this->assertNull( $invitation->sentAt       ?? null );
        $this->assertNull( $invitation->sentCount    ?? null );
        $this->assertNull( $invitation->token        ?? null );
    }

    public function testIsInviteAction(): void
    {
        $this->assertInstanceOf( InviteAction::class , new Invitation() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( 'https://schema.oihana.xyz' , Invitation::CONTEXT );
    }

    public function testTraitConstants(): void
    {
        $this->assertSame( 'redirectUrl' , Invitation::REDIRECT_URL );
        $this->assertSame( 'sentAt'      , Invitation::SENT_AT      );
        $this->assertSame( 'sentCount'   , Invitation::SENT_COUNT   );
        $this->assertSame( 'token'       , Invitation::TOKEN        );

        $this->assertSame( 'pending'   , Invitation::ACTION_STATUS_PENDING   );
        $this->assertSame( 'accepted'  , Invitation::ACTION_STATUS_ACCEPTED  );
        $this->assertSame( 'expired'   , Invitation::ACTION_STATUS_EXPIRED   );
        $this->assertSame( 'cancelled' , Invitation::ACTION_STATUS_CANCELLED );
    }

    /**
     * @throws ReflectionException
     */
    public function testHydration(): void
    {
        $data =
        [
            Invitation::REDIRECT_URL => 'https://app.example.com/welcome' ,
            Invitation::SENT_AT      => '2026-04-24T08:00:00Z' ,
            Invitation::SENT_COUNT   => 2 ,
            Invitation::TOKEN        => 'sha256-hash-value' ,
            'email'                  => 'invitee@example.com' ,
        ];

        $invitation = new Invitation( $data );

        $this->assertSame( 'https://app.example.com/welcome' , $invitation->redirectUrl );
        $this->assertSame( '2026-04-24T08:00:00Z'            , $invitation->sentAt      );
        $this->assertSame( 2                                  , $invitation->sentCount   );
        $this->assertSame( 'sha256-hash-value'                , $invitation->token       );
        $this->assertSame( 'invitee@example.com'              , $invitation->email       );
    }

    #[DataProvider('actionStatusProvider')]
    public function testActionStatusValues( string $status ): void
    {
        $this->assertContains( $status ,
        [
            Invitation::ACTION_STATUS_PENDING ,
            Invitation::ACTION_STATUS_ACCEPTED ,
            Invitation::ACTION_STATUS_EXPIRED ,
            Invitation::ACTION_STATUS_CANCELLED ,
        ]);
    }

    public static function actionStatusProvider(): array
    {
        return
        [
            [ Invitation::ACTION_STATUS_PENDING   ] ,
            [ Invitation::ACTION_STATUS_ACCEPTED  ] ,
            [ Invitation::ACTION_STATUS_EXPIRED   ] ,
            [ Invitation::ACTION_STATUS_CANCELLED ] ,
        ];
    }
}
