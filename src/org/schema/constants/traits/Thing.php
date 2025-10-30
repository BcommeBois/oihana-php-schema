<?php

namespace org\schema\constants\traits;

trait Thing
{
    const string ABOUT                      = 'about' ;
    const string ADDITIONAL_PROPERTY        = 'additionalProperty' ;
    const string ADDITIONAL_TYPE            = 'additionalType' ;
    const string ALTERNATE_NAME             = 'alternateName' ;
    const string DISAMBIGUATING_DESCRIPTION = 'disambiguatingDescription' ;
    const string ID                         = 'id' ;
    const string IMAGE                      = 'image' ;
    const string IS_BASED_ON                = 'isBasedOn' ;
    const string IS_PART_OF                 = 'isPartOf' ;
    const string IS_RELATED_OF              = 'isRelatedTo' ;
    const string IS_SIMILAR_TO              = 'isSimilarTo' ;
    const string HAS_PART                   = 'hasPart' ;
    const string NAME                       = 'name' ;
    const string NUM_PART                   = 'numPart' ;
    const string POTENTIAL_ACTION           = 'potentialAction' ;
    const string SAME_AS                    = 'sameAs' ;
    const string SUBJECT_OF                 = 'subjectOf' ;
    const string URL                        = 'url' ;

    // --------- Metadatas

    use ArangoDB ,
        DublinCore ,
        JsonLD ;

    // --------- Specials

    const string KEY         = 'key'   ;
    const string IDS         = 'ids'   ;
    const string WITH_STATUS = 'withStatus' ;
}