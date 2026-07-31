<?php

namespace tests\xyz\oihana\schema\traits\places ;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\traits\places\SiteFlagsTrait;

/**
 * Host exposing {@see SiteFlagsTrait}, mirroring how
 * {@see \xyz\oihana\schema\places\CustomerSite} and
 * {@see \xyz\oihana\schema\places\ProviderSite} use it.
 */
class SiteFlagsHost
{
    use SiteFlagsTrait ;

    public null|array $additionalProperty = null ;
}

class SiteFlagsTraitTest extends TestCase
{
    #[DataProvider( 'flagsProvider' )]
    public function testFlagReflectsTheMatchingAdditionalProperty( string $propertyID , string $method )
    {
        $host = new SiteFlagsHost() ;

        $this->assertFalse( $host->$method() ) ;

        $host->additionalProperty = [ [ 'propertyID' => $propertyID , 'value' => true ] ] ;

        $this->assertTrue( $host->$method() ) ;
    }

    public static function flagsProvider(): array
    {
        return
        [
            'billing address'    => [ Oihana::IS_BILLING_ADDRESS    , 'isBillingAddress'    ] ,
            'construction site'  => [ Oihana::IS_CONSTRUCTION_SITE  , 'isConstructionSite'  ] ,
            'default address'    => [ Oihana::IS_DEFAULT_ADDRESS    , 'isDefaultAddress'    ] ,
            'delivery address'   => [ Oihana::IS_DELIVERY_ADDRESS   , 'isDeliveryAddress'   ] ,
            'shipping address'   => [ Oihana::IS_SHIPPING_ADDRESS   , 'isShippingAddress'   ] ,
        ] ;
    }

    public function testASiteMayClaimNoneOfTheFlags()
    {
        $host = new SiteFlagsHost() ;

        $host->additionalProperty = [] ;

        $this->assertFalse( $host->isBillingAddress()   ) ;
        $this->assertFalse( $host->isConstructionSite() ) ;
        $this->assertFalse( $host->isDefaultAddress()   ) ;
        $this->assertFalse( $host->isDeliveryAddress()  ) ;
        $this->assertFalse( $host->isShippingAddress()  ) ;
    }
}
