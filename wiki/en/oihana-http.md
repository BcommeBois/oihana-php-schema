# `xyz\oihana\schema\http` — Oihana HTTP

The `xyz\oihana\schema\http` namespace provides **structured views of HTTP request metadata**. Its first member, `UserAgentInfo`, normalises the free-form `User-Agent` header into typed fields that are easy to consume, store and audit.

> 🇫🇷 Cette page existe aussi en [français](../fr/oihana-http.md).

---

## When to use it

Pick `UserAgentInfo` whenever you need to **persist or reason about the client** behind a request — typically embedded in a `Session` or an `AuditAction` record :

- attach the parsed browser / OS / device class to a login session,
- flag bot traffic (`isBot`),
- keep the original header verbatim (`raw`) for forensics.

It extends `org\schema\Intangible` : a User-Agent is a transient attribute of a request, not a persistable entity in its own right. It exposes the `@context = 'https://schema.oihana.xyz'` distinguisher in the JSON-LD output.

The DTO is meant to be **populated by the parsing helpers in `oihana/php-http`** (e.g. `oihana\http\helpers\parseUserAgent()`) — this library only defines the shape.

---

## Quick example

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

## Class catalog

| Class           | Extends      | Purpose                                                                                          |
|-----------------|--------------|---------------------------------------------------------------------------------------------------|
| `UserAgentInfo` | `Intangible` | Structured view of an HTTP `User-Agent` header — browser, OS, device class, bot flag, raw string. |

### Properties

| Property         | Type          | Description                                                                       |
|------------------|---------------|-----------------------------------------------------------------------------------|
| `browser`        | `string\|null` | Browser product name (`Chrome`, `Firefox`, `Safari`, `Edge`, …).                 |
| `browserVersion` | `string\|null` | Browser version as a free-form string (`126.0.6478.127`).                         |
| `os`             | `string\|null` | Operating system name (`Windows`, `macOS`, `Linux`, `Android`, `iOS`).            |
| `osVersion`      | `string\|null` | Operating system version as a free-form string (`10`, `14.5`).                    |
| `deviceType`     | `string\|null` | Coarse device class — a `DeviceType` constant. Stored as a string for forward-compatibility. |
| `isBot`          | `bool\|null`   | Whether the agent is a bot, crawler or other automated client.                    |
| `raw`            | `string\|null` | The original `User-Agent` header value, preserved verbatim for audit / forensics. |

For exhaustive property lists, browse the source under [`src/xyz/oihana/schema/http/`](../../src/xyz/oihana/schema/http) or the [API reference](../../docs).

---

## Related constants

Property keys are exposed by the [`UserAgentInfoTrait`](../../src/xyz/oihana/schema/constants/traits/http/UserAgentInfoTrait.php) trait, composed into the domain-level [`HttpTrait`](../../src/xyz/oihana/schema/constants/traits/HttpTrait.php) aggregator and wired into the master [`Oihana`](../../src/xyz/oihana/schema/constants/Oihana.php) class. You can therefore reach them through `Oihana::BROWSER`, `Oihana::DEVICE_TYPE`, `Oihana::IS_BOT`, etc.

The `deviceType` values come from the [`DeviceType`](../../src/xyz/oihana/schema/constants/http/DeviceType.php) constant class : `bot`, `desktop`, `mobile`, `tablet`, `unknown`.

---

## Related reading

- [`xyz\oihana\schema`](oihana-core.md) — `AuditAction` and the cross-cutting types a `UserAgentInfo` is embedded in.
- [`org\schema`](schema-org/README.md) — `Intangible` and the Schema.org base.
- [Getting started](getting-started.md) — installation, hydration, JSON-LD basics.
- [API reference](../../docs).
