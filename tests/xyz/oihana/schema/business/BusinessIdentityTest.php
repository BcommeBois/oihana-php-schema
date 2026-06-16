<?php

namespace tests\xyz\oihana\schema\business ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use org\schema\Intangible;
use org\schema\Organization;
use org\schema\Person;

use xyz\oihana\schema\business\BusinessIdentity;
use xyz\oihana\schema\enumerations\BusinessIdentityRole;

class BusinessIdentityTest extends TestCase
{
    public function testDefaults(): void
    {
        $identity = new BusinessIdentity();

        $this->assertNull( $identity->memberOf ?? null );
        $this->assertNull( $identity->role     ?? null );
        $this->assertNull( $identity->subject  ?? null );
    }

    public function testIsIntangible(): void
    {
        $this->assertInstanceOf( Intangible::class , new BusinessIdentity() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( 'https://schema.oihana.xyz' , BusinessIdentity::CONTEXT );
    }

    public function testPropertyNameConstants(): void
    {
        $this->assertSame( 'memberOf' , BusinessIdentity::MEMBER_OF );
        $this->assertSame( 'role'     , BusinessIdentity::ROLE );
        $this->assertSame( 'subject'  , BusinessIdentity::SUBJECT );
    }

    /**
     * @throws ReflectionException
     */
    public function testHydrationWithScalarSubject(): void
    {
        $identity = new BusinessIdentity
        ([
            BusinessIdentity::ROLE    => BusinessIdentityRole::SELLER ,
            BusinessIdentity::SUBJECT => 'people/BECOU_Seller' ,
        ]);

        $this->assertSame( 'seller'              , $identity->role );
        $this->assertSame( 'people/BECOU_Seller' , $identity->subject );
        $this->assertNull( $identity->memberOf ?? null );
    }

    /**
     * @throws ReflectionException
     */
    public function testSubjectAndMemberOfObjectAssignment(): void
    {
        $identity = new BusinessIdentity();

        $identity->role     = BusinessIdentityRole::CUSTOMER_CONTACT ;
        $identity->subject  = new Person([ 'id' => '94565' ]);
        $identity->memberOf = new Organization([ 'id' => '13658' ]);

        $this->assertSame( 'customerContact' , $identity->role );
        $this->assertInstanceOf( Person::class       , $identity->subject );
        $this->assertInstanceOf( Organization::class , $identity->memberOf );
        $this->assertSame( '94565' , $identity->subject->id );
        $this->assertSame( '13658' , $identity->memberOf->id );
    }
}
