<?php

namespace tests\org\schema\enumerations ;

use PHPUnit\Framework\TestCase;

use org\schema\Enumeration;
use org\schema\enumerations\DeliveryMethod;

class DeliveryMethodTest extends TestCase
{
    public function testIsEnumeration(): void
    {
        $this->assertInstanceOf( Enumeration::class , new DeliveryMethod() );
    }

    public function testConstants(): void
    {
        $this->assertSame( 'http://purl.org/goodrelations/v1#DHL'                        , DeliveryMethod::DHL              );
        $this->assertSame( 'http://purl.org/goodrelations/v1#DeliveryModeDirectDownload' , DeliveryMethod::DIRECT_DOWNLOAD  );
        $this->assertSame( 'http://purl.org/goodrelations/v1#FederalExpress'             , DeliveryMethod::FEDERAL_EXPRESS  );
        $this->assertSame( 'http://purl.org/goodrelations/v1#DeliveryModeFreight'        , DeliveryMethod::FREIGHT          );
        $this->assertSame( 'http://purl.org/goodrelations/v1#DeliveryModeMail'           , DeliveryMethod::MAIL             );
        $this->assertSame( 'http://purl.org/goodrelations/v1#DeliveryModePickUp'         , DeliveryMethod::ON_SITE_PICKUP   );
        $this->assertSame( 'http://purl.org/goodrelations/v1#DeliveryModeOwnFleet'       , DeliveryMethod::OWN_FLEET        );
        $this->assertSame( 'http://purl.org/goodrelations/v1#UPS'                        , DeliveryMethod::UPS              );
    }

    public function testIncludes(): void
    {
        $this->assertTrue ( DeliveryMethod::includes( DeliveryMethod::ON_SITE_PICKUP ) );
        $this->assertFalse( DeliveryMethod::includes( 'unknown' ) );
    }
}
