<?php

namespace tests\xyz\oihana\schema\appointments ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\Reflection;

use org\schema\Action;
use org\schema\actions\PlanAction;
use org\schema\actions\ScheduleAction;
use org\schema\constants\Schema;

use xyz\oihana\schema\appointments\CustomerAppointment;
use xyz\oihana\schema\appointments\FollowUp;
use xyz\oihana\schema\appointments\VisitReport;
use xyz\oihana\schema\constants\Oihana;

class FollowUpTest extends TestCase
{
    public function testIsAScheduleAction(): void
    {
        $followUp = new FollowUp() ;

        $this->assertInstanceOf( ScheduleAction::class , $followUp );
        $this->assertInstanceOf( PlanAction::class     , $followUp );
        $this->assertInstanceOf( Action::class         , $followUp );
    }

    public function testSchemaType(): void
    {
        $this->assertSame( 'https://schema.oihana.xyz/FollowUp' , FollowUp::getSchemaType() );
    }

    /**
     * Everything a promise needs is already published : when it is due, whether it
     * is still owed, who owes it, and what it says.
     */
    public function testItStatesWhatIsOwedAndWhen(): void
    {
        $followUp = new FollowUp
        ([
            Oihana::FOLLOW_UP_TYPE => 'CALL' ,
            Schema::SCHEDULED_TIME => '2026-09-10' ,
            Schema::ACTION_STATUS  => 'https://schema.org/PotentialActionStatus' ,
            Schema::DESCRIPTION    => 'Call back once the quotation has been sent.' ,
        ]);

        $this->assertSame( 'CALL'                                      , $followUp->followUpType  );
        $this->assertSame( '2026-09-10'                                , $followUp->scheduledTime );
        $this->assertSame( 'https://schema.org/PotentialActionStatus'  , $followUp->actionStatus  );
        $this->assertSame( 'Call back once the quotation has been sent.'            , $followUp->description   );
    }

    /**
     * A promise with no slot is the ordinary case : it is owed, and nothing has been
     * booked to honour it yet.
     */
    public function testAPromiseNeedsNoAppointment(): void
    {
        $followUp = new FollowUp([ Oihana::FOLLOW_UP_TYPE => 'CALL' ] );

        $this->assertNull( $followUp->result ?? null );
    }

    /**
     * @throws ReflectionException
     */
    public function testTheBookedMeetingIsReadBackAsAnAppointment(): void
    {
        $followUp = new Reflection()->hydrate
        (
            [
                Oihana::FOLLOW_UP_TYPE => 'VISIT' ,
                Schema::RESULT         => [ Schema::NAME => 'On-site review' , Schema::START_DATE => '2026-10-01T09:00:00+02:00' ] ,
            ],
            FollowUp::class
        );

        $this->assertInstanceOf( CustomerAppointment::class , $followUp->result       );
        $this->assertSame( 'On-site review'      , $followUp->result->name );
    }

    /**
     * A report carries its promises, and each is read back as one.
     *
     * @throws ReflectionException
     */
    public function testAReportCarriesItsFollowUps(): void
    {
        $report = new Reflection()->hydrate
        (
            [
                Oihana::FOLLOW_UP =>
                [
                    [ Oihana::FOLLOW_UP_TYPE => 'QUOTE' , Schema::SCHEDULED_TIME => '2026-09-05' ] ,
                    [ Oihana::FOLLOW_UP_TYPE => 'CALL'  , Schema::SCHEDULED_TIME => '2026-09-10' ] ,
                ],
            ],
            VisitReport::class
        );

        $this->assertIsArray( $report->followUp );
        $this->assertCount( 2 , $report->followUp );
        $this->assertInstanceOf( FollowUp::class , $report->followUp[ 0 ] );
        $this->assertSame( 'QUOTE'      , $report->followUp[ 0 ]->followUpType  );
        $this->assertSame( '2026-09-10' , $report->followUp[ 1 ]->scheduledTime );
    }
}
