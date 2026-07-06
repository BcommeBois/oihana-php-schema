# `xyz\oihana\schema\auth` — Authentication, identity & RBAC

The `xyz\oihana\schema\auth` namespace models everything Oihana applications need around **identity, sessions, OAuth2/OIDC clients and Role-Based Access Control** (RBAC). It is designed around an IdP-agnostic core (Zitadel, Auth0, Keycloak, …) and ships with Casbin-friendly helpers.

> 🇫🇷 Cette page existe aussi en [français](../../fr/oihana/auth.md).

---

## When to use it

Reach for this namespace when you need to:

- represent users, roles, permissions and policies for an admin UI or an API,
- describe OAuth2 client applications and the keyfiles issued for them,
- track sessions and revocations across providers,
- bridge to [Casbin](https://casbin.org/) via `toPolicy()` / `toCasbinPolicy()` helpers,
- expose a Service Account (machine identity) backed by a JWT private-key authentication flow (RFC 7523).

The `@context` of every entity in this namespace is `https://schema.oihana.xyz` to distinguish it from pure Schema.org payloads.

---

## Quick example

```php
use xyz\oihana\schema\auth\Application;
use xyz\oihana\schema\auth\Keyfile;
use xyz\oihana\schema\auth\User;
use xyz\oihana\schema\constants\Oihana;

$user = new User
([
    Oihana::NAME    => 'Alice' ,
    Oihana::EMAIL   => 'alice@example.com' ,
    Oihana::STATUS  => 'active' ,
    Oihana::ROLES   => [ 'admin' , 'auditor' ] ,
]);

$application = new Application
([
    Oihana::NAME      => 'Mobile companion' ,
    Oihana::CLIENT_ID => 'app-mobile-01' ,
    Oihana::OWNER     => $user ,
    Oihana::KEYFILE   => new Keyfile([ /* one-shot PRIVATE_KEY_JWT payload */ ]),
]);

echo json_encode( $application , JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
```

---

## Core classes

### Identity & lifecycle

| Class            | Role                                                                                                  |
|------------------|-------------------------------------------------------------------------------------------------------|
| `User`           | Authenticated user — extends `org\schema\Person`, adds activation, login counters, pending-email workflow, blocked-for scope, tokens-invalid-before cutoff, metadata, devices. |
| `Invitation`     | Extends Schema.org `InviteAction` — email invitation lifecycle (pending / accepted / cancelled / expired / revoked). |
| `PasswordReset`  | Extends Schema.org `UpdateAction` — password-reset workflow (token hash, redirect URL, sent timestamp, status). |
| `Session`        | Active connection record — IP, user-agent, token hash, expiration, revocation reason. |

### OAuth2 / OIDC clients

| Class             | Role                                                                                                |
|-------------------|-----------------------------------------------------------------------------------------------------|
| `Application`     | OAuth2 client (PKCE, M2M, public) — owner, scopes, IP whitelist, expiration, disabled-* audit fields, embedded `Keyfile`. |
| `Keyfile`         | Self-sufficient PRIVATE_KEY_JWT keyfile (RFC 7523) — `key`, `keyId`, `userId`, `clientId`, `type`, plus connection metadata (`issuer`, `audience`, `scope`, `apiBaseUrl`). |
| `OAuthClient`     | Mirror of an IdP client (e.g. Zitadel) — resolves an opaque `clientId` to a human-readable label. |
| `WebApplication`  | Browser-facing OAuth2 application (extends Schema.org `WebApplication`). |
| `WebAPI`          | Backend API consumed by applications (audience target of the access tokens). |
| `Service`         | Machine identity / Service Account backed by a Zitadel Machine User — JWT private_key_jwt RFC 7523. |

### Authorization (RBAC)

| Class            | Role                                                                                                  |
|------------------|-------------------------------------------------------------------------------------------------------|
| `Permission`     | Casbin-compatible rule — `subject`, `domain`, `object`, `action`, `effect`. |
| `Role`           | Grouping of permissions and policies — extends `WebAPI`; ships `toPolicy()` / `toCasbinPolicy()`. |
| `Policy`         | RBAC authorization bundle for M2M applications — collects applications, permissions, roles. |
| `PendingRevocation` | Provider-agnostic deferred revocation queue (Zitadel, Magento, Auth0) with retry tracking. |

---

## Constants and enumerations

| Constants class                                 | Purpose                                                                 |
|-------------------------------------------------|-------------------------------------------------------------------------|
| `xyz\oihana\schema\constants\Oihana`            | Aggregator — every Oihana property key reachable via traits (`Oihana::ROLES`, `Oihana::CLIENT_ID`, …). |
| `xyz\oihana\schema\constants\ApplicationType`   | Application type discriminator. |
| `xyz\oihana\schema\constants\CasbinPolicy`      | Field names used by Casbin policy entries. |
| `xyz\oihana\schema\constants\Effect`            | Casbin effect values (`allow` / `deny`). |
| `xyz\oihana\schema\constants\InvitationStatus`  | Invitation lifecycle states (`none`, `pending`, `accepted`, `expired`, `canceled`). |
| `xyz\oihana\schema\constants\JWTAlgorithm`      | JWT signing algorithms (`RS256`, `ES256`, `HS256`, …). |
| `xyz\oihana\schema\constants\JwtClaim`          | Full IANA JWT Claims registry (RFC 7519, OIDC, OAuth2, RFC 7800, RFC 8693). |
| `xyz\oihana\schema\constants\SessionRevocationReason` | Session revocation taxonomy (`LOGOUT`, `TOKENS_REVOKED`, `USER_REVOKED`, `EMERGENCY_REVOKE`, `ORPHANED`, `USER_DELETED`). |
| `xyz\oihana\schema\constants\UserStatus`        | Account lifecycle (`active`, `disabled`). |
| `xyz\oihana\schema\constants\auth\KeyfileType`  | IdP-emitted keyfile types (`application`, `serviceaccount`). |
| `xyz\oihana\schema\constants\auth\TokenRequestField` / `TokenRequestValue` | OAuth2 token endpoint form fields and canonical values. |
| `xyz\oihana\schema\constants\auth\TokenResponseField` | RFC 6749 §5.1 token response field names. |

The trait library under [`xyz\oihana\schema\constants\traits\auth/`](../../src/xyz/oihana/schema/constants/traits/auth) composes per-resource property keys (`ApplicationTrait`, `KeyfileTrait`, `PolicyTrait`, `SessionTrait`, `UserTrait`, …) into the global `AuthTrait`, which is itself composed into [`Oihana`](../../src/xyz/oihana/schema/constants/Oihana.php).

---

## Casbin integration

`Permission`, `Role` and `Policy` all expose a `toPolicy()` method that emits ready-to-load Casbin policy entries:

```php
use xyz\oihana\schema\auth\Permission;
use xyz\oihana\schema\auth\Role;

$role = new Role();
$role->permissions =
[
    new Permission([ 'subject' => 'role:admin' , 'domain' => 'api' , 'object' => '/users' , 'action' => 'GET'  ]),
    new Permission([ 'subject' => 'role:admin' , 'domain' => 'api' , 'object' => '/users' , 'action' => 'POST' ]),
];

$rules = $role->toCasbinPolicy(); // alias of toPolicy()
```

---

## Related reading

- [Getting started](../getting-started.md) — installation, hydration, JSON-LD basics.
- [`org\schema`](../schema-org/README.md) — base vocabulary that `User`, `Application`, `WebAPI`, etc. extend.
- [Casbin documentation](https://casbin.org/) — policy/effect/matcher fundamentals.
- [RFC 7523 — JWT Profile for OAuth 2.0 Client Authentication](https://www.rfc-editor.org/rfc/rfc7523) — backs `Keyfile` and `Service`.
- [API reference](../../../docs).
