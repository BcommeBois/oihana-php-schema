<?php

namespace xyz\oihana\schema\constants\traits;

use xyz\oihana\schema\constants\traits\http\UserAgentInfoTrait;

/**
 * Domain-level aggregator trait composing every HTTP-related field
 * constants trait under `xyz\oihana\schema\constants\traits\http`.
 *
 * Mirrors the pattern of {@see AuthTrait}: per-entity traits stay
 * narrow and re-usable, this trait pulls them together so the
 * top-level {@see \xyz\oihana\schema\constants\Oihana} aggregator only
 * needs one `use` per domain.
 *
 * @package xyz\oihana\schema\constants\traits
 * @category HTTP
 * @author  Marc Alcaraz (ekameleon)
 */
trait HttpTrait
{
    use UserAgentInfoTrait ;
}
