<?php

namespace org\schema\constants ;

use org\schema\constants\traits\Properties;

/**
 * Collection of schema.org property name constants.
 *
 * This class exposes, through a composition of traits, a comprehensive set of
 * constants that represent property names defined by schema.org and a few
 * custom extensions used by this package. Using constants instead of raw
 * strings prevents typos, eases refactoring, and improves IDE auto-completion.
 *
 * The constants are imported from multiple Traits (see the org\schema\constants\traits
 * namespace). When this class uses these traits, their constants become
 * available as if they were declared directly on the class, and can therefore be
 * referenced statically (e.g., Schema::NAME, Schema::AT_TYPE, etc.).
 *
 * Typical use cases include building JSON-LD or other structured data arrays
 * where keys must match schema.org property names exactly.
 *
 * Example:
 * ```php
 * use org\schema\constants\Schema ;
 * $person = new Person
 * ([
 *     Schema::AT_TYPE => 'Person',
 *     Schema::NAME    => 'Alice',
 *     Schema::URL     => 'https://example.com/alice',
 * ]);
 * ```
 *
 * Notes:
 * - This class is intentionally lightweight and contains no methods or state;
 *   it only aggregates constants.
 * - If you prefer a shorter alias, you can import it as "Schema" or any name
 *   of your choice via PHP's use-as syntax.
 *
 * @see https://schema.org/ Official schema.org vocabulary
 *
 * @package org\schema\constants
 */
class Schema
{
    use Properties ;
}