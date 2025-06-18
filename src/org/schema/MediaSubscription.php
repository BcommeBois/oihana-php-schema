<?php

namespace org\schema;

/**
 * A subscription which allows a user to access media including audio, video, books, etc.
 * https://schema.org/MediaSubscription
 */
class MediaSubscription extends Intangible
{
    /**
     * The Organization responsible for authenticating the user's subscription.
     * For example, many media apps require a cable/satellite provider to authenticate your subscription before playing media.
     * @var Organization|null
     */
    public ?Organization $authenticator ;

    /**
     * An Offer which must be accepted before the user can perform the Action.
     * For example, the user may need to buy a movie before being able to watch it.
     * @var Offer|null
     */
    public ?Offer $expectsAcceptanceOf ;
}