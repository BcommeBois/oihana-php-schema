<?php

namespace tests\org\schema\creativeWork ;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;

use oihana\reflect\Reflection;

use org\schema\constants\Prop;
use org\schema\constants\Schema;
use org\schema\constants\traits\Credential as CredentialConstants;
use org\schema\constants\traits\Properties;
use org\schema\creativeWork\Credential;
use org\schema\CreativeWork;
use org\schema\DefinedTerm;
use org\schema\Duration;
use org\schema\Organization;
use org\schema\places\AdministrativeArea;
use org\schema\Thing;

/**
 * {@see Credential} is the schema.org parent of
 * {@see \org\schema\creativeWork\EducationalOccupationalCredential} — it carries the four
 * credential properties (`credentialCategory`, `recognizedBy`, `validFor`, `validIn`) that
 * used to be declared on the educational subtype only.
 *
 * @see https://schema.org/Credential
 */
class CredentialTest extends TestCase
{
    public function testDefaults(): void
    {
        $credential = new Credential();

        $this->assertNull( $credential->credentialCategory ?? null );
        $this->assertNull( $credential->recognizedBy       ?? null );
        $this->assertNull( $credential->validFor           ?? null );
        $this->assertNull( $credential->validIn            ?? null );
    }

    public function testLineage(): void
    {
        $credential = new Credential();

        $this->assertInstanceOf( CreativeWork::class , $credential );
        $this->assertInstanceOf( Thing::class        , $credential );
    }

    public function testSchemaType(): void
    {
        $this->assertSame( 'https://schema.org/Credential' , Credential::getSchemaType() );
    }

    // --- Constants ---

    public function testPropertiesTraitComposesTheCredentialTrait(): void
    {
        $this->assertContains
        (
            CredentialConstants::class ,
            ( new ReflectionClass( Properties::class ) )->getTraitNames() ,
            'The Credential constants trait must be aggregated into Schema/Prop through Properties.'
        );
    }

    public function testCredentialConstantsAggregatedIntoSchemaAndProp(): void
    {
        $this->assertSame( 'credentialCategory' , Schema::CREDENTIAL_CATEGORY );
        $this->assertSame( 'recognizedBy'       , Schema::RECOGNIZED_BY       );
        $this->assertSame( 'validFor'           , Schema::VALID_FOR           );
        $this->assertSame( 'validIn'            , Schema::VALID_IN            );

        $this->assertSame( Schema::CREDENTIAL_CATEGORY , Prop::CREDENTIAL_CATEGORY );
        $this->assertSame( Schema::RECOGNIZED_BY       , Prop::RECOGNIZED_BY       );
        $this->assertSame( Schema::VALID_FOR           , Prop::VALID_FOR           );
        $this->assertSame( Schema::VALID_IN            , Prop::VALID_IN            );
    }

    // --- Property typing ---

    public function testAcceptsScalarReferences(): void
    {
        $credential = new Credential();

        $credential->credentialCategory = 'degree' ;
        $credential->recognizedBy       = 'organizations/42' ;
        $credential->validFor           = 'P3Y' ;
        $credential->validIn            = 'areas/FR' ;

        $this->assertSame( 'degree'           , $credential->credentialCategory );
        $this->assertSame( 'organizations/42' , $credential->recognizedBy       );
        $this->assertSame( 'P3Y'              , $credential->validFor           );
        $this->assertSame( 'areas/FR'         , $credential->validIn            );
    }

    public function testAcceptsStructuredObjects(): void
    {
        $credential = new Credential();

        $credential->credentialCategory = new DefinedTerm( [ 'name' => 'degree' ] );
        $credential->recognizedBy       = new Organization( [ 'name' => 'Université de Paris' ] );
        $credential->validFor           = new Duration( [ 'name' => 'P3Y' ] );
        $credential->validIn            = new AdministrativeArea( [ 'name' => 'France' ] );

        $this->assertInstanceOf( DefinedTerm::class        , $credential->credentialCategory );
        $this->assertInstanceOf( Organization::class       , $credential->recognizedBy       );
        $this->assertInstanceOf( Duration::class           , $credential->validFor           );
        $this->assertInstanceOf( AdministrativeArea::class , $credential->validIn            );
    }

    public function testValidForAcceptsNumericDurations(): void
    {
        $credential = new Credential();

        $credential->validFor = 36 ;
        $this->assertSame( 36 , $credential->validFor );

        $credential->validFor = 36.5 ;
        $this->assertSame( 36.5 , $credential->validFor );
    }

    // --- Hydration ---

    /**
     * The constructor performs a raw, shallow assignment : every structured property
     * must therefore accept a plain array or the assignment throws a TypeError.
     */
    public function testConstructorCopiesRawArraysVerbatim(): void
    {
        $data =
        [
            'name'                       => 'Master of Science' ,
            Schema::CREDENTIAL_CATEGORY  => [ 'name' => 'degree' ] ,
            Schema::RECOGNIZED_BY        => [ 'name' => 'Université de Paris' ] ,
            Schema::VALID_FOR            => [ 'name' => 'P3Y' ] ,
            Schema::VALID_IN             => [ 'name' => 'France' ] ,
        ];

        $credential = new Credential( $data );

        $this->assertSame( 'Master of Science'              , $credential->name               );
        $this->assertSame( $data[ Schema::CREDENTIAL_CATEGORY ] , $credential->credentialCategory );
        $this->assertSame( $data[ Schema::RECOGNIZED_BY ]       , $credential->recognizedBy       );
        $this->assertSame( $data[ Schema::VALID_FOR ]           , $credential->validFor           );
        $this->assertSame( $data[ Schema::VALID_IN ]            , $credential->validIn            );
    }

    /**
     * The reflection-based hydration path leaves the credential properties raw as well :
     * none of them declares a `#[HydrateWith]` attribute.
     *
     * @throws ReflectionException
     */
    public function testReflectionHydration(): void
    {
        $credential = ( new Reflection() )->hydrate
        (
            [
                'name'                      => 'Master of Science' ,
                Schema::CREDENTIAL_CATEGORY => 'degree' ,
                Schema::VALID_FOR           => 'P3Y' ,
            ] ,
            Credential::class
        );

        $this->assertInstanceOf( Credential::class , $credential );
        $this->assertSame( 'Master of Science' , $credential->name               );
        $this->assertSame( 'degree'            , $credential->credentialCategory );
        $this->assertSame( 'P3Y'               , $credential->validFor           );
    }

    // --- Serialization ---

    public function testJsonSerializeExposesTheCredentialType(): void
    {
        $credential = new Credential
        ([
            'name'                      => 'Master of Science' ,
            Schema::CREDENTIAL_CATEGORY => 'degree' ,
        ]);

        $array = $credential->jsonSerialize() ;

        $this->assertIsArray( $array );
        $this->assertSame( 'Credential'          , $array[ Schema::AT_TYPE ]    );
        $this->assertSame( 'https://schema.org'  , $array[ Schema::AT_CONTEXT ] );
        $this->assertSame( 'Master of Science'   , $array[ 'name' ]             );
        $this->assertSame( 'degree'              , $array[ Schema::CREDENTIAL_CATEGORY ] );
    }
}
