<?php

namespace tests\org\schema\helpers;

use ArrayObject;
use org\schema\helpers\SchemaResolver;
use PHPUnit\Framework\TestCase;
use stdClass;

class SchemaResolverTest extends TestCase
{
    public function testReturnsMappedClassForArrayTarget(): void
    {
        $resolver = new SchemaResolver(
            'type',
            [
                'customer' => stdClass::class,
                'place'    => ArrayObject::class
            ],
            null
        );

        $target = ['type' => 'customer'];
        $this->assertSame( stdClass::class, $resolver($target));

        $target = ['type' => 'place'];
        $this->assertSame( ArrayObject::class, $resolver($target));
    }

    public function testReturnsMappedClassForObjectTarget(): void
    {
        $resolver = new SchemaResolver(
            'type',
            ['customer' => stdClass::class],
            null
        );

        $target = (object)['type' => 'customer'];
        $this->assertSame( stdClass::class, $resolver($target));
    }

    public function testReturnsDefaultWhenTypeNotFound(): void
    {
        $resolver = new SchemaResolver(
            'type',
            ['customer' => stdClass::class],
            ArrayObject::class
        );

        $target = ['type' => 'unknown'];
        $this->assertSame( ArrayObject::class, $resolver($target));
    }

    public function testReturnsNullWhenTargetNotArrayOrObject(): void
    {
        $resolver = new SchemaResolver('type', ['customer' => stdClass::class]);

        $this->assertNull($resolver(null));
        $this->assertNull($resolver('string'));
        $this->assertNull($resolver(123));
        $this->assertNull($resolver(1.23));
        $this->assertNull($resolver(true));
    }

    public function testReturnsNullWhenMappedValueNotStringOrEmpty(): void
    {
        $resolver = new SchemaResolver
        (
            'type',
            [
                'bad1' => null,
                'bad2' => '',
                'bad3' => 'hello',
                'good' => stdClass::class
            ]
        );

        $this->assertNull($resolver(['type' => 'bad1']));
        $this->assertNull($resolver(['type' => 'bad2']));
        $this->assertNull($resolver(['type' => 'bad3']));
        $this->assertSame( stdClass::class, $resolver(['type' => 'good']));
    }

    public function testWorksWithObjectsMissingTypeKey(): void
    {
        $resolver = new SchemaResolver(
            'type',
            ['customer' => stdClass::class],
            null
        );

        $target = (object)['otherKey' => 'value'];
        $this->assertNull($resolver($target));
    }

    public function testWorksWithArraysMissingTypeKey(): void
    {
        $resolver = new SchemaResolver(
            'type',
            ['customer' => stdClass::class],
            null
        );

        $target = ['otherKey' => 'value'];
        $this->assertNull($resolver($target));
    }
}