<?php

namespace xyz\oihana\schema\enumerations;

use org\schema\Enumeration;

/**
 * What became of a mirrored business document in the system it was harvested from,
 * once that system no longer holds it where the copy was read.
 *
 * A mirror ({@see BusinessDocumentAuthority::MIRRORED}) is refreshed by reading its
 * source again. When the source moves the document elsewhere — an order delivered and
 * archived, a quote turned into an order, a row recreated under another identity — the
 * copy is no longer read, and nothing in it says so : it keeps telling the state of its
 * last reading. This enumeration says why the copy stopped being refreshed.
 *
 * 🔑 **An absent value means the document is current** : it was read on the last pass,
 * or it is one of ours. Only a copy the source left carries a state.
 *
 * ⚠️ **It records what the harvester observed, not what the source declared.** The
 * reason is read off the documents the harvester holds — a delivery note citing the
 * order, an order citing the quote, another copy of the same number — and it may move
 * from {@see self::REMOVED} to a more precise reason once those documents arrive.
 *
 * | Constant  | Description                                                                       | Value                                                           |
 * |-----------|-----------------------------------------------------------------------------------|-----------------------------------------------------------------|
 * | CONVERTED | A quote the source turned into an order : an order cites it.                      | https://schema.oihana.xyz/BusinessDocumentSourceState#Converted |
 * | DELIVERED | An order the source delivered : a delivery note cites it.                         | https://schema.oihana.xyz/BusinessDocumentSourceState#Delivered |
 * | REMOVED   | The source no longer holds it, and nothing else tells what became of it.          | https://schema.oihana.xyz/BusinessDocumentSourceState#Removed   |
 * | REPLACED  | The source recreated it under another identity : another copy carries its number. | https://schema.oihana.xyz/BusinessDocumentSourceState#Replaced  |
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\enumerations
 * @since   1.5.0
 */
class BusinessDocumentSourceState extends Enumeration
{
    /**
     * A quote the source turned into an order — an order cites it.
     */
    public const string CONVERTED = 'https://schema.oihana.xyz/BusinessDocumentSourceState#Converted' ;

    /**
     * An order the source delivered — a delivery note cites it.
     */
    public const string DELIVERED = 'https://schema.oihana.xyz/BusinessDocumentSourceState#Delivered' ;

    /**
     * The source no longer holds the document, and nothing else tells what became of it.
     */
    public const string REMOVED = 'https://schema.oihana.xyz/BusinessDocumentSourceState#Removed' ;

    /**
     * The source recreated the document under another identity — another copy carries its number.
     */
    public const string REPLACED = 'https://schema.oihana.xyz/BusinessDocumentSourceState#Replaced' ;
}
