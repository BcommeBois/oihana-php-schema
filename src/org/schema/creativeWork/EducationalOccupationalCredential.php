<?php

namespace org\schema\creativeWork;

use org\schema\DefinedTerm;

/**
 * An educational or occupational credential. A diploma, academic degree, certification, qualification, badge, etc.,
 * that may be awarded to a person or other entity that meets the requirements defined by the credentialer.
 *
 * @see https://schema.org/EducationalOccupationalCredential
 */
class EducationalOccupationalCredential extends Credential
{
    /**
     * Knowledge, skill, ability or personal attribute that must be demonstrated by a person or other entity in order
     * to do something such as earn an Educational Occupational Credential or understand a LearningResource.
     * @var null|string|array|DefinedTerm
     */
    public null|string|array|DefinedTerm $competencyRequired ;

    /**
     * The level in terms of progression through an educational or training context.
     * Examples of educational levels include 'beginner', 'intermediate' or 'advanced', and formal sets of level indicators.
     *
     * Note: inherited from {@see \org\schema\traits\CreativeWorkTrait} — declared here only for documentation.
     * @var null|string|array|DefinedTerm
     */
    public null|string|array|DefinedTerm $educationalLevel ;
}
