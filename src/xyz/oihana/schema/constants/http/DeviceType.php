<?php

namespace xyz\oihana\schema\constants\http;

use oihana\reflect\traits\ConstantsTrait;

/**
 * Coarse-grained device classification derived from the request
 * `User-Agent` string.
 *
 * Used as the value of `UserAgentInfo::$deviceType` to drive routing,
 * analytics and audit decisions that only need a high-level grouping
 * rather than a full device fingerprint.
 *
 * @package xyz\oihana\schema\constants\http
 * @category HTTP
 * @author  Marc Alcaraz (ekameleon)
 */
class DeviceType
{
    use ConstantsTrait ;

    /**
     * Bot, crawler or other automated agent (Googlebot, Bingbot,
     * monitoring probes, headless test runners…).
     */
    public const string BOT = 'bot' ;

    /**
     * Desktop browser running on a laptop / workstation.
     */
    public const string DESKTOP = 'desktop' ;

    /**
     * Handheld mobile device (smartphone form factor).
     */
    public const string MOBILE = 'mobile' ;

    /**
     * Tablet device (iPad-class form factor).
     */
    public const string TABLET = 'tablet' ;

    /**
     * Device family could not be determined from the User-Agent.
     */
    public const string UNKNOWN = 'unknown' ;
}
