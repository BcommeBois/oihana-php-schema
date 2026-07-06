<?php

namespace tests\xyz\oihana\schema\places ;

use PHPUnit\Framework\TestCase;

use org\schema\PostalAddress;

use xyz\oihana\schema\places\JobSite;

class JobSiteTest extends TestCase
{
    public function testMagicSetHandlesPostalAddressProperties(): void
    {
        $jobSite = new JobSite() ;

        $jobSite->streetAddress = '12 rue des Bois' ;

        $this->assertInstanceOf( PostalAddress::class , $jobSite->address ) ;
        $this->assertSame( '12 rue des Bois' , $jobSite->address->streetAddress ) ;
    }
}
