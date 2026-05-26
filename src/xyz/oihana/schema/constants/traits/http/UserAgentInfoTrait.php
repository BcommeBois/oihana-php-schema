<?php

namespace xyz\oihana\schema\constants\traits\http;

/**
 * Property name constants for `xyz\oihana\schema\http\UserAgentInfo`.
 *
 * Centralises the attribute keys exposed by the DTO so consumers do not
 * have to sprinkle magic strings across their code.
 *
 * @package xyz\oihana\schema\constants\traits\http
 * @category HTTP
 * @author  Marc Alcaraz (ekameleon)
 */
trait UserAgentInfoTrait
{
    /**
     * The browser product name (e.g. `'Chrome'`, `'Firefox'`,
     * `'Safari'`, `'Edge'`). `null` when no browser could be
     * identified.
     */
    public const string BROWSER = 'browser' ;

    /**
     * The browser product version as a free-form string
     * (e.g. `'126.0.6478.127'`). `null` when not extracted.
     */
    public const string BROWSER_VERSION = 'browserVersion' ;

    /**
     * The coarse-grained device classification — one of the
     * `xyz\oihana\schema\constants\http\DeviceType` constants.
     */
    public const string DEVICE_TYPE = 'deviceType' ;

    /**
     * Whether the User-Agent was identified as belonging to a bot,
     * crawler or other automated agent.
     */
    public const string IS_BOT = 'isBot' ;

    /**
     * The operating system name (e.g. `'Windows'`, `'macOS'`,
     * `'Linux'`, `'Android'`, `'iOS'`). `null` when no OS could be
     * identified.
     */
    public const string OS = 'os' ;

    /**
     * The operating system version as a free-form string
     * (e.g. `'10'`, `'14.5'`). `null` when not extracted.
     */
    public const string OS_VERSION = 'osVersion' ;

    /**
     * The original `User-Agent` request header value, preserved
     * verbatim for audit and forensics.
     */
    public const string RAW = 'raw' ;
}
