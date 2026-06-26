# Oihana PHP Schema library - Change Log

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) and this project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]

### Added

- Adds the `xyz\oihana\schema\thesaurus` namespace and the `ThesaurusTerm` entity (extends the schema.org `DefinedTerm`) — a thesaurus term enriched with house-specific properties (e.g. `color`, a `#RRGGBB` hex string) layered on top of harvested data. These local properties survive a re-harvest because the harvest performs an AQL `UPSERT ... UPDATE` merge that only rewrites the source fields and leaves untouched attributes in place.
  - Adds the companion `xyz\oihana\schema\constants\traits\thesaurus\ThesaurusTermTrait` trait centralising the `ThesaurusTerm` property name constant (`COLOR`), and the domain-level `xyz\oihana\schema\constants\traits\ThesaurusTrait` aggregator (mirrors `AuthTrait`/`HttpTrait`). The aggregator is wired into `xyz\oihana\schema\constants\Oihana`, so the field is reachable via `Oihana::COLOR` (its value matches the `COLOR` keys already exposed by the auth traits).
  - Adds the `tests/xyz/oihana/schema/thesaurus` unit-test suite covering `ThesaurusTerm` defaults, constants and hydration.
- Adds the SKOS hierarchy layer to the `thesaurus` namespace, so the library can model parent/child concept trees that schema.org alone cannot express.
  - Adds `xyz\oihana\schema\thesaurus\Concept` (extends the schema.org `DefinedTerm`, equated by schema.org with `skos:Concept`) carrying the SKOS semantic relations through the new `xyz\oihana\schema\thesaurus\traits\HasSkosRelations` trait: `broader` (the direct parent, `null|string|array|Concept`), `narrower`, `broaderTransitive` and `narrowerTransitive` (`null|string|array`, hydrated element-by-element via `#[HydrateWith(Concept::class)]`). All relations are nullable and traversal-only — never persisted, never harvested, populated only on selected API responses. Each accepts a bare `_key` string, an AQL-projected associative array (rebuildable via `new Concept($array)`) or a hydrated object.
  - Adds `xyz\oihana\schema\thesaurus\ProductCategoryTerm` (extends `Concept`, `use HasColor`) — the only thesaurus family that is both hierarchical and colored; flat families stay on `ThesaurusTerm`.
  - Adds the companion `xyz\oihana\schema\constants\traits\thesaurus\ConceptTrait` (constants `BROADER`, `BROADER_TRANSITIVE`, `NARROWER`, `NARROWER_TRANSITIVE`), aggregated through `ThesaurusTrait` into `Oihana` (reachable as `Oihana::BROADER`, etc.).
  - Adds the `ConceptTest` and `ProductCategoryTermTest` suites, covering defaults, constants, the `Oihana` aggregation, and both hydration paths (raw constructor assignment vs `#[HydrateWith]` honored by `Reflection::hydrate()`).

### Changed

- Extracts the `ThesaurusTerm::$color` property into a reusable `xyz\oihana\schema\thesaurus\traits\HasColor` trait, so the color hint can be composed by several thesaurus entities without duplication. `ThesaurusTerm` now `use`s this trait — no change to its public surface (the `color` property and the `ThesaurusTerm::COLOR` constant are unchanged).

## [1.1.0] - 2026-06-23

### Added

