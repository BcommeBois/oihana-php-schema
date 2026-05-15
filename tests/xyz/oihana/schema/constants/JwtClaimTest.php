<?php

namespace tests\xyz\oihana\schema\constants ;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

use xyz\oihana\schema\constants\JwtClaim;

class JwtClaimTest extends TestCase
{
    /**
     * Map of every constant name exposed by JwtClaim to its expected string value.
     *
     * Registered RFC 7519 claims are exposed both under their short form (e.g. ISS)
     * and a long human-readable alias (e.g. ISSUER); both must resolve to the
     * same string value.
     */
    private const array EXPECTED =
    [
        // RFC 7519 registered claims (short + long aliases)
        'ISS'                   => 'iss' ,
        'ISSUER'                => 'iss' ,
        'SUB'                   => 'sub' ,
        'SUBJECT'               => 'sub' ,
        'AUD'                   => 'aud' ,
        'AUDIENCE'              => 'aud' ,
        'EXP'                   => 'exp' ,
        'EXPIRES_AT'            => 'exp' ,
        'NBF'                   => 'nbf' ,
        'NOT_BEFORE'            => 'nbf' ,
        'IAT'                   => 'iat' ,
        'ISSUED_AT'             => 'iat' ,
        'JTI'                   => 'jti' ,
        'JWT_ID'                => 'jti' ,

        // OAuth 2.0 / OIDC common claims
        'AZP'                   => 'azp' ,
        'NONCE'                 => 'nonce' ,
        'AUTH_TIME'             => 'auth_time' ,
        'ACR'                   => 'acr' ,
        'AMR'                   => 'amr' ,
        'SCOPE'                 => 'scope' ,
        'SCP'                   => 'scp' ,
        'CLIENT_ID'             => 'client_id' ,

        // OIDC Session Management
        'SID'                   => 'sid' ,
        'SESSION_ID'            => 'sid' ,

        // OIDC ID Token validation
        'AT_HASH'               => 'at_hash' ,
        'C_HASH'                => 'c_hash' ,

        // OIDC standard profile claims (Core §5.1)
        'NAME'                  => 'name' ,
        'GIVEN_NAME'            => 'given_name' ,
        'FAMILY_NAME'           => 'family_name' ,
        'MIDDLE_NAME'           => 'middle_name' ,
        'NICKNAME'              => 'nickname' ,
        'PREFERRED_USERNAME'    => 'preferred_username' ,
        'PROFILE'               => 'profile' ,
        'PICTURE'               => 'picture' ,
        'WEBSITE'               => 'website' ,
        'EMAIL'                 => 'email' ,
        'EMAIL_VERIFIED'        => 'email_verified' ,
        'GENDER'                => 'gender' ,
        'BIRTHDATE'             => 'birthdate' ,
        'ZONEINFO'              => 'zoneinfo' ,
        'LOCALE'                => 'locale' ,
        'PHONE_NUMBER'          => 'phone_number' ,
        'PHONE_NUMBER_VERIFIED' => 'phone_number_verified' ,
        'ADDRESS'               => 'address' ,
        'UPDATED_AT'            => 'updated_at' ,

        // RFC 8693 — Token Exchange
        'ACT'                   => 'act' ,
        'MAY_ACT'               => 'may_act' ,

        // RFC 7800 — Proof-of-Possession
        'CNF'                   => 'cnf' ,

        // Provider-specific / non-standard
        'GROUPS'                => 'groups' ,
        'ROLES'                 => 'roles' ,
        'ENTITLEMENTS'          => 'entitlements' ,
        'TID'                   => 'tid' ,
        'OID'                   => 'oid' ,
    ];

    public function testConstantsValues(): void
    {
        foreach ( self::EXPECTED as $name => $value )
        {
            $this->assertSame( $value , constant( JwtClaim::class . "::$name" ) , "JwtClaim::$name must equal '$value'" );
        }
    }

    /**
     * Ensures that every claim known to be registered by RFC 7519 is exposed
     * under both its short form and a long human-readable alias, and that both
     * resolve to the same value.
     */
    #[DataProvider('registeredAliasProvider')]
    public function testRegisteredClaimAliases( string $short , string $long , string $value ): void
    {
        $this->assertSame( $value , constant( JwtClaim::class . "::$short" ) );
        $this->assertSame( $value , constant( JwtClaim::class . "::$long"  ) );
        $this->assertSame
        (
            constant( JwtClaim::class . "::$short" ) ,
            constant( JwtClaim::class . "::$long"  )
        );
    }

    public static function registeredAliasProvider(): array
    {
        return
        [
            'iss' => [ 'ISS' , 'ISSUER'     , 'iss' ] ,
            'sub' => [ 'SUB' , 'SUBJECT'    , 'sub' ] ,
            'aud' => [ 'AUD' , 'AUDIENCE'   , 'aud' ] ,
            'exp' => [ 'EXP' , 'EXPIRES_AT' , 'exp' ] ,
            'nbf' => [ 'NBF' , 'NOT_BEFORE' , 'nbf' ] ,
            'iat' => [ 'IAT' , 'ISSUED_AT'  , 'iat' ] ,
            'jti' => [ 'JTI' , 'JWT_ID'     , 'jti' ] ,
            'sid' => [ 'SID' , 'SESSION_ID' , 'sid' ] ,
        ];
    }

    #[DataProvider('jwtClaimProvider')]
    public function testValidClaims( string $claim ): void
    {
        $this->assertContains( $claim , array_values( self::EXPECTED ) );
    }

    public static function jwtClaimProvider(): array
    {
        return array_map( static fn( string $value ): array => [ $value ] , array_unique( array_values( self::EXPECTED ) ) );
    }

    public function testIncludesViaConstantsTrait(): void
    {
        foreach ( array_unique( array_values( self::EXPECTED ) ) as $value )
        {
            $this->assertTrue( JwtClaim::includes( $value ) , "JwtClaim::includes('$value') must be true" );
        }

        $this->assertFalse( JwtClaim::includes( 'unknown'   ) );
        $this->assertFalse( JwtClaim::includes( ''          ) );
        $this->assertFalse( JwtClaim::includes( 'ISS'       ) );
    }

    public function testGetConstantValuesViaConstantsTrait(): void
    {
        $values = JwtClaim::getConstantValues();

        foreach ( array_unique( array_values( self::EXPECTED ) ) as $value )
        {
            $this->assertContains( $value , $values );
        }
    }
}
