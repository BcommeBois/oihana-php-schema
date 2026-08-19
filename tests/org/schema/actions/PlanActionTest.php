<?php

namespace tests\org\schema\actions ;

use PHPUnit\Framework\TestCase;

use org\schema\Action;
use org\schema\actions\OrganizeAction;
use org\schema\actions\PlanAction;
use org\schema\actions\ScheduleAction;
use org\schema\constants\Schema;
use org\schema\Thing;

class PlanActionTest extends TestCase
{
    public function testIsAnOrganizeActionAndAnAction(): void
    {
        $action = new PlanAction() ;

        $this->assertInstanceOf( OrganizeAction::class , $action );
        $this->assertInstanceOf( Action::class         , $action );
        $this->assertInstanceOf( Thing::class          , $action );
    }

    public function testScheduledTimeDefaultsToNull(): void
    {
        $this->assertNull( new PlanAction()->scheduledTime ?? null );
    }

    public function testConstructorCopiesTheScheduledTime(): void
    {
        $action = new PlanAction([ Schema::SCHEDULED_TIME => '2026-09-10T08:00:00+02:00' ] );

        $this->assertSame( '2026-09-10T08:00:00+02:00' , $action->scheduledTime );
    }

    /**
     * A plan states two different moments, and collapsing them loses the point : the
     * act of planning happens now, the thing planned is due later.
     */
    public function testTheScheduledTimeIsNotTheTimeOfThePlanningItself(): void
    {
        $action = new PlanAction
        ([
            Schema::START_TIME     => '2026-09-03T10:41:00+02:00' ,
            Schema::END_TIME       => '2026-09-03T10:42:00+02:00' ,
            Schema::SCHEDULED_TIME => '2026-09-10' ,
        ]);

        $this->assertSame( '2026-09-03T10:41:00+02:00' , $action->startTime     );
        $this->assertSame( '2026-09-03T10:42:00+02:00' , $action->endTime       );
        $this->assertSame( '2026-09-10'                , $action->scheduledTime );
    }

    /**
     * The property is declared once, on the act of planning, and every subtype that
     * schedules something reads it from there.
     */
    public function testScheduleActionInheritsTheScheduledTime(): void
    {
        $action = new ScheduleAction([ Schema::SCHEDULED_TIME => '2026-09-10' ] );

        $this->assertInstanceOf( PlanAction::class , $action                );
        $this->assertSame( '2026-09-10'            , $action->scheduledTime );
    }

    public function testTheConstantNamesTheProperty(): void
    {
        $this->assertSame( 'scheduledTime' , Schema::SCHEDULED_TIME );
    }

    public function testItSerializesTheScheduledTime(): void
    {
        $action = new PlanAction([ Schema::SCHEDULED_TIME => '2026-09-10' ] );

        $this->assertSame( '2026-09-10' , $action->jsonSerialize()[ Schema::SCHEDULED_TIME ] ?? null );
    }
}
