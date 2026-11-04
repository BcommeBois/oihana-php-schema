<?php

namespace xyz\oihana\schema\business\documents;

use org\schema\UnitPriceSpecification as SchemaUnitPriceSpecification;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\constants\traits\business\documents\UnitPriceSpecificationTrait;

/**
 * A component of the price of a {@see BusinessDocumentLine} : the schema.org
 * {@see SchemaUnitPriceSpecification}, plus whether the document prints it.
 *
 * It keeps the short name of its parent on purpose : a component serializes
 * with the same `@type` as before, and a consumer reading the type of a price
 * component reads it unchanged.
 *
 * @package xyz\oihana\schema\business\documents
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.5.0
 */
class UnitPriceSpecification extends SchemaUnitPriceSpecification
{
    use UnitPriceSpecificationTrait ;

    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * Whether this component is printed on the document the customer receives.
     *
     * 🔑 **Its absence means it is printed.** Only a component left out says
     * so, with `false`, which keeps every price written before this property
     * existed exactly as true as it was.
     *
     * A source may fold a discount into the price it prints : the customer reads
     * a single net price, while the list price and the discount it was worked
     * out from stay on record. Both truths are worth keeping — the price the
     * customer read, and how it was reached — and this is what tells them apart :
     * the price as printed comes as an ordinary component, the list price and the
     * discount beside it are marked `false`.
     *
     * A reader that leaves out the marked components reads what the customer
     * read. It serializes like any other property : leaving them out is the
     * customer-facing renderer's job, the type does not do it on its own.
     *
     * ⚠️ Not to be confused with {@see BusinessDocumentLine::$includedInTotal} :
     * an unprinted component is no less true than a printed one — it says how
     * the price was reached, only the customer was not shown it.
     *
     * @var bool|null
     * @since 1.5.0
     */
    public ?bool $printed ;
}
