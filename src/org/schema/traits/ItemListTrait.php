<?php

namespace org\schema\traits;

use org\schema\DefinedTerm;
use org\schema\Enumeration;
use org\schema\ListItem;
use org\schema\Thing;

/**
 * A list of items of any sort—for example, Top 10 Movies About Weathermen, or Top 100 Party Songs. Not to be confused with HTML lists, which are often used only for formatting.
 */
trait ItemListTrait
{
    /**
     * Indicates a prototype of the elements in the list that is used to hold aggregate information (ratings, offers, etc.).
     */
    public ?Thing $aggregateElement ;

    /**
     * For itemListElement values, you can use simple strings (e.g. "Peter", "Paul", "Mary"), existing entities, or use ListItem.
     * Text values are best if the elements in the list are plain strings.
     * Existing entities are best for a simple, unordered list of existing things in your data.
     * ListItem is used with ordered lists when you want to provide additional context about the element in that list or when the same item might be in different places in different lists.
     * Note: The order of elements in your mark-up is not sufficient for indicating the order or elements. Use ListItem with a 'position' property in such cases.
     * @var null|array|string|ListItem|Thing
     */
    public null|array|string|ListItem|Thing $itemListElement ;

    /**
     * Type of ordering (e.g. Ascending, Descending, Unordered).
     * @var string|Enumeration|DefinedTerm|null
     */
    public null|string|Enumeration|DefinedTerm $itemListOrder ;

    /**
     * The number of items in an ItemList.
     * Note that some descriptions might not fully describe all items in a list (e.g., multi-page pagination); in such cases, the numberOfItems would be for the entire list.
     * @var int|float|null
     */
    public null|int|float $numberOfItems ;
}