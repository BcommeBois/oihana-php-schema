<?php

namespace xyz\oihana\schema\constants ;

use oihana\reflect\traits\ConstantsTrait;

/**
 * Enumeration of standard JWT claim names.
 *
 * Covers the IANA "JSON Web Token Claims" registry and the most
 * common neighbouring specifications:
 * - RFC 7519 registered claims (`iss`, `sub`, `aud`, `exp`, `nbf`, `iat`, `jti`)
 * - Common OAuth 2.0 / OpenID Connect claims (`azp`, `nonce`, `acr`, `amr`, `scope`...)
 * - OIDC standard profile claims (OIDC Core §5.1)
 * - OIDC Session Management (`sid` — Front-Channel / Back-Channel Logout)
 * - OIDC ID Token validation hashes (`at_hash`, `c_hash`)
 * - RFC 8693 Token Exchange (`act`, `may_act`)
 * - RFC 7800 Proof-of-Possession (`cnf`)
 * - Widely used provider-specific claims (`groups`, `roles`, `tid`, `oid`...)
 *
 * Each registered claim from RFC 7519 is exposed under both its short
 * form (e.g. {@see self::ISS}) and a long, human-readable alias
 * (e.g. {@see self::ISSUER}). Both constants resolve to the same
 * string value and may be used interchangeably.
 *
 * Example:
 * ```php
 * $assertion = JWT::encode
 * (
 *     [
 *         JwtClaim::ISS => $this->serviceAccount[ ZitadelKeyFile::USER_ID ] ,
 *         JwtClaim::SUB => $this->serviceAccount[ ZitadelKeyFile::USER_ID ] ,
 *         JwtClaim::AUD => $this->issuer ,
 *         JwtClaim::IAT => $now ,
 *         JwtClaim::EXP => $now + 3600 ,
 *     ] ,
 *     ...
 * ) ;
 * ```
 *
 * References:
 * - RFC 7519  — JSON Web Token (registered claims)
 * - RFC 7515  — JSON Web Signature
 * - RFC 7800  — Proof-of-Possession Key Semantics for JWTs
 * - RFC 8693  — OAuth 2.0 Token Exchange
 * - OIDC Core 1.0 (§2 ID Token, §3.1.3.6 / §3.3.2.11, §5.1 Standard Claims)
 * - OIDC Front-Channel / Back-Channel Logout 1.0 (`sid`)
 * - IANA JSON Web Token Claims Registry
 *
 * @package xyz\oihana\schema\constants
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.2
 */
class JwtClaim
{
    use ConstantsTrait ;

    // -------------------------------------------------------------------------
    // Registered claims (RFC 7519 §4.1)
    // -------------------------------------------------------------------------

    /**
     * `iss` — Issuer of the JWT (RFC 7519 §4.1.1).
     *
     * Identifies the principal that issued the JWT. For a
     * `jwt-bearer` client assertion (RFC 7523), this is the
     * OAuth `client_id` of the application.
     *
     * Value: case-sensitive string, typically a StringOrURI.
     */
    public const string ISS = 'iss' ;

    /**
     * Long-form alias of {@see self::ISS}.
     */
    public const string ISSUER = 'iss' ;

    /**
     * `sub` — Subject of the JWT (RFC 7519 §4.1.2).
     *
     * Identifies the principal that is the subject of the JWT.
     * The claim value must be locally unique in the context of
     * the issuer, or globally unique.
     *
     * For a `jwt-bearer` client assertion, this is the OAuth
     * `client_id` of the application (same value as {@see self::ISS}).
     */
    public const string SUB = 'sub' ;

    /**
     * Long-form alias of {@see self::SUB}.
     */
    public const string SUBJECT = 'sub' ;

    /**
     * `aud` — Audience of the JWT (RFC 7519 §4.1.3).
     *
     * Identifies the recipients that the JWT is intended for.
     * For a `client_credentials` exchange, this is typically
     * the issuer URL of the IdP.
     *
     * Value: a single StringOrURI or an array of StringOrURI.
     */
    public const string AUD = 'aud' ;

    /**
     * Long-form alias of {@see self::AUD}.
     */
    public const string AUDIENCE = 'aud' ;

    /**
     * `exp` — Expiration time (RFC 7519 §4.1.4).
     *
     * Unix epoch time (seconds since 1970-01-01T00:00:00Z) at
     * which the JWT must no longer be accepted for processing.
     * For short-lived client assertions, `now + 60s` is usually
     * sufficient.
     *
     * Value: NumericDate.
     */
    public const string EXP = 'exp' ;

    /**
     * Long-form alias of {@see self::EXP}.
     */
    public const string EXPIRES_AT = 'exp' ;

    /**
     * `nbf` — Not Before (RFC 7519 §4.1.5).
     *
     * Unix epoch time before which the JWT must not be accepted.
     * Helps mitigate clock skew between client and server.
     *
     * Value: NumericDate.
     */
    public const string NBF = 'nbf' ;

    /**
     * Long-form alias of {@see self::NBF}.
     */
    public const string NOT_BEFORE = 'nbf' ;

