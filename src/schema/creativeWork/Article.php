<?php

namespace org\schema\creativeWork ;

use org\schema\CreativeWork;

/**
 * An article, such as a news article or piece of investigative report.
 * Newspapers and magazines have articles of many different types and this is intended to cover them all.
 * @see https://schema.org/Article
 */
class Article extends CreativeWork
{
    /**
     * The actual body of the article.
     */
    public string|object|null $articleBody ;

    /**
     * Articles may belong to one or more 'sections' in a magazine or newspaper, such as Sports, Lifestyle, etc.
     */
    public null|array|string|object $articleSection ;

    /**
     * For an Article, typically a NewsArticle, the backstory property provides a textual summary giving a brief explanation of why and how an article was created. In a journalistic setting this could include information about reporting process, methods, interviews, data sources, etc.
     * @var string|CreativeWork|null
     */
    public null|string|CreativeWork $backstory ;

    /**
     * The page on which the work ends; for example "138" or "xvi".
     * @var object|string|null
     */
    public null|object|string $pageEnd ;

    /**
     * The page on which the work starts; for example "135" or "xiii".
     * @var object|string|null
     */
    public null|object|string $pageStart ;

    /**
     * Any description of pages that is not separated into pageStart and pageEnd;
     * for example, "1-6, 9, 55" or "10-12, 46-49".
     * @var string|null
     */
    public ?string $pagination ;

    /**
     * Indicates sections of a Web page that are particularly 'speakable' in the sense of being highlighted as being especially appropriate for text-to-speech conversion.
     * Other sections of a page may also be usefully spoken in particular circumstances; the 'speakable' property serves to indicate the parts most likely to be generally useful for speech.
     * @var string|array|null|SpeakableSpecification
     */
    public null|array|string|SpeakableSpecification $speakable ;

    /**
     * The number of words in the text of the CreativeWork such as an Article, Book, etc.
     * @var int|null
     */
    public ?int $wordCount ;
}


