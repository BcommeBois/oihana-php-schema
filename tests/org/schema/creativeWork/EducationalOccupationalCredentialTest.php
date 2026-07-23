<?php

namespace tests\org\schema\creativeWork ;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionUnionType;

use oihana\reflect\Reflection;

use org\schema\constants\Schema;
use org\schema\constants\traits\Credential as CredentialConstants;
use org\schema\constants\traits\EducationalOccupationCredential as EducationalOccupationCredentialConstants;
use org\schema\creativeWork\Credential;
use org\schema\creativeWork\EducationalOccupationalCredential;
use org\schema\CreativeWork;
use org\schema\DefinedTerm;
use org\schema\Organization;
use org\schema\Person;
use org\schema\Thing;

/**
 * Regression suite for the extraction of {@see Credential} out of
 * {@see EducationalOccupationalCredential} : the four credential properties
 * (`credentialCategory`, `recognizedBy`, `validFor`, `validIn`) moved up to the parent
 * and must stay reachable from the educational subtype, both as properties and as
 * constants.
 *
 * @see https://schema.org/EducationalOccupationalCredential
 */
class EducationalOccupationalCredentialTest extends TestCase
{
    public function testDefaults(): void
    {
        $credential = new EducationalOccupationalCredential();

        $this->assertNull( $credential->competencyRequired ?? null );
        $this->assertNull( $credential->educationalLevel   ?? null );
        $this->assertNull( $credential->credentialCategory ?? null );
        $this->assertNull( $credential->recognizedBy       ?? null );
        $this->assertNull( $credential->validFor           ?? null );
        $this->assertNull( $credential->validIn            ?? null );
    }

    /**
     * schema.org makes EducationalOccupationalCredential a subtype of Credential —
     * it used to extend CreativeWork directly in this library.
     */
    public function testExtendsCredential(): void
    {
        $credential = new EducationalOccupationalCredential();

        $this->assertInstanceOf( Credential::class   , $credential );
        $this->assertInstanceOf( CreativeWork::class , $credential );
        $this->assertInstanceOf( Thing::class        , $credential );
    }

    public function testSchemaType(): void
    {
        $this->assertSame
        (
            'https://schema.org/EducationalOccupationalCredential' ,
            EducationalOccupationalCredential::getSchemaType()
        );
    }

    // --- Constants ---

    /**
     * The credential constants are no longer duplicated in the educational trait :
     * they are inherited from the Credential constants trait.
     */
    public function testConstantsTraitComposesTheCredentialTrait(): void
    {
        $this->assertContains
        (
            CredentialConstants::class ,
            ( new ReflectionClass( EducationalOccupationCredentialConstants::class ) )->getTraitNames()
        );
    }

    public function testEducationalConstants(): void
    {
        $this->assertSame( 'competencyRequired' , Schema::COMPETENCY_REQUIRED );
        $this->assertSame( 'educationalLevel'   , Schema::EDUCATIONAL_LEVEL   );
    }

    // --- Inherited properties ---

    public function testInheritedCredentialProperties(): void
    {
        $credential = new EducationalOccupationalCredential();

        $credential->credentialCategory = new DefinedTerm( [ 'name' => 'degree' ] );
        $credential->recognizedBy       = new Organization( [ 'name' => 'Université de Paris' ] );
        $credential->validFor           = 'P3Y' ;
        $credential->validIn            = [ 'name' => 'France' ] ;

        $this->assertInstanceOf( DefinedTerm::class  , $credential->credentialCategory );
        $this->assertInstanceOf( Organization::class , $credential->recognizedBy       );
        $this->assertSame( 'P3Y'                     , $credential->validFor           );
        $this->assertSame( [ 'name' => 'France' ]    , $credential->validIn            );
    }

    public function testOwnProperties(): void
    {
        $credential = new EducationalOccupationalCredential();

        $credential->competencyRequired = new DefinedTerm( [ 'name' => 'PHP' ] );
        $credential->educationalLevel   = 'advanced' ;

        $this->assertInstanceOf( DefinedTerm::class , $credential->competencyRequired );
        $this->assertSame( 'advanced'              , $credential->educationalLevel   );
    }

