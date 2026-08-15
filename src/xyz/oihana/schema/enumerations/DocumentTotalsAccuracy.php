<?php

namespace xyz\oihana\schema\enumerations;

use org\schema\Enumeration;

/**
 * How the monetary summary of a business document is to be read — as the exact
 * amounts, or as a floor the final ones will exceed.
 *
 * A document is not always able to price everything it carries. A line whose
 * price list has nothing to say about it, a regulatory fee no rate covers, a
 * carriage whose term names neither a rate nor a threshold : each of them is
 * owed and none of them can be quantified. The honest arithmetic is to add
 * nothing for them — a zero would claim that nothing is due — and the amounts
 * that come out are then true but incomplete.
 *
 * ⚠️ **Nothing in the amounts themselves says so.** Read alone, a
 * {@see \xyz\oihana\schema\business\documents\DocumentTotals} whose total is a
 * floor is indistinguishable from one whose total is final, and an interface
 * printing it announces a figure that will be exceeded. That is the single
 * question this enumeration answers, and it is carried by
 * {@see \xyz\oihana\schema\business\documents\BusinessDocument::$totalsAccuracy}
 * so that a reader holding the summary alone — a list, a total bar, a printed
 * recap — can tell the two apart without loading a single line.
 *
 * 🔑 **It states the consequence, not the cause.** Every reason a document has
 * to be short falls under the same word, and the reasons themselves stay where
 * they happened : on the adjustment that carries no amount, on the line that
 * carries no subtotal.
 *
 * 🔑 **The accuracy answers to the same authority as the amounts.** A mirrored
 * document states what its source billed, and its source priced everything it
 * charged — so the reading is `EXACT`, stated rather than recomputed. Only a
 * document whose amounts are computed here has an accuracy to work out.
 *
 * ⚠️ **An absent value states nothing, and must not be read as `EXACT`.**
 * Unlike {@see BusinessDocumentAuthority}, whose absence carries the safe
 * meaning, silence here is only silence : a document written before the
 * property existed may well be a floor. A producer is expected to always state
 * it, and whoever adds the property to a store is expected to pass over what is
 * already in it.
 *
 * | Constant | Description                                                                         | Value                                                        |
 * |----------|-------------------------------------------------------------------------------------|--------------------------------------------------------------|
 * | EXACT    | The amounts are final — everything the document carries could be priced.            | https://schema.oihana.xyz/DocumentTotalsAccuracy#Exact       |
 * | MINIMUM  | The amounts are a floor — something the document carries could not be priced.       | https://schema.oihana.xyz/DocumentTotalsAccuracy#Minimum     |
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\enumerations
 * @since   1.4.0
 */
class DocumentTotalsAccuracy extends Enumeration
{
    /**
     * The amounts are final — everything the document carries could be priced.
     */
    public const string EXACT = 'https://schema.oihana.xyz/DocumentTotalsAccuracy#Exact' ;

    /**
     * The amounts are a floor — the document carries something nobody could price, so what is
     * finally due is at least what they say, and more.
     */
    public const string MINIMUM = 'https://schema.oihana.xyz/DocumentTotalsAccuracy#Minimum' ;
}
