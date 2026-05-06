# Oihana PHP Schema library - Change Log

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) and this project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]

### Added

- Adds the SchemaResolver helper class.
- Adds the xyz\oihana\schema\auth namespace
  - Adds the WebApi (extends the schema.org definition), Permission, Role and User classes
  - Adds the Application class (OAuth2/PKCE/M2M client with permissions, IP whitelist, expiration)
  - Adds the Keyfile class (self-sufficient PRIVATE_KEY_JWT keyfile for M2M clients — IdP material `key`, `keyId`, `userId`, `clientId`, `type` plus connection metadata `issuer`, `audience`, `scope`, `apiBaseUrl`)
  - Extends Application with keyId and keyfile properties (active key id and one-shot keyfile payload returned on creation/rotation)
  - Adds the Invitation class (extends Schema.org InviteAction, tracks email invitation lifecycle)
  - Adds the OAuthClient class (Zitadel client mirror, resolves opaque clientId to human-readable label)
  - Adds the Policy class (RBAC authorization bundle for M2M applications, with applications, color, permissions, protected, roles and system properties)
  - Adds the Policy::toPolicy() and Policy::toCasbinPolicy() methods (Casbin-ready policy entries from attached permissions)
  - Adds the Role::toCasbinPolicy() and Permission::toCasbinPolicy() aliases of the existing toPolicy() methods
  - Extends Application with createdBy, disabledAt, disabledBy, disabledReason, lastSeenIP, policies and policiesCount properties
  - Adds the Session class (tracks active connections with IP, user-agent, token hash, expiration, revocation) + the SessionRevocationReason constants
  - Extends Role with color, default, level, policies, policiesCount, protected, system properties
  - Extends User with activated, appMetadata, applications, blockedFor, devices, firstLoginAt,maxLevel, pendingEmail, pendingEmailSince, signedUp and metadata properties
  - Extends User with invitationStatus and status properties (admin lifecycle gating and invitation projection)
  - Extends User with pendingEmailCodeExpiresAt and pendingEmailCodeHash properties (verification code lifecycle for email change flow)
- Adds the JWTAlgorithm constant class
- Adds the InvitationStatus constant class (none, pending, accepted, expired, canceled — user-side projection of the latest invitation lifecycle)
- Adds the UserStatus constant class (active, disabled — admin-controlled login gating, distinct from the immutable activated flag)
- Adds the xyz\oihana\schema\constants\auth namespace
  - Adds the KeyfileType constant class (application, serviceaccount — IdP-emitted keyfile types, backed by ConstantsTrait)
- Adds ItemAvailability
- Adds the PostalAddress::extendedAddress property (new standard property in https://schema.org/PostalAddress)
- Adds the xyz\oihana\schema\places namespace
  - Adds the Site, JobSite, Office, Warehouse classes
- Adds the xyz\oihana\schema\auth\WebApplication class
- Adds the org\schema\actions namespace with the full Schema.org Action type hierarchy (~115 action classes)
- Adds the JSONSerializer tool and integrates it in ThingTrait::jsonSerialize
- Adds the ThingTrait::getReduceOptions method
- Adds the Offer::provider property
- Adds role fields and WebApplication trait in the auth namespace
- Adds the xyz\oihana\schema\constants\traits\auth namespace with property-name traits:
  - ApplicationTrait, InvitationTrait, KeyfileTrait, OAuthClientTrait, PolicyTrait, SessionTrait
  - Extends ApplicationTrait with keyId and keyfile constants
  - Adds the shared property traits: ClientIdTrait (clientId), ProtectedResourceTrait (color, protected, system)
  - Adds the plural collection traits: ApplicationsTrait, PermissionsTrait, PoliciesTrait, RolesTrait, UsersTrait
  - Extends RoleTrait with default, level, policies, policiesCount constants (color, protected, system now provided by ProtectedResourceTrait)
  - Extends UserTrait with activated, appMetadata, applications, blockedFor, devices, firstLoginAt, metadata, signedUp constants
  - Extends UserTrait with invitationStatus and status constants
  - Extends UserTrait with pendingEmailCodeExpiresAt and pendingEmailCodeHash constants
  - Extends KeyfileTrait with apiBaseUrl, audience, issuer, scope and userId constants (removes the now-redundant APP_ID — the Keyfile property still exists, but the constant is provided by OAuthClientTrait)
- Composes the new auth traits into the AuthTrait aggregator
- Adds the xyz\oihana\schema\AuditAction class (auditable action with request tracking and RGPD-compliant logging)
- Adds the xyz\oihana\schema\enumerations\AuditActionType enumeration (CREATE, UPDATE, DELETE, ADD, LOGIN, LOGOUT, REJECT)
- Adds the xyz\oihana\schema\constants\traits\AuditTrait with AuditAction property constants
- Adds the AuditTrait in the Oihana constants class

### Changed

- ThingTrait::jsonSerialize now returns all null properties by default (no compression)
- Refactors the ThingTrait::toArray implementation (removes the $class parameter)
- Refactors ApplicationTrait, OAuthClientTrait, SessionTrait and WebApplicationTrait to consume the shared ClientIdTrait
- Refactors PolicyTrait and RoleTrait to consume the shared ProtectedResourceTrait

### Fixed

- Fixes the areaServed property type to accept integer values
- Fixes Role::toPolicy() crashing when the permissions property is uninitialized
- Fixes PermissionTrait::NAME constant value (was incorrectly set to 'domain' instead of 'name')

### Removed

- Removes the Scope class and ScopeTrait (replaced by Policy)
- Removes the ApplicationTemplate class and ApplicationTemplateTrait (Application lifecycle simplified)

## [1.0.1] - 2025-10-30

### Added

- Adds xyz\oihana\schema (new package)
- Adds xyz\oihana\schema\Log
- Adds xyz\oihana\schema\Pagination

- Adds the org\schema\DublinCore class

- Adds the org\schema\constants\Schema constant

## [1.0.0] - 2025-06-17

### Added

- Adds schema\ folder with all first classes and helpers
