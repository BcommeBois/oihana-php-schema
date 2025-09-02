<?php

namespace tests\org\schema\constants;

use org\schema\constants\Prop;
use org\schema\constants\Schema;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class SchemaTest extends TestCase
{
    public function testSchemaUsesPropertiesTrait()
    {
        $rc = new ReflectionClass(Schema::class);
        $traits = $rc->getTraitNames();
        $this->assertNotEmpty($traits, 'Schema should use traits');
        $this->assertContains('org\\schema\\constants\\traits\\Properties', $traits);
    }

    public function testCoreThingConstantsAreAvailable()
    {
        $this->assertSame('@type', Schema::AT_TYPE);
        $this->assertSame('@context', Schema::AT_CONTEXT);
        $this->assertSame('name', Schema::NAME);
        $this->assertSame('description', Schema::DESCRIPTION);
        $this->assertSame('url', Schema::URL);
    }

    public function testProductConstantsAreAvailable()
    {
        // Quelques constantes du trait Product
        $this->assertSame('aggregateRating', Schema::AGGREGATE_RATING);
        $this->assertSame('isAccessoryOrSparePartFor', Schema::IS_ACCESSORY_OR_SPARE_PART_FOR);
        $this->assertSame('mobileUrl', Schema::MOBILE_URL);
        $this->assertSame('productID', Schema::PRODUCT_ID);
    }

    public function testAudienceAndRatingAndAggregateRatingConstants()
    {
        // Audience
        $this->assertSame('audienceType', Schema::AUDIENCE_TYPE);
        $this->assertSame('geographicArea', Schema::GEOGRAPHIC_AREA);

        // Rating
        $this->assertSame('author', Schema::AUTHOR);
        $this->assertSame('bestRating', Schema::BEST_RATING);
        $this->assertSame('ratingAspect', Schema::RATING_ASPECT);
        $this->assertSame('ratingExplanation', Schema::RATING_EXPLANATION);
        $this->assertSame('ratingValue', Schema::RATING_VALUE);
        $this->assertSame('worstRating', Schema::WORST_RATING);

        // AggregateRating
        $this->assertSame('itemReviewed', Schema::ITEM_REVIEWED);
        $this->assertSame('ratingCount', Schema::RATING_COUNT);
        $this->assertSame('reviewCount', Schema::REVIEW_COUNT);
    }

    public function testDefinedTermAndCategoryCode()
    {
        $this->assertSame('termCode', Schema::TERM_CODE);
        $this->assertSame('inDefinedTermSet', Schema::IN_DEFINED_TERM_SET);
        $this->assertSame('codeValue', Schema::CODE_VALUE);
        $this->assertSame('inCodeSet', Schema::IN_CODE_SET);
    }

    public function testOfferShippingDetailsAndProductModelGroup()
    {
        // OfferShippingDetails
        $this->assertSame('deliveryTime', Schema::DELIVERY_TIME);
        $this->assertSame('shippingDestination', Schema::SHIPPING_DESTINATION);
        $this->assertSame('width', Schema::WIDTH);

        // ProductModel
        $this->assertSame('predecessorOf', Schema::PREDECESSOR_OF);
        $this->assertSame('successorOf', Schema::SUCCESSOR_OF);

        // ProductGroup
        $this->assertSame('hasVariant', Schema::HAS_VARIANT);
        $this->assertSame('productGroupID', Schema::PRODUCT_GROUP_ID);
        $this->assertSame('variesBy', Schema::VARIES_BY);
    }

    public function testAlignmentObject()
    {
        $this->assertSame('alignmentType', Schema::ALIGNMENT_TYPE);
        $this->assertSame('educationalFramework', Schema::EDUCATIONAL_FRAMEWORK);
        $this->assertSame('targetDescription', Schema::TARGET_DESCRIPTION);
        $this->assertSame('targetName', Schema::TARGET_NAME);
        $this->assertSame('targetUrl', Schema::TARGET_URL);
    }

    public function testSchemaAndPropExposeSameConstants()
    {
        // Vérifie qu'un échantillon de constantes sont identiques entre Schema et Prop
        $this->assertSame(Schema::NAME, Prop::NAME);
        $this->assertSame(Schema::AT_TYPE, Prop::AT_TYPE);
        $this->assertSame(Schema::IS_ACCESSORY_OR_SPARE_PART_FOR, Prop::IS_ACCESSORY_OR_SPARE_PART_FOR);
        $this->assertSame(Schema::AUDIENCE_TYPE, Prop::AUDIENCE_TYPE);
        $this->assertSame(Schema::RATING_VALUE, Prop::RATING_VALUE);
    }

    public function testAllSchemaConstantsExistAsClassConstants()
    {
        // Sanity check: toutes les constantes de Schema doivent être des constantes de classe accessibles via Reflection
        $schemaRC = new ReflectionClass(Schema::class);
        $schemaConstants = $schemaRC->getConstants();
        $this->assertIsArray($schemaConstants);
        $this->assertArrayHasKey('NAME', $schemaConstants);
        $this->assertArrayHasKey('AT_TYPE', $schemaConstants);
        $this->assertArrayHasKey('AT_CONTEXT', $schemaConstants);
    }
}
