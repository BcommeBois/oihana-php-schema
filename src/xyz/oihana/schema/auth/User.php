<?php

namespace xyz\oihana\schema\auth;

use oihana\reflect\attributes\HydrateWith;

use org\schema\Person;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\constants\traits\auth\UserTrait;

/**
 * Represents a User resource within an OAuth2 and RBAC (Role-Based Access Control) context.
 *
 * @package xyz\oihana\schema\auth
 * @category Security
 * @subcategory OAuth2 / RBAC
 * @since 1.0.2
 * @author Marc Alcaraz
 */
class User extends Person
{
    use UserTrait ;

    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * Data that the user has read-only access to (e.g. roles, permissions, vip, etc)
     * @var array|object|null
     */
    public array|object|null $appMetadata ;

    /**
     * Whether the user has completed their first successful login.
     * Defaults to false on creation; set to true by CallbackController
     * on the first OIDC callback (password init or external IdP).
     * @var bool|null
     */
    public bool|null $activated ;

    /**
     * The authorized applications of this particular user.
     * @var array|null
     */
    public array|null $applications ;

    /**
     * Indicates if the User is blocked for specific API, Applications, etc.
     * @var array|string|null
     */
    public array|null|string $blockedFor ;

    /**
     * The devices being used by this particular user.
     * This list is used to unlink the User, the refresh token will be revoked, forcing the user to re-login on the application.
     * @var array|null
     */
    public array|null $devices ;

    /**
     * The timestamp of the first successful login (ISO 8601).
     * Immutable once set. Audit/analytics value only — do not use for
     * "last seen" checks (use vendor lastLogin instead).
     * @var string|null
     */
    public string|null $firstLoginAt ;

    /**
     * The materialized status of the latest invitation associated with this
     * user. Used by the admin UI to filter and display lifecycle state
     * without querying the `invitations` collection. Maintained by the
     * controllers at every transition (create, resend, cancel, accept, expire).
     *
     * @see \xyz\oihana\schema\constants\InvitationStatus
     * @var string|null
     */
    public string|null $invitationStatus ;

    /**
     * Date of the latest login.
     * @var string|null
     */
    public string|null $lastLogin ;

    /**
     * The numbers of logins of the User.
     * @var string|null
     */
    public string|null $loginsCount ;

    /**
     * Data that the user has read/write access to (e.g. color_preference, blog_url, etc.)
     * @var array|object|null
     */
    public array|object|null $metadata ;

    /**
     * Email address pending verification (Zitadel-side).
     * Set when the user requested an email change; cleared once Zitadel
     * confirms verification (claim email_verified=true on next login or webhook).
     * The official `email` remains the previously verified address until then.
     * @var string|null
     */
    public string|null $pendingEmail ;

    /**
     * Timestamp (ISO 8601) when `pendingEmail` was requested.
     * Used by the UI to display "verification pending since …".
     * @var string|null
     */
    public string|null $pendingEmailSince ;

    /**
     * Define the permissions (scopes) that this particular User.
     * @var array<Permission>|null
     */
    #[HydrateWith( Permission::class ) ]
    public array|null $permissions ;

    /**
     * The number of permissions attached on this particular User.
     * @var int|null
     */
    public int|null $permissionsCount ;

    /**
     * Define the roles (scopes) that this particular User.
     * @var array<Role>|null
     */
    #[HydrateWith( Role::class ) ]
    public array|null $roles ;

    /**
     * The number of roles attached on this particular User.
     * @var int|null
     */
    public int|null $rolesCount ;

    /**
     * Date of the signed-up of the User.
     * @var string|null
     */
    public string|null $signedUp ;

    /**
     * The lifecycle status of this user account, gating authentication.
     * Distinct from `activated` (which is the immutable record of the first
     * successful login). Defaults to `active` on creation, can be set to
     * `disabled` by an administrator to refuse further logins.
     *
     * @see \xyz\oihana\schema\constants\UserStatus
     * @var string|null
     */
    public string|null $status ;
}