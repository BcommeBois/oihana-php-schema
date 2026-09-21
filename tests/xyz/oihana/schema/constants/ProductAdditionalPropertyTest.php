<?php

namespace tests\xyz\oihana\schema\constants ;

use PHPUnit\Framework\TestCase;

use xyz\oihana\schema\constants\ProductAdditionalProperty;

class ProductAdditionalPropertyTest extends TestCase
{
    public function testChoiceIsSpelledChoice(): void
    {
        $this->assertSame( 'choice' , ProductAdditionalProperty::CHOICE );
        $this->assertTrue( ProductAdditionalProperty::includes( ProductAdditionalProperty::CHOICE ) );
    }

    /**
     * The former spellings are no longer accepted : `choise` was a typo, and
     * `Choise` broke the camelCase convention every other property follows.
     */
    public function testIncludesRejectsTheFormerSpellings(): void
    {
        $this->assertFalse( ProductAdditionalProperty::includes( 'choise' ) );
        $this->assertFalse( ProductAdditionalProperty::includes( 'Choise' ) );
    }
}
