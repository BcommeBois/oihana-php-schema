<?php

namespace tests\xyz\oihana\schema\constants;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

use xyz\oihana\schema\constants\ApplicationType;

class ApplicationTypeTest extends TestCase
{
    public function testConstantsValues(): void
    {
        $expected =
        [
            'SPA'    => 'spa',
            'NATIVE' => 'native',
            'M2M'    => 'm2m',
            'WEB'    => 'web',
        ];

        foreach ($expected as $name => $value)
        {
            $this->assertSame($value, constant(ApplicationType::class . "::$name"));
        }
    }

    #[DataProvider('applicationTypeProvider')]
    public function testValidApplicationTypes(string $type): void
    {
        $this->assertContains($type,
        [
            ApplicationType::SPA,
            ApplicationType::NATIVE,
            ApplicationType::M2M,
            ApplicationType::WEB,
        ]);
    }

    public static function applicationTypeProvider(): array
    {
        return
        [
            [ApplicationType::SPA],
            [ApplicationType::NATIVE],
            [ApplicationType::M2M],
            [ApplicationType::WEB],
        ];
    }
}