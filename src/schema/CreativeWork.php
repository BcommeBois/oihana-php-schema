<?php

namespace org\schema;

use org\schema\traits\CreativeWorkTrait;

/**
 * The most generic kind of creative work, including books, movies, photographs, software programs, etc.
 * @see https://schema.org/CreativeWork
 */
class CreativeWork extends Thing
{
    use CreativeWorkTrait ;
}


