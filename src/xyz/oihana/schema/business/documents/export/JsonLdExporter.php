<?php

namespace xyz\oihana\schema\business\documents\export;

use JsonException;

use xyz\oihana\schema\business\documents\BusinessDocument;

/**
 * A trivial {@see BusinessDocumentExporter} that serializes a document to
 * its JSON-LD representation, via {@see \org\schema\traits\ThingTrait::jsonSerialize()}
 * (inherited through `Intangible`/`Thing`).
 *
 * Demonstration purposes only — no UBL/Factur-X/Peppol mapping, no PDF/HTML
 * rendering ; those regulatory/presentation formats are out of scope of this
 * namespace for now.
 *
 * @package xyz\oihana\schema\business\documents\export
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.3.0
 */
class JsonLdExporter implements BusinessDocumentExporter
{
    /**
     * @throws JsonException
     */
    public function export( BusinessDocument $document ) : string
    {
        return json_encode( $document , JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) ;
    }
}
