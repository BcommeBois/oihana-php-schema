<?php

namespace xyz\oihana\schema\business\documents;

use oihana\reflect\attributes\HydrateWith;

use org\schema\CompoundPriceSpecification as SchemaCompoundPriceSpecification;
use org\schema\UnitPriceSpecification as SchemaUnitPriceSpecification;

use xyz\oihana\schema\constants\Oihana;

/**
 * The price of a {@see BusinessDocumentLine} : the schema.org
 * {@see SchemaCompoundPriceSpecification}, whose components hydrate into the
 * document {@see UnitPriceSpecification}, so that each one can say whether the
 * document prints it.
 *
 * It keeps the short name of its parent on purpose : a line price serializes
 * with the same `@type` as before.
 *
 * @package xyz\oihana\schema\business\documents
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.5.0
 */
class CompoundPriceSpecification extends SchemaCompoundPriceSpecification
{
    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * The components of the price, applying in parallel : the base price, a
     * discount, an eco-fee, a deposit...
     *
     * Redeclared only to hydrate each raw component into the document
     * {@see UnitPriceSpecification}, which carries `printed` ; the type is the
     * parent's, unchanged.
     *
     * @var null|array|SchemaUnitPriceSpecification
     */
    #[HydrateWith( UnitPriceSpecification::class ) ]
    public null|array|SchemaUnitPriceSpecification $priceComponent ;
}
