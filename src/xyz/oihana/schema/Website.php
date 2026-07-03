<?php

namespace xyz\oihana\schema;

use org\schema\Thing;
use xyz\oihana\schema\constants\Oihana;

/**
 * An website information. Based on https://developers.google.com/google-apps/contacts/v3/reference#gcWebsite.
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema
 * @since   1.3.0
 */
class Website extends Thing
{
    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * A link to the website.
     */
    public ?string $href ;

    /**
     * When multiple websites appear in an entry, indicates which is primary. At most one website may be primary. Default value is false.
     */
    public ?bool $primary ;

    /**
     * A programmatic value that identifies the type of a website (related website values).
     * Related values :
     * - home-page : "The home page of the contact."
     * - blog      : "Contact's blog."
     * - profile   : "Contact's profile."
     * - home      : "home-related site."
     * - work      : "work-related site."
     * - other     : "site of other type."
     * - ftp       : "FTP site."
     */
    public ?string $rel ;
}


