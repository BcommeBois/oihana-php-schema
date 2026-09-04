<?php

namespace xyz\oihana\schema\products;

use oihana\reflect\attributes\HydrateAs;

use org\schema\Intangible;
use org\schema\ListItem;
use org\schema\Thing;

use xyz\oihana\schema\constants\Oihana;

/**
 * A resource that may be applied to something else — a service an item can
 * receive, an option a subscription can take — together with **how** it applies.
 *
 * The shape schema.org gives a list entry (`item` and `position`), plus the one
 * thing it does not name : whether the entry applies **by default**.
 *
 * 🚨 **`appliedByDefault` belongs to the LINK, never to the resource.** The same
 * service can apply by default to one item and be merely offered on another :
 * a treatment included in the price of one board is an option on the next. Put
 * the flag on the resource itself and it would read « this applies everywhere »,
 * which is false — and false in a way nothing shows, since the resource looks
 * perfectly ordinary. That is the whole reason this class exists rather than a
 * bare array of resources.
 *
 * 🔑 **`item` carries a reference, not a copy of the record.** The price, the
 * unit and the availability of a resource live on its own record, and a price
 * depends on who is buying : copying them here would freeze, at the moment the
 * link is written, figures that belong to the moment it is read.
 *
 * ⚠️ **An absent `appliedByDefault` is not a `false`.** Absent says « the source
 * does not tell » ; `false` says « the source says no ». A consumer that needs
 * to tell one from the other can, and one that does not can read both as « not
 * by default ».
 *
 * 🚨 **Why it does not extend {@see ListItem}, which names `item` and `position`
 * already.** That class types `item` as `?Thing`, and PHP forbids widening the
 * type of an inherited property : a subclass may not turn it into
 * `null|array|Thing`. Every class of this library is built from an array —
 * `new Product( [ … ] )` — so an `item` that cannot hold an array would throw
 * on the constructor path and work on the reflection one, which is the worst of
 * both. The property names are kept identical, so what the JSON-LD says is
 * unchanged ; only the inheritance differs.
 *
 * ```php
 * $link = new ApplicableResource
 * ([
 *     'item'             => [ 'id' => 'srv-polish' ] ,
 *     'position'         => 1 ,
 *     'appliedByDefault' => true ,
 * ]);
 * ```
 *
 * @author  Marc Alcaraz (eKameleon)
 * @package xyz\oihana\schema\products
 * @since   1.4.0
 */
class ApplicableResource extends Intangible
{
    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * Whether the resource applies to its host without being asked for.
     *
     * `true` — it is applied unless someone removes it ; `false` — it is
     * offered and must be chosen ; absent — the source does not say.
     *
     * @var bool|null
     */
    public ?bool $appliedByDefault ;

    /**
     * The resource this link points at.
     *
     * 🔑 **Typed down to a {@see Product} by default**, because that is what an
     * applicable resource is today : a record of the catalogue like any other.
     * The declared type stays a {@see Thing} so that a `Service` fits the day
     * one exists, without touching this class.
     *
     * @var null|array|Thing
     */
    #[HydrateAs(Product::class)]
    public null|array|Thing $item ;

    /**
     * The rank of the link in the list it belongs to, counting from 1.
     *
     * 🔑 **The rank is data, not an array index.** A source that numbers its
     * slots keeps them numbered even when one is empty, and a consumer that
     * re-orders the list must still be able to say where an entry sat.
     *
     * @var null|int|string
     */
    public null|int|string $position ;
}
