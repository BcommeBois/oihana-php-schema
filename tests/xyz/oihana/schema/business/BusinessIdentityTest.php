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

    public function testIsMatchesRole(): void
    {
        $identity = new BusinessIdentity([ BusinessIdentity::ROLE => BusinessIdentityRole::SELLER ]);

        $this->assertTrue ( $identity->is( BusinessIdentityRole::SELLER ) );
        $this->assertFalse( $identity->is( BusinessIdentityRole::CUSTOMER ) );
    }

    public function testIsWithUndefinedRole(): void
    {
        $identity = new BusinessIdentity();

        $this->assertFalse( $identity->is( BusinessIdentityRole::SELLER ) );
    }

    public function testSubjectIdWithScalar(): void
    {
        $identity = new BusinessIdentity([ BusinessIdentity::SUBJECT => 'people/BECOU' ]);

        $this->assertSame( 'people/BECOU' , $identity->subjectId() );
    }

    public function testSubjectIdWithObject(): void
    {
        $identity = new BusinessIdentity();
        $identity->subject = new Person([ 'id' => '94565' ]);

        $this->assertSame( '94565' , $identity->subjectId() );
    }

    public function testSubjectIdWithObjectExposingKeyOnly(): void
    {
        $identity = new BusinessIdentity();
        $identity->subject = new Person([ '_key' => 'BECOU' ]);

        $this->assertSame( 'BECOU' , $identity->subjectId() );
    }

    public function testSubjectIdWithNull(): void
    {
        $identity = new BusinessIdentity();

        $this->assertNull( $identity->subjectId() );
    }

    public function testMemberOfIdWithScalar(): void
    {
        $identity = new BusinessIdentity([ BusinessIdentity::MEMBER_OF => 'organizations/13658' ]);

        $this->assertSame( 'organizations/13658' , $identity->memberOfId() );
    }

    public function testMemberOfIdWithObject(): void
    {
        $identity = new BusinessIdentity();
        $identity->memberOf = new Organization([ 'id' => '13658' ]);

        $this->assertSame( '13658' , $identity->memberOfId() );
    }

    public function testMemberOfIdWithObjectExposingKeyOnly(): void
    {
        $identity = new BusinessIdentity();
        $identity->memberOf = new Organization([ '_key' => '13658' ]);

        $this->assertSame( '13658' , $identity->memberOfId() );
    }

    public function testMemberOfIdWithObjectExposingIdOnly(): void
    {
        $identity = new BusinessIdentity();
        $identity->memberOf = new Organization([ '_id' => 'organizations/13658' ]);

        $this->assertSame( 'organizations/13658' , $identity->memberOfId() );
    }

    public function testMemberOfIdWithNull(): void
    {
        $identity = new BusinessIdentity();

        $this->assertNull( $identity->memberOfId() );
    }
}
