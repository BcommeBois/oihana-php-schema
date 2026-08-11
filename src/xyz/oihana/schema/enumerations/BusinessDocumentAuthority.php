<?php

namespace xyz\oihana\schema\enumerations;

use org\schema\Enumeration;

/**
 * Which system holds the truth about a business document — ours, or the one it
 * was harvested from.
 *
 * Orthogonal to everything else the document says about itself : its class
 * (quote, invoice...) tells what it *is*, its
 * {@see BusinessDocumentDirection} tells which side of the trade we stand on,
 * and its {@see BusinessDocumentStatus} tells where it stands in its own
 * lifecycle. None of them tells **who is allowed to change it**.
 *
 * The distinction only becomes visible when documents of both origins share a
 * collection : a quote drafted here and an invoice mirrored from an ERP look
 * alike, yet a write on the second is meaningless — the next harvest would
 * overwrite it, silently, and the person who made the correction would never
 * learn that it was lost.
 *
 * ⚠️ **This enumeration states the fact ; it does not enforce it.** Refusing
 * the write belongs to whoever exposes the document — a schema carries meaning,
 * not permissions.
 *
 * 🔑 **An absent value means the document is ours.** Documents written before
 * this property existed keep the meaning they were stored with, and only what
 * is harvested has to say so explicitly.
 *
 * | Constant | Description                                                          | Value                                                          |
 * |----------|----------------------------------------------------------------------|----------------------------------------------------------------|
 * | OWNED    | Our own store is the source of truth ; the document is editable here. | https://schema.oihana.xyz/BusinessDocumentAuthority#Owned      |
 * | MIRRORED | An external system is the source of truth ; we hold a read-only copy. | https://schema.oihana.xyz/BusinessDocumentAuthority#Mirrored   |
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\enumerations
 * @since   1.4.0
 */
class BusinessDocumentAuthority extends Enumeration
{
    /**
     * An external system is the source of truth — the document is a read-only copy, refreshed by whatever brought it in.
     */
    public const string MIRRORED = 'https://schema.oihana.xyz/BusinessDocumentAuthority#Mirrored' ;

    /**
     * Our own store is the source of truth — the document was created here and is edited here.
     */
    public const string OWNED = 'https://schema.oihana.xyz/BusinessDocumentAuthority#Owned' ;
}