    /**
     * `iat` — Issued At (RFC 7519 §4.1.6).
     *
     * Unix epoch time at which the JWT was minted. Can be used
     * by recipients to determine the age of the token.
     *
     * Value: NumericDate.
     */
    public const string IAT = 'iat' ;

    /**
     * Long-form alias of {@see self::IAT}.
     */
    public const string ISSUED_AT = 'iat' ;

    /**
     * `jti` — JWT ID (RFC 7519 §4.1.7).
     *
     * Unique identifier for the JWT. Used to prevent replay
     * attacks by ensuring each assertion is only used once;
     * the authorization server may store and reject already
     * seen identifiers.
     *
     * Value: case-sensitive string.
     */
    public const string JTI = 'jti' ;

    /**
     * Long-form alias of {@see self::JTI}.
     */
    public const string JWT_ID = 'jti' ;

    // -------------------------------------------------------------------------
    // OAuth 2.0 / OIDC commonly used claims
    // -------------------------------------------------------------------------

    /**
     * `azp` — Authorized Party (OIDC Core §2).
     *
     * The party to which the ID Token was issued. Used when the
     * authorized presenter differs from the sole audience. The
     * value is the OAuth `client_id` of that party.
     */
    public const string AZP = 'azp' ;

    /**
     * `nonce` — String value used to associate a Client session
     * with an ID Token and to mitigate replay attacks (OIDC Core §2).
     *
     * If sent in the Authentication Request, the same value must
     * be returned unmodified in the ID Token.
     */
    public const string NONCE = 'nonce' ;

    /**
     * `auth_time` — Time when the End-User authentication
     * occurred (OIDC Core §2).
     *
     * Value: NumericDate.
     */
    public const string AUTH_TIME = 'auth_time' ;

    /**
     * `acr` — Authentication Context Class Reference (OIDC Core §2).
     *
     * Identifies the authentication context class that the
     * authentication performed satisfied. Value is a
     * case-sensitive string, often a URI.
     */
    public const string ACR = 'acr' ;

    /**
     * `amr` — Authentication Methods References (OIDC Core §2).
     *
     * JSON array of strings identifying the authentication
     * methods used (e.g. `["pwd","mfa"]`). See RFC 8176 for a
     * registry of values.
     */
    public const string AMR = 'amr' ;

    /**
     * `scope` — OAuth 2.0 scope values (RFC 8693 §4.2).
     *
     * Space-separated string listing the OAuth 2.0 scope values
     * granted to the token.
     */
    public const string SCOPE = 'scope' ;

    /**
     * `scp` — Alternative scope representation used by some
     * identity providers (e.g. Microsoft Identity Platform).
     *
     * Usually a JSON array of strings, conveying the same
     * information as {@see self::SCOPE}.
     */
    public const string SCP = 'scp' ;

    /**
     * `client_id` — OAuth 2.0 Client Identifier valid at the
     * authorization server (RFC 8693 §4.3).
     */
    public const string CLIENT_ID = 'client_id' ;

    // -------------------------------------------------------------------------
    // OIDC Session Management (Front-Channel / Back-Channel Logout)
    // -------------------------------------------------------------------------

    /**
     * `sid` — Session ID.
     *
     * Identifier for a Session, used to enable per-session logout.
     * Present in ID Tokens when session management is in use and
     * required in Logout Tokens.
     *
     * Specs:
     * - OIDC Front-Channel Logout 1.0
     * - OIDC Back-Channel Logout 1.0
     */
    public const string SID = 'sid' ;

    /**
     * Long-form alias of {@see self::SID}.
     */
    public const string SESSION_ID = 'sid' ;

    // -------------------------------------------------------------------------
    // OIDC ID Token validation (OIDC Core §3.1.3.6 / §3.3.2.11)
    // -------------------------------------------------------------------------

    /**
     * `at_hash` — Access Token hash value (OIDC Core §3.1.3.6).
     *
     * Base64url-encoded left-most half of the hash of the
     * `access_token`. Used by the client to validate that the
     * access token is bound to the ID Token.
     */
    public const string AT_HASH = 'at_hash' ;

    /**
     * `c_hash` — Authorization Code hash value (OIDC Core §3.3.2.11).
     *
     * Base64url-encoded left-most half of the hash of the
     * authorization `code`. Used in the hybrid flow to validate
     * that the code is bound to the ID Token.
     */
    public const string C_HASH = 'c_hash' ;

    // -------------------------------------------------------------------------
    // OIDC standard profile claims (OIDC Core §5.1)
    // -------------------------------------------------------------------------

    /**
     * `name` — End-User's full name in displayable form,
     * including all name parts.
     */
    public const string NAME = 'name' ;

    /**
     * `given_name` — Given name(s) or first name(s) of the End-User.
     */
    public const string GIVEN_NAME = 'given_name' ;

    /**
     * `family_name` — Surname(s) or last name(s) of the End-User.
     */
    public const string FAMILY_NAME = 'family_name' ;

