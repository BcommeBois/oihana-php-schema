# `xyz\oihana\schema\http` — HTTP Oihana

Le namespace `xyz\oihana\schema\http` fournit des **vues structurées des métadonnées de requête HTTP**. Son premier membre, `UserAgentInfo`, normalise l'en-tête `User-Agent` au format libre en champs typés faciles à consommer, stocker et auditer.

> 🇬🇧 This page is also available in [English](../../en/oihana/http.md).

---

## Quand l'utiliser

Choisissez `UserAgentInfo` dès que vous devez **persister ou raisonner sur le client** à l'origine d'une requête — généralement embarqué dans un enregistrement `Session` ou `AuditAction` :

- rattacher le navigateur / l'OS / la classe d'appareil analysés à une session de connexion,
- signaler le trafic des bots (`isBot`),
- conserver l'en-tête original tel quel (`raw`) à des fins forensiques.

Il étend `org\schema\Intangible` : un User-Agent est un attribut transitoire d'une requête, pas une entité persistable en propre. Il expose le distinguisheur `@context = 'https://schema.oihana.xyz'` dans le JSON-LD.

Ce DTO est destiné à être **rempli par les helpers d'analyse de `oihana/php-http`** (par ex. `oihana\http\helpers\parseUserAgent()`) — cette bibliothèque ne définit que la structure.

---

## Exemple express

```php
use xyz\oihana\schema\http\UserAgentInfo;
use xyz\oihana\schema\constants\http\DeviceType;

$info = new UserAgentInfo
([
    UserAgentInfo::BROWSER         => 'Chrome'  ,
    UserAgentInfo::BROWSER_VERSION => '126.0'   ,
    UserAgentInfo::OS              => 'macOS'   ,
    UserAgentInfo::OS_VERSION      => '14.5'    ,
    UserAgentInfo::DEVICE_TYPE     => DeviceType::DESKTOP ,
    UserAgentInfo::IS_BOT          => false     ,
    UserAgentInfo::RAW             => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 14_5) AppleWebKit/...' ,
]) ;

echo json_encode( $info , JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
```

---

## Catalogue des classes

| Classe          | Étend        | Rôle                                                                                             |
|-----------------|--------------|---------------------------------------------------------------------------------------------------|
| `UserAgentInfo` | `Intangible` | Vue structurée d'un en-tête HTTP `User-Agent` — navigateur, OS, classe d'appareil, indicateur bot, chaîne brute. |

### Propriétés

| Propriété        | Type           | Description                                                                       |
|------------------|----------------|-----------------------------------------------------------------------------------|
| `browser`        | `string\|null` | Nom du navigateur (`Chrome`, `Firefox`, `Safari`, `Edge`, …).                     |
| `browserVersion` | `string\|null` | Version du navigateur sous forme de chaîne libre (`126.0.6478.127`).              |
| `os`             | `string\|null` | Nom du système d'exploitation (`Windows`, `macOS`, `Linux`, `Android`, `iOS`).    |
| `osVersion`      | `string\|null` | Version du système d'exploitation sous forme de chaîne libre (`10`, `14.5`).      |
| `deviceType`     | `string\|null` | Classe d'appareil grossière — une constante `DeviceType`. Stockée en chaîne pour la compatibilité ascendante. |
| `isBot`          | `bool\|null`   | Indique si l'agent est un bot, un crawler ou un autre client automatisé.          |
| `raw`            | `string\|null` | La valeur originale de l'en-tête `User-Agent`, conservée telle quelle pour l'audit / la forensique. |

Pour la liste exhaustive des propriétés, parcourez le code source sous [`src/xyz/oihana/schema/http/`](../../src/xyz/oihana/schema/http) ou la [référence d'API](../../../docs).

---

## Constantes associées

Les clés de propriétés sont exposées par le trait [`UserAgentInfoTrait`](../../src/xyz/oihana/schema/constants/traits/http/UserAgentInfoTrait.php), composé dans l'agrégateur de domaine [`HttpTrait`](../../src/xyz/oihana/schema/constants/traits/HttpTrait.php) et câblé dans la classe maîtresse [`Oihana`](../../src/xyz/oihana/schema/constants/Oihana.php). Vous pouvez donc y accéder via `Oihana::BROWSER`, `Oihana::DEVICE_TYPE`, `Oihana::IS_BOT`, etc.

Les valeurs de `deviceType` proviennent de la classe de constantes [`DeviceType`](../../src/xyz/oihana/schema/constants/http/DeviceType.php) : `bot`, `desktop`, `mobile`, `tablet`, `unknown`.

---

## Pour aller plus loin

- [`xyz\oihana\schema`](core.md) — `AuditAction` et les types transverses dans lesquels un `UserAgentInfo` est embarqué.
- [`org\schema`](../schema-org/README.md) — `Intangible` et la base Schema.org.
- [Démarrage rapide](../demarrage.md) — installation, hydratation, bases du JSON-LD.
- [Référence d'API](../../../docs).
