<?php

namespace com\progress\schema\constants\traits ;

/**
 * Aggregator trait that exposes every Progress OpenEdge SQL system catalog
 * property key.
 *
 * This trait simply composes the individual traits dedicated to each system
 * catalog table (Table, Column, Index, View, User, …).
 * Including this trait on a class makes every property constant available
 * through that class.
 *
 * @package com\progress\schema\constants\traits
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.0.0
 */
trait Properties
{
    use Authorization ,
        Column        ,
        Common        ,
        Constraint    ,
        DataType      ,
        Index         ,
        Procedure     ,
        Sequence      ,
        Synonym       ,
        Table         ,
        Trigger       ,
        User          ,
        View          ;
}
