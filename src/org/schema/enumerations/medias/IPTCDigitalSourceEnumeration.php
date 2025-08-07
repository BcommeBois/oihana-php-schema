<?php

namespace org\schema\enumerations\medias;

use org\schema\enumerations\MediaEnumeration;

/**
 * IPTC "Digital Source" codes for use with the digitalSourceType property,
 * providing information about the source for a digital media object.
 *
 * In general these codes are not declared here to be mutually exclusive, although some combinations
 * would be contradictory if applied simultaneously, or might be considered mutually incompatible
 * by upstream maintainers of the definitions. See the IPTC documentation for detailed definitions of all terms.
 *
 * @see https://schema.org/IPTCDigitalSourceEnumeration
 * @see https://iptc.org/
 */
class IPTCDigitalSourceEnumeration extends MediaEnumeration
{

}