<?php

namespace org\schema;

/**
 * An intangible item that describes an alignment between a learning resource and a node in an educational framework.
 *
 * Should not be used where the nature of the alignment can be described using a simple property,
 * for example to express that a resource teaches or assesses a competency.
 *
 *  ### Example
 *  ```json
 *  {
 *     "@context"             : "https://schema.org",
 *     "@type"                :  "LearningResource",
 *     "name"                 : "Introduction à la cybersécurité",
 *     "educationalAlignment" :
 *     {
 *         "@type"                : "AlignmentObject",
 *         "alignmentType"        : "teaches",
 *         "educationalFramework" : "EQF",
 *         "targetName"           : "Sécurité des systèmes d'information",
 *         "targetUrl"            :  "https://example.org/eqf/competence/ssi",
 *         "targetDescription"    : "Compétence liée à la protection des systèmes numériques."
 *     }
 * }
 *  ```
 *
 * @see http://schema.org/AlignmentObject
 */
class AlignmentObject extends Intangible
{
    /**
     * A category of alignment between the learning resource and the framework node. `
     * Recommended values include: 'requires', 'textComplexity', 'readingLevel', and 'educationalSubject'.
     * @var string|null
     */
    public null|string $alignmentType ;

    /**
     * The framework to which the resource being described is aligned.
     * @var string|null
     */
    public null|string $educationalFramework;

    /**
     * The description of a node in an established educational framework.
     * @var string|null
     */
    public null|string $targetDescription;

    /**
     * The name of a node in an established educational framework.
     * @var string|null
     */
    public null|string $targetName;

    /**
     * The URL of a node in an established educational framework.
     * @var string|null
     */
    public null|string $targetUrl ;
}


