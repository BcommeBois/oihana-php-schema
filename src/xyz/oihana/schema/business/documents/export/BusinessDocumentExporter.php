<?php

namespace xyz\oihana\schema\business\documents\export;

use xyz\oihana\schema\business\documents\BusinessDocument;

/**
 * Serializes a {@see BusinessDocument} into a specific wire format
 * (JSON-LD, UBL, PDF, HTML...).
 *
 * Regulatory formats (UBL, Factur-X, Peppol...) are out of scope of this
 * namespace for now — see {@see JsonLdExporter} for the only implementation
 * currently provided, a trivial JSON-LD demonstration.
 *
 * @package xyz\oihana\schema\business\documents\export
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.3.0
 */
interface BusinessDocumentExporter
{
    /**
     * Serializes the given business document.
     *
     * @param BusinessDocument $document The document to export.
     *
     * @return string The serialized representation of the document.
     */
    public function export( BusinessDocument $document ) : string ;
}
