<?php

namespace org\schema\creativeWork\comments ;

use org\schema\creativeWork\Comment;
use org\schema\DefinedTerm;
use org\schema\ItemList;

/**
 * A specific question - e.g. from a user seeking answers online, or collected in a Frequently Asked Questions (FAQ) document.
 * @see https://schema.org/Question
 */
class Question extends Comment
{
    /**
     * The answer(s) that has been accepted as best, typically on a Question/Answer site.
     *
     * Sites vary in their selection mechanisms, e.g. drawing on community opinion and/or the view of the Question author.
     *
     * @var null|Answer|array|ItemList
     */
    public null|Answer|array|ItemList $acceptedAnswer ;

    /**
     * The number of answers this question has received.
     * @var int|null
     */
    public int|null $answerCount ;

    /**
     * For questions that are part of learning resources (e.g. Quiz), eduQuestionType indicates the format
     * of question being given. Example: "Multiple choice", "Open ended", "Flashcard".
     * @var string|null|DefinedTerm
     */
    public null|string|DefinedTerm $eduQuestionType ;

    /**
     * An answer (possibly one of several, possibly incorrect) to a Question, e.g. on a Question/Answer site.
     *
     * @var null|Answer|array|ItemList
     */
    public null|Answer|array|ItemList $suggestedAnswer ;
}


