<?php

namespace tests\org\schema\helpers\hydrate ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use org\schema\DefinedTerm;

use xyz\oihana\schema\thesaurus\DeliveryMethodTerm;

use function org\schema\helpers\hydrate\hydrateDefinedTerm;

final class HydrateDefinedTermTest extends TestCase
{
    /**
     * @throws ReflectionException
     */
    public function testHydratesASingleDefinition(): void
    {
        $term = hydrateDefinedTerm( [ 'name' => 'Express' , 'termCode' => 'EXP' ] ) ;

        $this->assertInstanceOf( DefinedTerm::class , $term ) ;
        $this->assertSame( 'Express' , $term->name ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testHydratesASingleDefinitionIntoTheGivenClass(): void
    {
        $term = hydrateDefinedTerm
        (
            [ 'name' => 'Free above 1000' , DeliveryMethodTerm::SHIPPING_RATE => 39 ] ,
            DeliveryMethodTerm::class
        );

        $this->assertInstanceOf( DeliveryMethodTerm::class , $term ) ;
        $this->assertSame( 'Free above 1000' , $term->name ) ;
        $this->assertSame( 39 , $term->shippingRate ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testHydratesAnIndexedArrayIntoTheGivenClass(): void
    {
        $terms = hydrateDefinedTerm
        (
            [
                [ 'name' => 'Express'  ] ,
                [ 'name' => 'Standard' ] ,
            ] ,
            DeliveryMethodTerm::class
        );

        $this->assertIsArray( $terms ) ;
        $this->assertCount( 2 , $terms ) ;
        $this->assertContainsOnlyInstancesOf( DeliveryMethodTerm::class , $terms ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testHydratesAnIndexedArrayOfDefinitions(): void
    {
        $terms = hydrateDefinedTerm(
        [
            [ 'name' => 'Express'  ] ,
            [ 'name' => 'Standard' ] ,
        ]) ;

        $this->assertIsArray( $terms ) ;
        $this->assertCount( 2 , $terms ) ;
        $this->assertContainsOnlyInstancesOf( DefinedTerm::class , $terms ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testKeepsBareReferencesAndNullifiesAnEmptyResult(): void
    {
        // A list of handles is the answer, not the absence of one : the property stores the
        // codes as published, and nothing was ever there to resolve.
        $this->assertSame( [ 'MEAL' , 'DEMO' ] , hydrateDefinedTerm( [ 'MEAL' , 'DEMO' ] ) ) ;

        // Only an entry that resolved to nothing is dropped.
        $this->assertNull( hydrateDefinedTerm( [ null , null ] ) ) ;
        $this->assertSame( [ 'MEAL' ] , hydrateDefinedTerm( [ null , 'MEAL' ] ) ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testReturnsANonArrayInputUnchanged(): void
    {
        $this->assertNull( hydrateDefinedTerm() ) ;
        $this->assertSame( 'Express' , hydrateDefinedTerm( 'Express' ) ) ;
    }

    /**
     * 🔑 **A bare reference survives inside a list**, exactly as it does on its own — the
     * contract every helper of the family states in its header, applied entry by entry.
     * A property that stores handles rather than resolved objects used to read back `null`.
     *
     * The keys matter as much as the contents : a filtered list left with gaps serializes
     * as a JSON **object**, and a consumer walking the value gets something it cannot walk.
     *
     * @throws ReflectionException
     */
    public function testAListOfReferencesSurvivesAndKeepsItsKeys(): void
    {
        $bare = hydrateDefinedTerm( [ 'MEAL' , 'MEAL' ] ) ;

        $this->assertSame( [ 'MEAL' , 'MEAL' ] , $bare ) ;

        $mixed = hydrateDefinedTerm( [ 'MEAL' , [ 'name' => 'Demonstration' ] ] ) ;

        $this->assertSame( [ 0 , 1 ] , array_keys( $mixed ) ) ;
        $this->assertSame( 'MEAL' , $mixed[ 0 ] ) ;
        $this->assertInstanceOf( DefinedTerm::class , $mixed[ 1 ] ) ;

        // The gap-free keys are what keeps it a JSON array rather than a JSON object.
        $this->assertSame( '["MEAL",{"@type":"DefinedTerm","@context":"https:\\/\\/schema.org","name":"Demonstration"}]' , json_encode( $mixed ) ) ;
    }
}