    /**
     * `middle_name` — Middle name(s) of the End-User.
     */
    public const string MIDDLE_NAME = 'middle_name' ;

    /**
     * `nickname` — Casual name of the End-User. May or may not
     * be the same as {@see self::GIVEN_NAME}.
     */
    public const string NICKNAME = 'nickname' ;

    /**
     * `preferred_username` — Shorthand name by which the End-User
     * wishes to be referred to. Not guaranteed to be unique.
     */
    public const string PREFERRED_USERNAME = 'preferred_username' ;

    /**
     * `profile` — URL of the End-User's profile page.
     */
    public const string PROFILE = 'profile' ;

    /**
     * `picture` — URL of the End-User's profile picture.
     */
    public const string PICTURE = 'picture' ;

    /**
     * `website` — URL of the End-User's web page or blog.
     */
    public const string WEBSITE = 'website' ;

    /**
     * `email` — End-User's preferred e-mail address.
     */
    public const string EMAIL = 'email' ;

    /**
     * `email_verified` — Boolean. `true` if the End-User's e-mail
     * address has been verified, `false` otherwise.
     */
    public const string EMAIL_VERIFIED = 'email_verified' ;

    /**
     * `gender` — End-User's gender. Values defined by the
     * specification are `female` and `male`; other values may
     * be used when neither fits.
     */
    public const string GENDER = 'gender' ;

    /**
     * `birthdate` — End-User's birthday in ISO 8601:2004
     * `YYYY-MM-DD` format. The year may be `0000` to indicate
     * that it is omitted.
     */
    public const string BIRTHDATE = 'birthdate' ;

    /**
     * `zoneinfo` — String from the zoneinfo time zone database
     * representing the End-User's time zone (e.g. `Europe/Paris`).
     */
    public const string ZONEINFO = 'zoneinfo' ;

    /**
     * `locale` — End-User's locale, as a BCP47 language tag
     * (e.g. `en-US`, `fr-FR`).
     */
    public const string LOCALE = 'locale' ;

    /**
     * `phone_number` — End-User's preferred telephone number,
     * preferably in E.164 format (e.g. `+33123456789`).
     */
    public const string PHONE_NUMBER = 'phone_number' ;

    /**
     * `phone_number_verified` — Boolean. `true` if the End-User's
     * phone number has been verified, `false` otherwise.
     */
    public const string PHONE_NUMBER_VERIFIED = 'phone_number_verified' ;

    /**
     * `address` — End-User's preferred postal address, as a
     * JSON object (see OIDC Core §5.1.1 for sub-fields).
     */
    public const string ADDRESS = 'address' ;

    /**
     * `updated_at` — Time the End-User's information was last
     * updated.
     *
     * Value: NumericDate.
     */
    public const string UPDATED_AT = 'updated_at' ;

    // -------------------------------------------------------------------------
    // RFC 8693 — OAuth 2.0 Token Exchange
    // -------------------------------------------------------------------------

    /**
     * `act` — Actor (RFC 8693 §4.1).
     *
     * JSON object identifying the acting party in a delegation
     * scenario. Contains claims (typically `sub` and `iss`)
     * describing the actor. May be nested to represent a chain
     * of delegation.
     */
    public const string ACT = 'act' ;

    /**
     * `may_act` — Authorized Actor (RFC 8693 §4.4).
     *
     * JSON object expressing that the named actor is authorized
     * to act on behalf of the subject of the token.
     */
    public const string MAY_ACT = 'may_act' ;

    // -------------------------------------------------------------------------
    // RFC 7800 — Proof-of-Possession Key Semantics for JWTs
    // -------------------------------------------------------------------------

    /**
     * `cnf` — Confirmation (RFC 7800).
     *
     * JSON object containing members that identify the
     * proof-of-possession key used to bind the token to a
     * specific holder (e.g. `jkt`, `jwk`, `x5t#S256`).
     */
    public const string CNF = 'cnf' ;

    // -------------------------------------------------------------------------
    // Common non-standard / provider-specific claims
    // -------------------------------------------------------------------------

    /**
     * `groups` — Group memberships of the subject.
     *
     * Non-standard but widely used (Keycloak, Okta, Azure AD...).
     * Usually a JSON array of strings.
     */
    public const string GROUPS = 'groups' ;

    /**
     * `roles` — Role assignments of the subject.
     *
     * Non-standard but widely used. Usually a JSON array of strings.
     */
    public const string ROLES = 'roles' ;

    /**
     * `entitlements` — Entitlements / fine-grained permissions
     * granted to the subject. Non-standard.
     */
    public const string ENTITLEMENTS = 'entitlements' ;

    /**
     * `tid` — Tenant ID.
     *
     * Used by Microsoft Identity Platform / Azure AD to identify
     * the Azure AD tenant the user belongs to.
     */
    public const string TID = 'tid' ;

    /**
     * `oid` — Object ID.
     *
     * Used by Microsoft Identity Platform / Azure AD as the
     * immutable identifier for the user object across applications.
     */
    public const string OID = 'oid' ;
}
