<?php

namespace org\schema;

/**
 * An application programming interface accessible over Web/Internet technologies.
 * @see https://schema.org/WebAPI
 */
class WebAPI extends Service
{
    /**
     * Further documentation describing the Web API in more detail.
     * @var string|CreativeWork|null
     */
    public null|string|CreativeWork $documentation ;
}