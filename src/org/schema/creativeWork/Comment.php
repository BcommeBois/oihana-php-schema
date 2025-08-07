<?php

namespace org\schema\creativeWork ;

use org\schema\CreativeWork;

/**
 * A comment on an item - for example, a comment on a blog post.
 *
 * The comment's content is expressed via the text property, and its topic via about,
 * properties shared with all CreativeWorks.
 *
 * @see https://schema.org/Comment
 */
class Comment extends CreativeWork
{
    /**
     * The number of downvotes this question, answer or comment has received from the community.
     * @var int|null
     */
    public int|null $downvoteCount ;

    /**
     * The parent of a question, answer or item in general.
     *
     * Typically used for Q/A discussion threads e.g. a chain of comments with
     * the first comment being an Article or other CreativeWork.
     *
     * See also comment which points from something to a comment about it.
     *
     * @var Comment|CreativeWork|null
     */
    public null|Comment|CreativeWork $parentItem ;

    /**
     * A CreativeWork such as an image, video, or audio clip shared as part of this posting.
     * @var CreativeWork|array|null
     */
    public null|CreativeWork|array $sharedContent ;

    /**
     * The number of upvotes this question, answer or comment has received from the community.
     * @var int|null
     */
    public int|null $upvoteCount ;
}


