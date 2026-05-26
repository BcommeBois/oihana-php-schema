<?php

namespace xyz\oihana\schema\http;

use org\schema\Intangible;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\constants\traits\http\UserAgentInfoTrait;

/**
 * Structured view of an HTTP request `User-Agent` header.
 *
 * Lightweight DTO populated by the parsing helpers in
 * `oihana/php-http` (e.g. `oihana\http\helpers\parseUserAgent()`). The
 * `User-Agent` header carries free-form vendor strings that are hard
 * to consume programmatically — this DTO normalises the most useful
 * fields (browser, OS, device class, bot flag) while keeping the
 * original string under {@see UserAgentInfo::$raw} for audit and
 * forensics.
 *
 * Intentionally extends Schema.org `Intangible` rather than `Thing`:
 * a User-Agent is not a persistable entity — it is a transient
 * attribute of a request that may be embedded in a `Session` or an
 * `AuditAction`.
 *
 * ### Example
 * ```php
 * use xyz\oihana\schema\http\UserAgentInfo;
 * use xyz\oihana\schema\constants\http\DeviceType;
 *
 * $info = new UserAgentInfo
 * ([
 *     UserAgentInfo::BROWSER         => 'Chrome'  ,
 *     UserAgentInfo::BROWSER_VERSION => '126.0'   ,
 *     UserAgentInfo::OS              => 'macOS'   ,
 *     UserAgentInfo::OS_VERSION      => '14.5'    ,
 *     UserAgentInfo::DEVICE_TYPE     => DeviceType::DESKTOP ,
 *     UserAgentInfo::IS_BOT          => false     ,
 *     UserAgentInfo::RAW             => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 14_5) AppleWebKit/...' ,
 * ]) ;
 * ```
 *
 * @see https://schema.org/Intangible
 *
 * @package xyz\oihana\schema\http
 * @category HTTP
 * @author  Marc Alcaraz (ekameleon)
 */
class UserAgentInfo extends Intangible
{
    use UserAgentInfoTrait ;

    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * The browser product name (e.g. `'Chrome'`, `'Firefox'`,
     * `'Safari'`, `'Edge'`). `null` when no browser could be
     * identified from the `User-Agent` string.
     *
     * @var string|null
     */
    public null|string $browser ;

    /**
     * The browser product version as a free-form string
     * (e.g. `'126.0.6478.127'`). `null` when not extracted.
     *
     * @var string|null
     */
    public null|string $browserVersion ;

    /**
     * The coarse-grained device classification.
     *
     * Expected values are
     * `xyz\oihana\schema\constants\http\DeviceType` constants
     * (`desktop`, `mobile`, `tablet`, `bot`, `unknown`). Stored as
     * a string for forward-compatibility with future device classes
     * a parser may introduce.
     *
     * @var string|null
     */
    public null|string $deviceType ;

    /**
     * Whether the User-Agent was identified as belonging to a bot,
     * crawler or other automated agent.
     *
     * @var bool|null
     */
    public null|bool $isBot ;

    /**
     * The operating system name (e.g. `'Windows'`, `'macOS'`,
     * `'Linux'`, `'Android'`, `'iOS'`). `null` when no OS could be
     * identified.
     *
     * @var string|null
     */
    public null|string $os ;

    /**
     * The operating system version as a free-form string
     * (e.g. `'10'`, `'14.5'`). `null` when not extracted.
     *
     * @var string|null
     */
    public null|string $osVersion ;

    /**
     * The original `User-Agent` request header value, preserved
     * verbatim for audit and forensics.
     *
     * @var string|null
     */
    public null|string $raw ;
}
