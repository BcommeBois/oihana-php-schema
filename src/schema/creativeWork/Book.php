<?php

namespace org\schema\creativeWork ;

use org\schema\CreativeWork;
use org\schema\creativeWork\enumerations\BookFormatType;
use org\schema\DefinedTerm;
use org\schema\Person;

/**
 * A book.
 * @see https://schema.org/Book
 */
class Book extends CreativeWork
{
    /**
     * Indicates whether the book is an abridged edition.
     */
    public ?bool $abridged ;

    /**
     * The book edition.
     */
    public null|string|object $bookEdition ;

    /**
     * The format of the book.
     * @var null|string|DefinedTerm|BookFormatType
     */
    public null|string|DefinedTerm|BookFormatType $bookFormat ;

    /**
     * The illustrator of the book.
     */
    public ?Person $illustrator ;

    /**
     * The ISBN of the book.
     */
    public ?string $isbn ;

    /**
     * The number of pages in the book.
     */
    public ?int $numberOfPages ;

    /**
     * The number of words in the text of the CreativeWork such as an Article, Book, etc.
     * @var int|null
     */
    public ?int $wordCount ;
}


