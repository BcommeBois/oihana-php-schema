# `xyz\oihana\schema\auth` — Authentification, identité & RBAC

Le namespace `xyz\oihana\schema\auth` modélise tout ce dont les applications Oihana ont besoin autour de **l'identité, des sessions, des clients OAuth2/OIDC et du contrôle d'accès par rôles** (RBAC). Il est conçu autour d'un cœur agnostique du fournisseur d'identité (Zitadel, Auth0, Keycloak, …) et fournit des helpers compatibles Casbin.

> 🇬🇧 This page is also available in [English](../en/oihana-auth.md).

---

## Quand l'utiliser

Utilisez ce namespace dès que vous avez besoin de :

- représenter des utilisateurs, rôles, permissions et politiques pour une UI d'administration ou une API,
- décrire des applications clientes OAuth2 et les keyfiles qui leur sont émis,
- suivre les sessions et leurs révocations à travers plusieurs fournisseurs,
- vous interfacer avec [Casbin](https://casbin.org/) via les helpers `toPolicy()` / `toCasbinPolicy()`,
- exposer un Service Account (identité machine) appuyé sur un flux d'authentification par JWT à clé privée (RFC 7523).

Le `@context` de chaque entité de ce namespace est `https://schema.oihana.xyz` afin de la distinguer des charges utiles purement Schema.org.

---

## Exemple express

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
    Oihana::KEYFILE   => new Keyfile([ /* charge utile PRIVATE_KEY_JWT one-shot */ ]),
]);

