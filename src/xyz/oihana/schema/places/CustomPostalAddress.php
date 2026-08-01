<?php

namespace xyz\oihana\schema\places;

use org\schema\PostalAddress;

use xyz\oihana\schema\constants\Oihana;

/**
 * A postal address dictated on the spot rather than picked from an address book.
 *
 * Some delivery addresses describe a place no record covers — a one-off site, a
 * neighbour's door, a yard with no number. This type exists so a frozen copy can
 * *say* so in its `additionalType`, instead of leaving it to be inferred from the
 * absence of a reference key : an inference that flips the day such addresses do
 * get recorded.
 *
 * It carries no property of its own and is never hydrated into — its whole purpose
 * is the type name {@see PostalAddress::getSchemaType()} derives from the redefined
 * {@see CustomPostalAddress::CONTEXT}, namely `https://schema.oihana.xyz/CustomPostalAddress`.
 * For that reason it must not appear in any property union.
 *
 * @see https://schema.org/PostalAddress
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\places
 * @since   1.4.0
 */
class CustomPostalAddress extends PostalAddress
{
    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;
}
