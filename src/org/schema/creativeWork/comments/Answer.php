<?php

namespace org\schema\creativeWork\comments ;

use org\schema\creativeWork\Comment;
use org\schema\creativeWork\WebContent;

/**
 * An answer offered to a question; perhaps correct, perhaps opinionated or wrong.
 *
 * @see https://schema.org/Answer
 */
class Answer extends Comment
{
    /**
     * A step-by-step or full explanation about Answer. Can outline how this Answer was achieved or contain more broad clarification or statement about it.
     * @var null|Comment|WebContent|array
     */
    public null|Comment|WebContent|array $answerExplanation ;
}


