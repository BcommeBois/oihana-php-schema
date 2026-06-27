<?php

namespace xyz\oihana\schema;

use org\schema\Intangible;
use xyz\oihana\schema\constants\Oihana;

/**
 * Represents a favorite — a user's bookmark onto a resource.
 *
 * A `Favorite` is the edge linking an authentication account (`_from`,
 * `users/{key}`) to a favorited resource (`_to`), stored in the
 * `user_has_favorite` edge collection. Being an edge, it carries `_from` /
 * `_to` (from {@see \org\schema\traits\ArangoDBTrait} on {@see \org\schema\Thing}).
 *
 * The `additionalType` property identifies the **functional favorite type**
 * (e.g. `customers`, `products`, `sellers`) — i.e. what kind of resource is
 * bookmarked. It drives, per type, the model used to rebuild the target, its
 * detail route and the read permission to enforce. It is **distinct** from the
 * `additionalType` of the targeted resource itself (its own Schema.org type):
 * several functional types may share a physical collection (customers and
 * subsidiaries both back `organizations`), so the favorite records the
 * functional type explicitly.
 *
 * Server-side only: this type lets a write be routed to the right model and
 * lets reads be grouped / scoped by type without loading the targets. A
 * favorites listing never exposes the `Favorite` itself — it rebuilds each
 * targeted resource with its own schema; `Favorite` is the internal link.
 *
 * ### Example
 *
 * ```json
 * {
 *     "@type": "Favorite",
 *     "@context": "https://schema.oihana.xyz",
 *     "additionalType": "products",
 *     "_from": "users/72488862",
 *     "_to": "products/105997",
 *     "created": "2026-06-27T14:30:00+02:00"
 * }
 * ```
 *
 * @package xyz\oihana\schema
 * @author  Marc Alcaraz
 *
 * @see https://schema.org/Intangible
 */
class Favorite extends Intangible
{
    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;
}