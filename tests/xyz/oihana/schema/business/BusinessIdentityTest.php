<?php

namespace tests\xyz\oihana\schema\business ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use org\schema\Intangible;
use org\schema\Organization;
use org\schema\Person;

use xyz\oihana\schema\business\BusinessIdentity;

class BusinessIdentityTest extends TestCase
{
    public function testDefaults(): void
    {
        $identity = new BusinessIdentity();

        $this->assertNull( $identity->subject ?? null );
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
        $this->assertSame( 'subject' , BusinessIdentity::SUBJECT );
    }

    /**
     * @throws ReflectionException
     */
    public function testHydrationWithScalarSubject(): void
    {
        $identity = new BusinessIdentity([ BusinessIdentity::SUBJECT => 'people/BECOU_Seller' ]);

        $this->assertSame( 'people/BECOU_Seller' , $identity->subject );
    }

    /**
     * @throws ReflectionException
     */
    public function testSubjectObjectAssignment(): void
    {
        $identity = new BusinessIdentity();

        $identity->subject = new Person([ 'id' => '94565' ]);

        $this->assertInstanceOf( Person::class , $identity->subject );
        $this->assertSame( '94565' , $identity->subject->id );
    }

    // ---- subjectType / isType

    public function testSubjectTypeFromObject(): void
    {
        $identity = new BusinessIdentity();
        $identity->subject = new Person([ 'additionalType' => 'Seller' ]);

        $this->assertSame( 'Seller' , $identity->subjectType() );
    }

    public function testSubjectTypeFromArray(): void
    {
        $identity = new BusinessIdentity();
        $identity->subject = new Person([ 'additionalType' => [ 'Seller' , 'Employee' ] ]);

        $this->assertSame( [ 'Seller' , 'Employee' ] , $identity->subjectType() );
    }

    public function testSubjectTypeWithoutAdditionalType(): void
    {
        $identity = new BusinessIdentity();
        $identity->subject = new Person([ 'id' => '94565' ]);

        $this->assertNull( $identity->subjectType() );
    }

    public function testSubjectTypeWithScalarSubject(): void
    {
        $identity = new BusinessIdentity([ BusinessIdentity::SUBJECT => 'people/BECOU' ]);

        $this->assertNull( $identity->subjectType() );
    }

    public function testIsTypeMatchesString(): void
    {
        $identity = new BusinessIdentity();
        $identity->subject = new Person([ 'additionalType' => 'Seller' ]);

        $this->assertTrue ( $identity->isType( 'Seller' ) );
        $this->assertFalse( $identity->isType( 'CustomerEmployee' ) );
    }

    public function testIsTypeMatchesArrayMembership(): void
    {
        $identity = new BusinessIdentity();
        $identity->subject = new Person([ 'additionalType' => [ 'Seller' , 'Employee' ] ]);

        $this->assertTrue ( $identity->isType( 'Employee' ) );
        $this->assertFalse( $identity->isType( 'CustomerEmployee' ) );
    }

    public function testIsTypeWithUndefinedType(): void
    {
        $identity = new BusinessIdentity();

        $this->assertFalse( $identity->isType( 'Seller' ) );
    }

    // ---- subjectKey

    public function testSubjectKeyWithScalar(): void
    {
        $identity = new BusinessIdentity([ BusinessIdentity::SUBJECT => 'people/BECOU' ]);

        $this->assertSame( 'people/BECOU' , $identity->subjectKey() );
    }

    public function testSubjectKeyDefaultsToKey(): void
    {
        $identity = new BusinessIdentity();
        $identity->subject = new Person([ '_key' => '94565' , 'id' => 'BECOU' ]);

        $this->assertSame( '94565' , $identity->subjectKey() );
    }

    public function testSubjectKeyWithExplicitKey(): void
    {
        $identity = new BusinessIdentity();
        $identity->subject = new Person([ '_key' => '94565' , 'id' => 'BECOU' ]);

        $this->assertSame( 'BECOU' , $identity->subjectKey( 'id' ) );
    }

    public function testSubjectKeyWithOrderedKeyList(): void
    {
        $identity = new BusinessIdentity();
        $identity->subject = new Person([ 'id' => 'BECOU' ]); // no _key

        $this->assertSame( 'BECOU' , $identity->subjectKey( [ '_key' , 'id' ] ) );
    }

    public function testSubjectKeyWithNull(): void
    {
        $identity = new BusinessIdentity();

        $this->assertNull( $identity->subjectKey() );
    }

    public function testSubjectKeyUnresolvableOnObject(): void
    {
        $identity = new BusinessIdentity();
        $identity->subject = new Person([ 'id' => 'BECOU' ]); // no _key

        $this->assertNull( $identity->subjectKey( '_key' ) ); // probed key absent on the object
    }

    // ---- worksForKey

    public function testWorksForKeyWithObject(): void
    {
        $identity = new BusinessIdentity();
        $identity->subject = new Person([ 'worksFor' => new Organization([ '_key' => '13658' ]) ]);

        $this->assertSame( '13658' , $identity->worksForKey() );
    }

    public function testWorksForKeyWithExplicitKey(): void
    {
        $identity = new BusinessIdentity();
        $identity->subject = new Person([ 'worksFor' => new Organization([ '_key' => '13658' , 'id' => '741278' ]) ]);

        $this->assertSame( '741278' , $identity->worksForKey( 'id' ) );
    }

    public function testWorksForKeyWithScalar(): void
    {
        $identity = new BusinessIdentity();
        $identity->subject = new Person([ 'worksFor' => 'organizations/13658' ]);

        $this->assertSame( 'organizations/13658' , $identity->worksForKey() );
    }

    public function testWorksForKeyWithoutWorksFor(): void
    {
        $identity = new BusinessIdentity();
        $identity->subject = new Person([ '_key' => '94565' ]);

        $this->assertNull( $identity->worksForKey() );
    }

    public function testWorksForKeyWithScalarSubject(): void
    {
        $identity = new BusinessIdentity([ BusinessIdentity::SUBJECT => 'people/BECOU' ]);

        $this->assertNull( $identity->worksForKey() );
    }

    // ---- array subject (raw projection reference, e.g. an AQL-projected document)

    public function testSubjectTypeFromArraySubject(): void
    {
        $identity = new BusinessIdentity();
        $identity->subject = [ 'additionalType' => 'Seller' ];

        $this->assertSame( 'Seller' , $identity->subjectType() );
    }

    public function testIsTypeFromArraySubject(): void
    {
        $identity = new BusinessIdentity();
        $identity->subject = [ 'additionalType' => 'Seller' ];

        $this->assertTrue ( $identity->isType( 'Seller' ) );
        $this->assertFalse( $identity->isType( 'CustomerEmployee' ) );
    }

    public function testSubjectKeyFromArraySubject(): void
    {
        $identity = new BusinessIdentity();
        $identity->subject = [ '_key' => '94565' , 'id' => 'BECOU' ];

        $this->assertSame( '94565' , $identity->subjectKey() );
    }

    public function testWorksForKeyFromArraySubject(): void
    {
        $identity = new BusinessIdentity();
        $identity->subject = [ 'worksFor' => [ '_key' => '13658' ] ];

        $this->assertSame( '13658' , $identity->worksForKey() );
    }
}
