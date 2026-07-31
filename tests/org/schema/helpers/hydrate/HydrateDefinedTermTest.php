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
    public function testFiltersNonTermEntriesAndNullifiesAnEmptyResult(): void
    {
        $this->assertNull( hydrateDefinedTerm( [ 'foo' , 'bar' ] ) ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testReturnsANonArrayInputUnchanged(): void
    {
        $this->assertNull( hydrateDefinedTerm() ) ;
        $this->assertSame( 'Express' , hydrateDefinedTerm( 'Express' ) ) ;
    }
}
