<?php

namespace tests\xyz\oihana\schema\helpers\hydrate\appointments ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use org\schema\constants\Schema;
use org\schema\DefinedTerm;
use org\schema\Organization;
use org\schema\Person;

use xyz\oihana\schema\appointments\Appointment;
use xyz\oihana\schema\appointments\FollowUp;

use function xyz\oihana\schema\helpers\hydrate\appointments\hydrateFollowUp;

final class HydrateFollowUpTest extends TestCase
{
    /**
     * @throws ReflectionException
     */
    public function testHydratesASingleFollowUpWithItsNestedReferences(): void
    {
        $followUp = hydrateFollowUp
        ([
            'followUpType'  => [ 'id' => 'CALL_BACK' , 'name' => 'Call back' ] ,
            'scheduledTime' => '2026-09-15' ,
            'agent'         => [ Schema::AT_TYPE => 'Person' , 'name' => 'Richard Roe' ] ,
        ]) ;

        $this->assertInstanceOf( FollowUp::class    , $followUp ) ;
        $this->assertInstanceOf( DefinedTerm::class , $followUp->followUpType ) ;
        $this->assertInstanceOf( Person::class      , $followUp->agent ) ;
        $this->assertSame( 'Richard Roe' , $followUp->agent->name ) ;
    }

    /**
     * The `Organization|Person` union is settled by the payload's `@type` : whoever
     * owes the promise may be a company as well as a person.
     *
     * @throws ReflectionException
     */
    public function testTheAgentMayBeAnOrganization(): void
    {
        $followUp = hydrateFollowUp( [ 'agent' => [ 'name' => 'Acme Corporation' ] ] ) ;

        $this->assertInstanceOf( Organization::class , $followUp->agent ) ;
    }

