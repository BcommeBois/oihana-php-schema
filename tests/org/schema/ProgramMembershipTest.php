<?php

namespace tests\org\schema ;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

use org\schema\constants\Schema;
use org\schema\constants\traits\MemberProgram as MemberProgramProperties;
use org\schema\constants\traits\ProgramMembership as ProgramMembershipProperties;
use org\schema\Intangible;
use org\schema\MemberProgram;
use org\schema\Organization;
use org\schema\ProgramMembership;

class ProgramMembershipTest extends TestCase
{
    public function testIsIntangible(): void
    {
        $this->assertInstanceOf( Intangible::class , new ProgramMembership() );
        $this->assertInstanceOf( Intangible::class , new MemberProgram()     );
    }

    public function testConstructorCopiesTheProperties(): void
    {
        $membership = new ProgramMembership
        ([
            Schema::MEMBERSHIP_NUMBER        => 'FF-99' ,
            Schema::MEMBERSHIP_POINTS_EARNED => 1_250 ,
            Schema::PROGRAM_NAME             => 'Etchea Frequent Flyer' ,
            Schema::HOSTING_ORGANIZATION     => new Organization([ Schema::NAME => 'Etchea Airlines' ] ) ,
            Schema::PROGRAM                  => new MemberProgram([ Schema::NAME => 'Frequent Flyer' ] ) ,
        ]);

        $this->assertSame( 'FF-99'                 , $membership->membershipNumber      );
        $this->assertSame( 1_250                   , $membership->membershipPointsEarned );
        $this->assertSame( 'Etchea Frequent Flyer' , $membership->programName           );
        $this->assertInstanceOf( Organization::class  , $membership->hostingOrganization );
        $this->assertInstanceOf( MemberProgram::class , $membership->program             );
    }

    /**
     * The structured properties take a raw array as well as an instance.
     *
     * The constructor assigns what it is given without typing it, so a payload
     * read from storage — nested arrays, nothing hydrated — has to be assignable
     * as it stands ; a union naming only the class would throw on the way in.
     */
    public function testStructuredPropertiesAcceptRawArrays(): void
    {
        $membership = new ProgramMembership
        ([
            Schema::HOSTING_ORGANIZATION     => [ Schema::NAME => 'Etchea Airlines' ] ,
            Schema::MEMBER                   => [ Schema::NAME => 'Ada Lovelace'    ] ,
            Schema::MEMBERSHIP_POINTS_EARNED => [ 'value' => 1_250 , 'unitText' => 'miles' ] ,
            Schema::PROGRAM                  => [ Schema::NAME => 'Frequent Flyer'  ] ,
        ]);

        $this->assertIsArray( $membership->hostingOrganization    );
        $this->assertIsArray( $membership->member                 );
        $this->assertIsArray( $membership->membershipPointsEarned );
        $this->assertIsArray( $membership->program                );

        $program = new MemberProgram([ Schema::HOSTING_ORGANIZATION => [ Schema::NAME => 'Etchea Airlines' ] ] );

        $this->assertIsArray( $program->hostingOrganization );
    }

    /**
     * @return array<int,array{0:string,1:string}>
     */
    public static function providePropertyTraits(): array
    {
        return
        [
            [ MemberProgramProperties::class     , MemberProgram::class     ] ,
            [ ProgramMembershipProperties::class , ProgramMembership::class ] ,
        ];
    }

    /**
     * Every constant of the trait names a property that really exists on the class.
     */
    #[DataProvider( 'providePropertyTraits' )]
    public function testEveryConstantNamesAnExistingProperty( string $trait , string $class ): void
    {
        $constants = ( new ReflectionClass( $trait ) )->getConstants() ;

        $this->assertNotEmpty( $constants );

        foreach ( $constants as $constant => $property )
        {
            $this->assertTrue
            (
                property_exists( $class , $property ) ,
                sprintf( '%s has no "%s" property, named by the %s constant.' , $class , $property , $constant )
            );
        }
    }
}
