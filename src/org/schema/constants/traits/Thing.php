<?php

namespace org\schema\constants\traits;

trait Thing
{
    const string ABOUT                      = 'about' ;
    const string ADDITIONAL_PROPERTY        = 'additionalProperty' ;
    const string ADDITIONAL_TYPE            = 'additionalType' ;
    const string ALTERNATE_NAME             = 'alternateName' ;
    const string DESCRIPTION                = 'description' ;
    const string DISAMBIGUATING_DESCRIPTION = 'disambiguatingDescription' ;
    const string ID                         = 'id' ;
    const string IDENTIFIER                 = 'identifier' ;
    const string IMAGE                      = 'image' ;
    const string LICENSE                    = 'license' ;
    const string NAME                       = 'name' ;
    const string POTENTIAL_ACTION           = 'potentialAction' ;
    const string SAME_AS                    = 'sameAs' ;
    const string SUBJECT_OF                 = 'subjectOf' ;
    const string URL                        = 'url' ;

    // --------- Metadatas

    // JSON-LD
    const string AT_CONTEXT = '@context' ;
    const string AT_ID      = '@id'      ;
    const string AT_TYPE    = '@type'    ;

    // Dublin Core
    const string CREATED  = 'created'  ;
    const string MODIFIED = 'modified' ;
    const string REVISION = 'revision' ;

    // ArangoDB
    const string _KEY       = '_key'     ;
    const string _REV       = '_rev'     ;
    const string _TYPE      = '_type'    ;
    const string _ID        = '_id'      ;

    // --------- Specials

    const string IS_BASED_ON   = 'isBasedOn' ;
    const string IS_PART_OF    = 'isPartOf' ;
    const string IS_RELATED_OF = 'isRelatedTo' ;
    const string IS_SIMILAR_TO = 'isSimilarTo' ;

    const string KEY   = 'key'   ;
    const string IDS   = 'ids'   ;

    const string HAS_PART = 'hasPart' ;
    const string NUM_PART = 'numPart' ;

    const string WITH_STATUS = 'withStatus' ;
}