    /**
     * 🚨 The meeting a follow-up names is a **reference**, not a document to unfold :
     * it is typed one level and no further. Going down would follow meeting → report →
     * follow-up → meeting for as long as the data holds, and only the data would stop
     * it.
     *
     * @throws ReflectionException
     */
    public function testTheResultIsTypedFlatAndGoesNoDeeper(): void
    {
        $followUp = hydrateFollowUp
        ([
            'followUpType' => [ 'id' => 'VISIT_AGAIN' ] ,
            'result'       =>
            [
                'name'   => 'Second meeting with Acme Corporation' ,
                'report' => [ 'mood' => [ 'id' => 'SATISFIED' ] , 'followUp' => [ [ 'followUpType' => [ 'id' => 'CALL_BACK' ] ] ] ] ,
            ] ,
        ]) ;

        $meeting = $followUp->result ;

        $this->assertInstanceOf( Appointment::class , $meeting ) ;
        $this->assertSame( 'Second meeting with Acme Corporation' , $meeting->name ) ;

        // One level, and no further : what the meeting itself carries stays raw.
        $this->assertIsArray( $meeting->report ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testHydratesAnIndexedArrayAndFiltersTheRest(): void
    {
        $followUps = hydrateFollowUp
        ([
            [ 'followUpType' => [ 'id' => 'CALL_BACK'   ] ] ,
            [ 'followUpType' => [ 'id' => 'SEND_QUOTE'  ] ] ,
        ]) ;

        $this->assertIsArray( $followUps ) ;
        $this->assertCount( 2 , $followUps ) ;
        $this->assertContainsOnlyInstancesOf( FollowUp::class , $followUps ) ;
    }

    /**
     * 🔑 « This report has no follow-up » is an answer worth serving, and it is not the
     * answer « nothing here was readable » : the empty list is kept, where the rest of
     * the family answers `null`.
     *
     * @throws ReflectionException
     */
    public function testAnEmptyListStaysAnEmptyList(): void
    {
        $this->assertSame( [] , hydrateFollowUp( [] ) ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testReturnsANonArrayInputUnchanged(): void
    {
        $this->assertNull( hydrateFollowUp() ) ;
        $this->assertSame( 'follow-up-ref-42' , hydrateFollowUp( 'follow-up-ref-42' ) ) ;

        $followUp = new FollowUp() ;
        $this->assertSame( $followUp , hydrateFollowUp( $followUp ) ) ;
    }

    /**
     * A reference nobody joined yet stays what it was : hydration reads what the
     * payload holds, it never rewrites a value it cannot resolve.
     *
     * @throws ReflectionException
     */
    public function testLeavesUnresolvedReferencesUntouched(): void
    {
        $followUp = hydrateFollowUp
        ([
            'agent'        => 'seller-ref-42' ,
            'result'       => 'appointment-ref-42' ,
            'followUpType' => 'CALL_BACK' ,
        ]) ;

        $this->assertSame( 'seller-ref-42'       , $followUp->agent ) ;
        $this->assertSame( 'appointment-ref-42'  , $followUp->result ) ;
        $this->assertSame( 'CALL_BACK'           , $followUp->followUpType ) ;
    }

    /**
     * Every nested reference answers the same through the follow-up as through the
     * nested helper called on its own, otherwise the same fact takes two shapes
     * depending on the path taken.
     *
     * @throws ReflectionException
     */
    public function testEmptyNestedListsYieldNullNotAnEmptyArray(): void
    {
        $followUp = hydrateFollowUp( [ 'followUpType' => [] , 'agent' => [] ] ) ;

        $this->assertInstanceOf( FollowUp::class , $followUp ) ;
        $this->assertNull( $followUp->followUpType ) ;
        $this->assertNull( $followUp->agent ) ;
    }

    /**
     * A promise produces one meeting, and that is what the property means — but a value
     * written as a list is still read as a list, each entry typed flat, and one that
     * resolves to nothing answers `null` rather than a leftover raw array.
     *
     * @throws ReflectionException
     */
    public function testAListOfResultsIsReadAsAList(): void
    {
        $followUp = hydrateFollowUp
        ([
            'result' =>
            [
                [ 'name' => 'Second meeting with Acme Corporation' ] ,
                [ 'name' => 'Third meeting with Acme Corporation'  ] ,
            ] ,
        ]) ;

        $this->assertIsArray( $followUp->result ) ;
        $this->assertCount( 2 , $followUp->result ) ;
        $this->assertContainsOnlyInstancesOf( Appointment::class , $followUp->result ) ;

        $this->assertNull( hydrateFollowUp( [ 'result' => [] ] )->result ) ;

        // A meeting nobody joined yet is a handle, and a handle in a list is still a handle.
        $this->assertSame( [ 'appointment-ref-42' ] , hydrateFollowUp( [ 'result' => [ 'appointment-ref-42' ] ] )->result ) ;
        $this->assertNull( hydrateFollowUp( [ 'result' => [ null ] ] )->result ) ;
    }

    /**
     * A list where nothing resolved answers `null` : the empty list says « none », this one
     * says « nothing here was readable », and the two are not the same answer. A list of
     * handles is neither — it is the answer itself.
     *
     * @throws ReflectionException
     */
    public function testAListThatResolvesToNothingAnswersNull(): void
    {
        $this->assertNull( hydrateFollowUp( [ null , null ] ) ) ;
        $this->assertSame( [ 'follow-up-ref-42' , 42 ] , hydrateFollowUp( [ 'follow-up-ref-42' , 42 ] ) ) ;
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
        $bare = hydrateFollowUp( [ 'follow-up-ref-42' , 'follow-up-ref-42' ] ) ;

        $this->assertSame( [ 'follow-up-ref-42' , 'follow-up-ref-42' ] , $bare ) ;

        $mixed = hydrateFollowUp( [ 'follow-up-ref-42' , [ 'followUpType' => [ 'id' => 'CALL_BACK' ] ] ] ) ;

        $this->assertSame( [ 0 , 1 ] , array_keys( $mixed ) ) ;
        $this->assertSame( 'follow-up-ref-42' , $mixed[ 0 ] ) ;
        $this->assertInstanceOf( FollowUp::class , $mixed[ 1 ] ) ;
    }
}