    /**
     * `educationalLevel` is also declared on CreativeWork : the redeclaration on the
     * subtype must stay type-compatible, otherwise the class does not even load.
     */
    public function testEducationalLevelIsTypeCompatibleWithCreativeWork(): void
    {
        $child  = ( new ReflectionClass( EducationalOccupationalCredential::class ) )->getProperty( 'educationalLevel' ) ;
        $parent = ( new ReflectionClass( CreativeWork::class ) )->getProperty( 'educationalLevel' ) ;

        $this->assertSame
        (
            self::typeNames( $parent->getType() ) ,
            self::typeNames( $child->getType() )
        );
    }

    // --- Hydration ---

    public function testConstructorCopiesRawArraysVerbatim(): void
    {
        $data =
        [
            'name'                       => 'Master of Science' ,
            Schema::COMPETENCY_REQUIRED  => [ 'name' => 'PHP' ] ,
            Schema::EDUCATIONAL_LEVEL    => 'advanced' ,
            Schema::CREDENTIAL_CATEGORY  => [ 'name' => 'degree' ] ,
            Schema::RECOGNIZED_BY        => [ 'name' => 'Université de Paris' ] ,
            Schema::VALID_FOR            => 'P3Y' ,
            Schema::VALID_IN             => [ 'name' => 'France' ] ,
        ];

        $credential = new EducationalOccupationalCredential( $data );

        $this->assertSame( $data[ Schema::COMPETENCY_REQUIRED ] , $credential->competencyRequired );
        $this->assertSame( $data[ Schema::EDUCATIONAL_LEVEL   ] , $credential->educationalLevel   );
        $this->assertSame( $data[ Schema::CREDENTIAL_CATEGORY ] , $credential->credentialCategory );
        $this->assertSame( $data[ Schema::RECOGNIZED_BY       ] , $credential->recognizedBy       );
        $this->assertSame( $data[ Schema::VALID_FOR           ] , $credential->validFor           );
        $this->assertSame( $data[ Schema::VALID_IN            ] , $credential->validIn            );
    }

    /**
     * @throws ReflectionException
     */
    public function testReflectionHydration(): void
    {
        $credential = ( new Reflection() )->hydrate
        (
            [
                'name'                      => 'Master of Science' ,
                Schema::EDUCATIONAL_LEVEL   => 'advanced' ,
                Schema::CREDENTIAL_CATEGORY => 'degree' ,
            ] ,
            EducationalOccupationalCredential::class
        );

        $this->assertInstanceOf( EducationalOccupationalCredential::class , $credential );
        $this->assertSame( 'advanced' , $credential->educationalLevel   );
        $this->assertSame( 'degree'   , $credential->credentialCategory );
    }

    // --- hasCredential on Person / Organization ---

    /**
     * `hasCredential` was widened from EducationalOccupationalCredential to Credential :
     * both the parent type and the educational subtype must be assignable.
     */
    public function testPersonAndOrganizationAcceptBothCredentialTypes(): void
    {
        $person       = new Person() ;
        $organization = new Organization() ;

        $person->hasCredential       = new EducationalOccupationalCredential( [ 'name' => 'MSc' ] ) ;
        $organization->hasCredential = new Credential( [ 'name' => 'ISO 9001' ] ) ;

        $this->assertInstanceOf( EducationalOccupationalCredential::class , $person->hasCredential       );
        $this->assertInstanceOf( Credential::class                       , $organization->hasCredential );
    }

    public function testHasCredentialConstant(): void
    {
        $this->assertSame( 'hasCredential' , Schema::HAS_CREDENTIAL );
    }

    // --- Helpers ---

    /**
     * Returns the sorted list of the names composing a (possibly union) property type.
     *
     * @return string[]
     */
    private static function typeNames( ReflectionNamedType|ReflectionUnionType|null $type ): array
    {
        $names = match( true )
        {
            $type instanceof ReflectionUnionType => array_map
            (
                static fn( ReflectionNamedType $named ) => $named->getName() ,
                $type->getTypes()
            ) ,
            $type instanceof ReflectionNamedType => [ $type->getName() ] ,
            default                              => [] ,
        };

        sort( $names );

        return $names ;
    }
}