echo json_encode( $application , JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
```

---

## Classes principales

### Identité & cycle de vie

| Classe           | Rôle                                                                                                  |
|------------------|-------------------------------------------------------------------------------------------------------|
| `User`           | Utilisateur authentifié — étend `org\schema\Person`, ajoute l'activation, les compteurs de login, le workflow pending-email, le scope blocked-for, le cutoff tokens-invalid-before, les métadonnées et les devices. |
| `Invitation`     | Étend `InviteAction` de Schema.org — cycle de vie d'invitation par email (pending / accepted / cancelled / expired / revoked). |
| `PasswordReset`  | Étend `UpdateAction` de Schema.org — workflow de réinitialisation (hash de token, URL de redirection, horodatage d'envoi, statut). |
| `Session`        | Enregistrement de connexion active — IP, user-agent, hash de token, expiration, raison de révocation. |

### Clients OAuth2 / OIDC

| Classe            | Rôle                                                                                                 |
|-------------------|------------------------------------------------------------------------------------------------------|
| `Application`     | Client OAuth2 (PKCE, M2M, public) — propriétaire, scopes, IP whitelist, expiration, champs d'audit `disabled*`, `Keyfile` embarquée. |
| `Keyfile`         | Keyfile PRIVATE_KEY_JWT auto-suffisant (RFC 7523) — `key`, `keyId`, `userId`, `clientId`, `type`, plus métadonnées de connexion (`issuer`, `audience`, `scope`, `apiBaseUrl`). |
| `OAuthClient`     | Miroir d'un client côté IdP (par exemple Zitadel) — résout un `clientId` opaque en libellé humainement lisible. |
| `WebApplication`  | Application OAuth2 côté navigateur (étend `WebApplication` de Schema.org). |
| `WebAPI`          | API backend consommée par les applications (audience cible des tokens d'accès). |
| `Service`         | Identité machine / Service Account adossé à un Machine User Zitadel — JWT private_key_jwt RFC 7523. |

### Autorisation (RBAC)

| Classe           | Rôle                                                                                                  |
|------------------|-------------------------------------------------------------------------------------------------------|
| `Permission`     | Règle compatible Casbin — `subject`, `domain`, `object`, `action`, `effect`. |
| `Role`           | Regroupement de permissions et politiques — étend `WebAPI` ; fournit `toPolicy()` / `toCasbinPolicy()`. |
| `Policy`         | Bundle d'autorisation RBAC pour les applications M2M — réunit applications, permissions et rôles. |
| `PendingRevocation` | File de révocation différée agnostique du fournisseur (Zitadel, Magento, Auth0) avec suivi de tentatives. |

---

## Constantes et énumérations

| Classe de constantes                            | Rôle                                                                    |
|-------------------------------------------------|-------------------------------------------------------------------------|
| `xyz\oihana\schema\constants\Oihana`            | Agrégateur — chaque clé de propriété Oihana joignable via les traits (`Oihana::ROLES`, `Oihana::CLIENT_ID`, …). |
| `xyz\oihana\schema\constants\ApplicationType`   | Discriminateur de type d'application. |
| `xyz\oihana\schema\constants\CasbinPolicy`      | Noms des champs utilisés par les entrées de politique Casbin. |
| `xyz\oihana\schema\constants\Effect`            | Effets Casbin (`allow` / `deny`). |
| `xyz\oihana\schema\constants\InvitationStatus`  | États du cycle de vie d'invitation (`none`, `pending`, `accepted`, `expired`, `canceled`). |
| `xyz\oihana\schema\constants\JWTAlgorithm`      | Algorithmes de signature JWT (`RS256`, `ES256`, `HS256`, …). |
| `xyz\oihana\schema\constants\JwtClaim`          | Registre IANA JWT Claims complet (RFC 7519, OIDC, OAuth2, RFC 7800, RFC 8693). |
| `xyz\oihana\schema\constants\SessionRevocationReason` | Taxonomie de révocation de session (`LOGOUT`, `TOKENS_REVOKED`, `USER_REVOKED`, `EMERGENCY_REVOKE`, `ORPHANED`, `USER_DELETED`). |
| `xyz\oihana\schema\constants\UserStatus`        | Cycle de vie du compte (`active`, `disabled`). |
| `xyz\oihana\schema\constants\auth\KeyfileType`  | Types de keyfile émis par l'IdP (`application`, `serviceaccount`). |
| `xyz\oihana\schema\constants\auth\TokenRequestField` / `TokenRequestValue` | Champs et valeurs canoniques du formulaire d'endpoint OAuth2 token. |
| `xyz\oihana\schema\constants\auth\TokenResponseField` | Noms des champs de réponse RFC 6749 §5.1. |

La bibliothèque de traits sous [`xyz\oihana\schema\constants\traits\auth/`](../../src/xyz/oihana/schema/constants/traits/auth) compose les clés de propriétés par ressource (`ApplicationTrait`, `KeyfileTrait`, `PolicyTrait`, `SessionTrait`, `UserTrait`, …) dans le trait global `AuthTrait`, lui-même composé dans [`Oihana`](../../src/xyz/oihana/schema/constants/Oihana.php).

---

## Intégration Casbin

`Permission`, `Role` et `Policy` exposent une méthode `toPolicy()` qui produit des entrées de politique prêtes à charger dans Casbin :

```php
use xyz\oihana\schema\auth\Permission;
use xyz\oihana\schema\auth\Role;

$role = new Role();
$role->permissions =
[
    new Permission([ 'subject' => 'role:admin' , 'domain' => 'api' , 'object' => '/users' , 'action' => 'GET'  ]),
    new Permission([ 'subject' => 'role:admin' , 'domain' => 'api' , 'object' => '/users' , 'action' => 'POST' ]),
];

$rules = $role->toCasbinPolicy(); // alias de toPolicy()
```

---

## Pour aller plus loin

- [Démarrage rapide](demarrage.md) — installation, hydratation, bases du JSON-LD.
- [`org\schema`](schema-org/README.md) — vocabulaire de base que `User`, `Application`, `WebAPI`, etc. étendent.
- [Documentation Casbin](https://casbin.org/) — fondamentaux policy/effect/matcher.
- [RFC 7523 — JWT Profile for OAuth 2.0 Client Authentication](https://www.rfc-editor.org/rfc/rfc7523) — base de `Keyfile` et `Service`.
- [Référence d'API](../../docs).