- Adds the bilingual wiki guides for the `xyz\oihana\schema\business` (`oihana-business.md`) and `xyz\oihana\schema\http` (`oihana-http.md`) namespaces (EN/FR), and lists both in the README `🗂️ Schemas overview` table and the wiki indexes.
- Adds the `Docs` GitHub Actions workflow that builds the phpDocumentor site (`composer doc`) and deploys it to GitHub Pages on every push to `main`. The generated `docs/` output is no longer tracked in git (now gitignored), and `phpdoc.xml` is bumped to `1.1.0`. Uses the Node 24 majors of the Pages actions (`configure-pages@v6`, `upload-pages-artifact@v5`, `deploy-pages@v5`) to avoid the Node 20 deprecation warnings.
- Adds the `xyz\oihana\schema\business` namespace and the `BusinessIdentity` entity (extends `Intangible`) — the typed link between an authenticated account and a business entity (a `Person` or an `Organization`), exposed through a single `subject` property. Keeps the account and its linked entity decoupled (no data merging); `subject` is a resolved reference, never a copy. The identity type is **derived** from the subject (its Schema.org `additionalType`), not stored on the link. `subject` accepts a hydrated object (`Person` / `Organization` / `Thing`), a scalar reference, **or** a raw associative `array` (an AQL-projected reference) — the hydrator does not force a class, so a downstream project can subclass and override the property with `#[HydrateWith]` if it wants a fixed type.
  - Adds the companion `xyz\oihana\schema\constants\traits\business\BusinessIdentityTrait` trait centralising the `BusinessIdentity` property name constant (`SUBJECT`). It is intentionally **not** aggregated into `xyz\oihana\schema\constants\Oihana` because its `SUBJECT` key collides with the already-aggregated `auth\PermissionTrait::SUBJECT`.
  - Adds the `identities` property (array of `BusinessIdentity`, hydrated via `#[HydrateWith]`) to `xyz\oihana\schema\auth\User`, with the companion `UserTrait::IDENTITIES` constant. An account may hold several identities (e.g. both a seller and a customer contact).
  - Adds neutral read accessors so consumers can navigate the link without re-implementing the lookup logic : `BusinessIdentity::subjectType()` (the subject's `additionalType`), `BusinessIdentity::isType()` (type test), `BusinessIdentity::subjectKey()` and `BusinessIdentity::worksForKey()` (resolve the `_key`/`id` of the subject, or of the organization it works for), plus `User::firstIdentityBySubjectType()` and `User::identitiesBySubjectType()` to filter the account's identities by subject type. Every accessor handles a `subject` that is either a hydrated object or a raw associative array.
  - Adds the `tests/xyz/oihana/schema/business` unit-test suite covering `BusinessIdentity` and the new `User::$identities` field.
  - Adds the `UserProfile` entity (extends `Intangible`) — a creation-time template that pairs an authorization `role` with the `expectedType` (`additionalType`) of the person the account will be linked to, plus a `color` UI hint. It carries no per-account state : it is consumed once, at creation time, to grant a role and attach a business identity. `role` is a resolved reference accepting a hydrated `Role` object, a scalar role key / name, **or** a raw associative `array` (an AQL-projected reference).
  - Adds the companion `xyz\oihana\schema\constants\traits\business\UserProfileTrait` trait centralising the `UserProfile` property name constants (`COLOR`, `EXPECTED_TYPE`, `ROLE`). Like `BusinessIdentityTrait`, it is intentionally **not** aggregated into `xyz\oihana\schema\constants\Oihana`; both are composed into the `xyz\oihana\schema\constants\traits\BusinessTrait` aggregator instead.
  - Adds the `tests/xyz/oihana/schema/business/UserProfileTest` unit-test suite covering the `UserProfile` defaults, constants and hydration.
- Adds the `xyz\oihana\schema\http` namespace and the `UserAgentInfo` DTO (extends `Intangible`) — structured view of an HTTP `User-Agent` header with `browser`, `browserVersion`, `os`, `osVersion`, `deviceType`, `isBot` and `raw` properties. Designed to be populated by the parsing helpers in `oihana/php-http` and to be embedded in `Session` / `AuditAction` records.
  - Adds the companion `xyz\oihana\schema\constants\traits\http\UserAgentInfoTrait` trait centralising the property name constants.
  - Adds the `xyz\oihana\schema\constants\http\DeviceType` constant class (`bot`, `desktop`, `mobile`, `tablet`, `unknown`) used by `UserAgentInfo::$deviceType`.
  - Adds the domain-level `xyz\oihana\schema\constants\traits\HttpTrait` aggregator (mirrors `AuthTrait`'s pattern) and wires it into `xyz\oihana\schema\constants\Oihana`, so the new HTTP field constants are reachable via `Oihana::BROWSER`, `Oihana::DEVICE_TYPE`, etc.
- Adds the com\progress\schema namespace — object-oriented mapping of the OpenEdge Progress SQL system catalog tables (~16 classes under com\progress\schema\system)
  - Adds Table (SYSTABLES), Column (SYSCOLUMNS) and Index (SYSINDEXES) — refactored with full PHPDoc, corrected `Column::$columnType` typing (previously mislabelled as the table type discriminator), and added the missing Progress columns (`columnId`, `precision`, `radix`, `format`, `label`, `mandatory`, `caseSensitive`, `decimal`, `numberOfRows`, `percentTouched`, `recordSize`, `tableAttributes`, `updateStats`, `ascDesc`, `fieldNumber`, `indexOwner`, `indexSequence`, `numberOfComponents`, `primary`, `unique`)
  - Adds the View class (SYSVIEWS — checkOption, textLength, viewText)
  - Adds the User class (SYSDBAUTH — grantee, grantor, dbaAccess, resourceAccess — de facto OpenEdge SQL user list)
  - Adds the Sequence class (SYSSEQUENCES — cycle, increment, initialValue, minValue, maxValue, sequenceOwner)
  - Adds the Synonym class (SYSSYNONYMS — baseTable, baseTableOwner, synonymOwner)
  - Adds the Procedure class (SYSPROCEDURES — procedureId, procedureOwner, numberOfArguments, returnType, remarks, procedureText)
  - Adds the Trigger class (SYSTRIGGER — event I/U/D, forEach R/S, timing B/A, triggerOwner, triggerText)
  - Adds the TableConstraint, CheckConstraint, ReferentialConstraint and KeyColumnUsage classes (SYS_TBL_CONSTRS, SYS_CHK_CONSTRS, SYS_REF_CONSTRS, SYS_KEYCOL_USAGE — full constraint metadata including matchType, updateRule, deleteRule, keySequence, deferrability)
  - Adds the TableAuth and ColumnAuth classes (SYSTABAUTH, SYSCOLAUTH — per-table and per-column GRANT flags: select, insert, update, delete, references, index, alter)
  - Adds the DataType class (SYSDATATYPES — typeCode, columnLength, dataTypePrecision, dataTypeRadix)
  - Adds the com\progress\schema\constants\Progress aggregator class with `Progress::SCHEMA = 'https://schema.progress.com'`
  - Adds 13 specialized constant traits (Authorization, Column, Common, Constraint, DataType, Index, Procedure, Sequence, Synonym, Table, Trigger, User, View) composed into a single Properties trait
  - Registers the PSR-4 autoload entry `com\progress\\` → `src/com/progress`
  - Adds the tests/com/progress unit-test suite (17 test classes, ~80 tests, 317 assertions) covering every system class and the Progress constants aggregator
- Adds a hand-written bilingual EN/FR wiki under `wiki/` complementing the phpDocumentor API reference
  - Adds wiki/en and wiki/fr language folders with reciprocal cross-links, getting-started guides (`getting-started.md` / `demarrage.md`) and per-namespace guides for `xyz\oihana\schema\auth`, `xyz\oihana\schema\places`, `xyz\oihana\schema` (cross-cutting types) and `com\progress\schema`
  - Splits the `org\schema` guide under `schema-org/` into one sub-page per sub-namespace (`core`, `actions`, `creative-work`, `events`, `places`, `organizations`, `services`, `items`, `enumerations`)
- Adds a `🗂️ Schemas overview` section in README.md with a summary table of all top-level namespaces and `@context` URIs, each row linking to the corresponding bilingual wiki guide
- Adds the SchemaResolver helper class.
- Adds the xyz\oihana\schema\auth namespace
  - Adds the WebApi (extends the schema.org definition), Permission, Role and User classes
  - Adds the Application class (OAuth2/PKCE/M2M client with permissions, IP whitelist, expiration)
  - Adds the Keyfile class (self-sufficient PRIVATE_KEY_JWT keyfile for M2M clients — IdP material `key`, `keyId`, `userId`, `clientId`, `type` plus connection metadata `issuer`, `audience`, `scope`, `apiBaseUrl`)
  - Extends Application with keyId and keyfile properties (active key id and one-shot keyfile payload returned on creation/rotation)
  - Adds the Invitation class (extends Schema.org InviteAction, tracks email invitation lifecycle)
  - Adds the PasswordReset class (extends Schema.org UpdateAction — password-reset request lifecycle: token hash, email, redirectUrl, sentAt ; action-status pending / consumed / expired / cancelled)
  - Adds the PendingRevocation class (provider-agnostic deferred revocation queue — attempts, lastAttemptAt, lastError, provider, reason, targetId, targetType, userIdentifier, userKey) for retrying failed IdP revocations (Zitadel, Magento, Auth0)
  - Adds the OAuthClient class (Zitadel client mirror, resolves opaque clientId to human-readable label)
  - Adds the Policy class (RBAC authorization bundle for M2M applications, with applications, color, permissions, protected, roles and system properties)
  - Adds the Policy::toPolicy() and Policy::toCasbinPolicy() methods (Casbin-ready policy entries from attached permissions)
  - Adds the Role::toCasbinPolicy() and Permission::toCasbinPolicy() aliases of the existing toPolicy() methods
  - Extends Application with createdBy, disabledAt, disabledBy, disabledReason, lastSeenIP, policies and policiesCount properties
  - Adds the Service class (machine identity / Service Account backed by a Zitadel Machine User — JWT private_key_jwt RFC 7523 ; clientId, keyId, keyfile, allowedIPs, expiresAt, lastSeenIP, lastUsedAt, permissions, policies, protected and disabled* audit fields)
  - Adds the Session class (tracks active connections with IP, user-agent, token hash, expiration, revocation) + the SessionRevocationReason constants
  - Extends SessionRevocationReason with the TOKENS_REVOKED constant (surfaced by the auth middleware when a token `iat` predates User::$tokensInvalidBefore)
  - Extends SessionRevocationReason with the USER_REVOKED constant (session explicitly invalidated by the user, e.g. "Sign out from other devices" — distinct from LOGOUT which terminates the current authenticated context)
  - Extends SessionRevocationReason with the EMERGENCY_REVOKE constant (session terminated as part of an emergency security response — incident response, confirmed compromise, automated threat-mitigation workflow)
  - Extends SessionRevocationReason with the ORPHANED constant (session no longer references a valid owning entity — surfaced by background cleanup jobs and referential-integrity sweeps, distinct from USER_DELETED which records an intentional user-deletion event)
  - Extends Role with color, default, level, policies, policiesCount, protected, system properties
  - Extends User with activated, appMetadata, applications, blockedFor, devices, firstLoginAt,maxLevel, pendingEmail, pendingEmailSince, signedUp and metadata properties
  - Extends User with invitationStatus and status properties (admin lifecycle gating and invitation projection)
  - Extends User with pendingEmailCodeExpiresAt and pendingEmailCodeHash properties (verification code lifecycle for email change flow)
  - Extends User with color, protected and system properties (admin display color and write/delete protection flags, provided by ProtectedResourceTrait)
  - Extends User with tokensInvalidBefore property (epoch-seconds cutoff used by the auth middleware to reject access tokens whose `iat` predates a bulk session revocation)
- Adds the JWTAlgorithm constant class
- Adds the JwtClaim constant class — full IANA JSON Web Token Claims registry coverage:
  - RFC 7519 §4.1 registered claims exposed under both short and long aliases (`ISS`/`ISSUER`, `SUB`/`SUBJECT`, `AUD`/`AUDIENCE`, `EXP`/`EXPIRES_AT`, `NBF`/`NOT_BEFORE`, `IAT`/`ISSUED_AT`, `JTI`/`JWT_ID`)
  - OAuth 2.0 / OIDC common claims (`azp`, `nonce`, `auth_time`, `acr`, `amr`, `scope`, `scp`, `client_id`)
  - OIDC Session Management (`sid` / `SESSION_ID` — Front-Channel / Back-Channel Logout)
  - OIDC ID Token validation hashes (`at_hash`, `c_hash`)
  - OIDC Core §5.1 standard profile claims (`name`, `given_name`, `family_name`, `middle_name`, `nickname`, `preferred_username`, `profile`, `picture`, `website`, `email`, `email_verified`, `gender`, `birthdate`, `zoneinfo`, `locale`, `phone_number`, `phone_number_verified`, `address`, `updated_at`)
  - RFC 8693 Token Exchange (`act`, `may_act`)
  - RFC 7800 Proof-of-Possession (`cnf`)
  - Provider-specific / non-standard (`groups`, `roles`, `entitlements`, `tid`, `oid`)
- Adds the InvitationStatus constant class (none, pending, accepted, expired, canceled — user-side projection of the latest invitation lifecycle)
- Adds the UserStatus constant class (active, disabled — admin-controlled login gating, distinct from the immutable activated flag)
- Adds the xyz\oihana\schema\constants\auth namespace
  - Adds the KeyfileType constant class (application, serviceaccount — IdP-emitted keyfile types, backed by ConstantsTrait)
  - Adds the TokenRequestField constant class (OAuth2 token endpoint form fields — assertion, client_assertion, client_assertion_type, grant_type, scope)
  - Adds the TokenRequestValue constant class (canonical values paired with TokenRequestField — DEFAULT_SCOPE, GRANT_CLIENT_CREDENTIALS, GRANT_JWT_BEARER, JWT_BEARER_ASSERTION_TYPE)
  - Adds the TokenResponseField constant class (RFC 6749 §5.1 successful response fields, plus OIDC and vendor extensions — access_token, expires_at, expires_in, id_token, refresh_token, scope, token_type)
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
  - ApplicationTrait, InvitationTrait, KeyfileTrait, OAuthClientTrait, PasswordResetTrait, PendingRevocationTrait, PolicyTrait, ServiceTrait, SessionTrait
  - Extends ApplicationTrait with keyId and keyfile constants
  - Adds the shared property traits: ClientIdTrait (clientId), ProtectedResourceTrait (color, protected, system)
  - Adds the plural collection traits: ApplicationsTrait, PermissionsTrait, PoliciesTrait, RolesTrait, ServicesTrait, UsersTrait
  - Composes ServicesTrait into PolicyTrait and UserTrait (services and servicesCount constants now reachable through Policy and User)
  - Extends RoleTrait with default, level, policies, policiesCount constants (color, protected, system now provided by ProtectedResourceTrait)
  - Extends UserTrait with activated, appMetadata, applications, blockedFor, devices, firstLoginAt, metadata, signedUp constants
  - Extends UserTrait with invitationStatus and status constants
  - Extends UserTrait with pendingEmailCodeExpiresAt and pendingEmailCodeHash constants
  - Extends UserTrait with tokensInvalidBefore constant (paired with the new User::$tokensInvalidBefore property)
  - Extends KeyfileTrait with apiBaseUrl, audience, issuer, scope and userId constants (removes the now-redundant APP_ID — the Keyfile property still exists, but the constant is provided by OAuthClientTrait)
- Composes the new auth traits into the AuthTrait aggregator
- Adds the xyz\oihana\schema\AuditAction class (auditable action with request tracking and RGPD-compliant logging)
- Extends AuditAction with event and outcome properties (business event tag and machine-readable result of the action)
- Adds the xyz\oihana\schema\enumerations\AuditActionType enumeration (CREATE, UPDATE, DELETE, ADD, LOGIN, LOGOUT, REJECT)
- Adds the xyz\oihana\schema\constants\traits\AuditTrait with AuditAction property constants
- Extends AuditTrait with event and outcome constants
- Adds the AuditTrait in the Oihana constants class

### Changed

- `SchemaResolver` now accepts a **list** of types for the discriminator (multi-typed documents), resolved by **map declaration order** (consistent with `php-arango`'s `FederatedSearch`), falling back to the default; scalar resolution unchanged.
- ThingTrait::jsonSerialize now returns all null properties by default (no compression)
- Refactors the ThingTrait::toArray implementation (removes the $class parameter)
- Refactors ApplicationTrait, OAuthClientTrait, SessionTrait and WebApplicationTrait to consume the shared ClientIdTrait
- Refactors PolicyTrait, RoleTrait and UserTrait to consume the shared ProtectedResourceTrait
- Extracts the `$color`, `$protected` and `$system` properties into the new HasProtectedResource property trait (xyz\oihana\schema\auth\traits) — applied to Permission, User and WebAPI (inherited by Policy and Role), removing the duplicated inline declarations
- Refactors PermissionTrait and WebAPITrait to consume the shared ProtectedResourceTrait (exposes COLOR, PROTECTED, SYSTEM constants for consistency with PolicyTrait, RoleTrait and UserTrait)
- Extends Permission with color, protected and system properties (admin display color and write/delete protection flags, provided by HasProtectedResource)
- Extends Policy with services and servicesCount properties (inbound services referencing this policy)

### Fixed

- Fixes `DataFeed` and `DataCatalog` referencing their related type as `DataSet` while the actual class is `Dataset` (the schema.org casing, https://schema.org/Dataset). On a case-sensitive filesystem (e.g. Linux CI) the PSR-4 autoloader could not resolve `DataSet.php`, making `DataFeed` a fatal load error. References now match the real class name.
- Fixes `ProductCollection` being impossible to instantiate (fatal error): it extends `Product` (which declares `$funding` as `null|string|array|Grant`) while also using `CreativeWorkTrait`, whose `$funding` was the incompatible `null|Grant|array`. The trait property is now `null|string|array|Grant`, so the composition is valid.
- Fixes the areaServed property type to accept integer values
- Fixes Role::toPolicy() crashing when the permissions property is uninitialized
- Fixes PermissionTrait::NAME constant value (was incorrectly set to 'domain' instead of 'name')
- Fixes Application using the plural ApplicationsTrait (collection constants APPLICATIONS, APPLICATIONS_COUNT) instead of the singular ApplicationTrait — Application::ALLOWED_IPS, KEY_ID, KEYFILE, etc. now resolve correctly
- Fixes SessionRevocationReason::USER_DELETED value (was incorrectly set to 'user_disabled', now resolves to 'user_deleted')

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
