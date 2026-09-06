<?php

namespace xyz\oihana\schema\enumerations;

use org\schema\Enumeration;

/**
 * Where the quantity of a document line comes from — worked out by the system,
 * or typed by someone.
 *
 * Most lines are typed : someone decides how many boards they are buying, and
 * that number answers to nobody. A few are not. When a line is written **as the
 * consequence of another** — a treatment that comes with the timber, a service
 * an article carries — the quantity is worked out from the line it serves, and
 * the two are bound : four boards of `0.019` cubic metres each make `0.076`
 * cubic metres of treatment, and nothing else.
 *
 * 🚨 **A bound quantity that stops following is a silent under-billing.** Raise
 * the boards from four to a hundred and leave the treatment at `0.076` : the
 * document charges twenty-five times less work than will be done, and no screen
 * says so — the line is there, it carries an amount, everything looks right.
 * The quantity has to follow.
 *
 * 🚨 **And a typed quantity that gets overwritten is a silent erasure.** The
 * moment someone types a figure, they are deciding — they measured, or they
 * agreed a lump sum. Recomputing over it throws that away just as quietly.
 *
 * Both rules are right and they exclude each other, so the line has to remember
 * which of the two it is under. That is the single question this enumeration
 * answers, carried by
 * {@see \xyz\oihana\schema\business\documents\BusinessDocumentLine::$quantityOrigin}.
 *
 * A line is born `CALCULATED` and turns `ENTERED` on the first figure typed into
 * it. **It does not come back on its own** : returning to the computed value is
 * a gesture in its own right, and one a producer is expected to offer — clearing
 * the quantity is the usual way to ask for it.
 *
 * ⚠️ **An absent value states nothing, and must not be read as `ENTERED`.**
 * Silence is only silence : a line written before the property existed says
 * nothing about where its number came from, and a reader must not conclude that
 * a human chose it.
 *
 * 🔑 **Two values today, and the wording leaves room for a third.** A line
 * mirrored from another system carries a quantity that was neither worked out
 * here nor typed here ; the day such a line has to say so, it can, where a
 * yes-or-no flag would have had to be renamed.
 *
 * | Constant   | Description                                                            | Value                                                        |
 * |------------|------------------------------------------------------------------------|--------------------------------------------------------------|
 * | CALCULATED | Worked out by the system, and bound to the line it was worked out from. | https://schema.oihana.xyz/QuantityOrigin#Calculated           |
 * | ENTERED    | Typed by someone, and answering to nobody.                             | https://schema.oihana.xyz/QuantityOrigin#Entered              |
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\enumerations
 * @since   1.5.0
 */
class QuantityOrigin extends Enumeration
{
    /**
     * The quantity was worked out by the system from another line, and follows it
     * as long as nobody types over it.
     */
    public const string CALCULATED = 'https://schema.oihana.xyz/QuantityOrigin#Calculated' ;

    /**
     * The quantity was typed by someone, and no recomputation may write over it.
     */
    public const string ENTERED = 'https://schema.oihana.xyz/QuantityOrigin#Entered' ;
}
