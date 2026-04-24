<?php

namespace tests\xyz\oihana\schema\constants\traits\auth ;

use PHPUnit\Framework\TestCase;

use xyz\oihana\schema\constants\traits\auth\ApplicationTemplateTrait;

class ApplicationTemplateTraitTest extends TestCase
{
    public function testConstantsValues(): void
    {
        $host = new class { use ApplicationTemplateTrait; };

        $expected =
        [
            'APPLICATION_TEMPLATES'       => 'applicationTemplates' ,
            'APPLICATION_TEMPLATES_COUNT' => 'applicationTemplatesCount' ,
            'SELF_SERVICE'                => 'selfService' ,
        ];

        foreach ( $expected as $name => $value )
        {
            $this->assertSame( $value , constant( $host::class . "::$name" ) );
        }
    }
}
