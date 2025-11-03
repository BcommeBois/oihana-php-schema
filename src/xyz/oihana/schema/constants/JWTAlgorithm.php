<?php

namespace xyz\oihana\schema\constants;

use oihana\reflect\traits\ConstantsTrait;

/**
 * Defines supported JSON Web Token (JWT) signing algorithms.
 * Each constant represents a standard algorithm identifier as defined by RFC 7518.
 *
 * 1. **Symmetric (HMAC)** – single shared secret key for both signing and verifying:
 * - HS256 — HMAC using SHA-256
 * - HS384 — HMAC using SHA-384
 * - HS512 — HMAC using SHA-512
 *
 * 2. **Asymmetric (RSA)** – private key signs, public key verifies:
 * - RS256 — RSASSA-PKCS1-v1_5 using SHA-256
 * - RS384 — RSASSA-PKCS1-v1_5 using SHA-384
 * - RS512 — RSASSA-PKCS1-v1_5 using SHA-512
 *
 * 3. **Asymmetric (RSA-PSS)** – enhanced padding scheme:
 * - PS256 — RSASSA-PSS using SHA-256 and MGF1 with SHA-256
 * - PS384 — RSASSA-PSS using SHA-384 and MGF1 with SHA-384
 * - PS512 — RSASSA-PSS using SHA-512 and MGF1 with SHA-512
 *
 * 4. **Optional / Other**:
 * - NONE — no digital signature or MAC
 *
 * Symmetric algorithms use a shared secret and are generally simpler to implement,
 * but asymmetric algorithms are recommended for production because they allow key
 * rotation and separation of issuer and verifier responsibilities.
 *
 * Helper methods:
 * - `isSymmetric(string $alg)` — returns true for HMAC algorithms
 * - `isAsymmetric(string $alg)` — returns true for RSA, RSA-PSS, or ECDSA algorithms
 *
 * @see https://datatracker.ietf.org/doc/html/rfc7518
 *
 * @package xyz\oihana\schema
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.2
 */
class JWTAlgorithm
{
    use ConstantsTrait ;

    /**
     * HS256 — HMAC using SHA-256.
     *
     * A symmetric algorithm that uses a single shared secret key
     * for both signing and verifying JWTs.
     *
     * Commonly used for simplicity when both the issuer and verifier
     * run in the same trusted environment.
     */
    public const string HS256 = 'HS256' ;

    /**
     * HS384 — HMAC using SHA-384.
     *
     * Similar to HS256 but using a larger SHA-384 hash,
     * providing stronger cryptographic security.
     */
    public const string HS384 = 'HS384';

    /**
     * HS512 — HMAC using SHA-512.
     *
     * Uses SHA-512 hash for maximum cryptographic strength
     * in symmetric signing scenarios.
     */
    public const string HS512 = 'HS512';

    /**
     * No digital signature or MAC.
     */
    public const string NONE = 'none';

    /**
     * PS256 — RSASSA-PSS using SHA-256 and MGF1 with SHA-256.
     *
     * An asymmetric algorithm using the RSA-PSS padding scheme,
     * offering stronger cryptographic security than RS256.
     */
    public const string PS256 = 'PS256' ;

    /**
     * PS384 — RSASSA-PSS using SHA-384 and MGF1 with SHA-384.
     *
     * Provides enhanced security over PS256 by using SHA-384 hash.
     */
    public const string PS384 = 'PS384';

    /**
     * PS512 — RSASSA-PSS using SHA-512 and MGF1 with SHA-512.
     *
     * Maximum cryptographic strength for RSA-PSS signing.
     */
    public const string PS512 = 'PS512';

    /**
     * RS256 — RSASSA-PKCS1-v1_5 using SHA-256.
     *
     * An asymmetric algorithm using a private key to sign and
     * a public key to verify the JWT.
     *
     * Recommended for production; allows key rotation and
     * separation of issuer and verifier.
     */
    public const string RS256 = 'RS256' ;

    /**
     * RS384 — RSASSA-PKCS1-v1_5 using SHA-384.
     *
     * Similar to RS256 but uses SHA-384 hash for stronger security.
     */
    public const string RS384 = 'RS384';

    /**
     * RS512 — RSASSA-PKCS1-v1_5 using SHA-512.
     *
     * Similar to RS256 but uses SHA-512 hash for maximum cryptographic strength.
     */
    public const string RS512 = 'RS512';

    /**
     * Returns true if the given algorithm is asymmetric.
     *
     * @param  string $algorithm  JWT algorithm name (e.g. "RS256")
     * @return bool
     */
    public static function isAsymmetric( string $algorithm ): bool
    {
        return preg_match('/^(RS|PS|ES)\d+$/', $algorithm ) === 1;
    }

    /**
     * Returns true if the given algorithm is symmetric.
     *
     * @param  string  $algorithm  JWT algorithm name (e.g. "HS256")
     * @return bool
     */
    public static function isSymmetric( string $algorithm ): bool
    {
        return preg_match('/^HS\d+$/', $algorithm ) === 1;
    }
}