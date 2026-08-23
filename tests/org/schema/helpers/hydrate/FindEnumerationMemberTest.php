<?php

namespace tests\org\schema\helpers\hydrate ;

use PHPUnit\Framework\TestCase;

use org\schema\enumerations\events\EventCancelled;
use org\schema\enumerations\events\EventPostponed;
use org\schema\enumerations\events\EventStatusType;

use function org\schema\helpers\hydrate\findEnumerationMember;

final class FindEnumerationMemberTest extends TestCase
{
    /**
     * @var array<string,string>
     */
    private const array MEMBERS =
    [
        EventStatusType::CANCELLED => EventCancelled::class ,
        EventStatusType::POSTPONED => EventPostponed::class ,
    ];

    public function testReadsTheMemberFromTheStatedUri(): void
    {
        $class = findEnumerationMember
        (
            [ 'additionalType' => EventStatusType::POSTPONED ] ,
            self::MEMBERS ,
            EventStatusType::class
        );

        $this->assertSame( EventPostponed::class , $class ) ;
    }

    /**
     * `@type` carries the short name of the class, never the URI — which is what a
     * payload serialized before `additionalType` existed holds alone.
     */
    public function testReadsTheMemberFromTheShortName(): void
    {
        $class = findEnumerationMember( [ '@type' => 'EventCancelled' ] , self::MEMBERS , EventStatusType::class ) ;

        $this->assertSame( EventCancelled::class , $class ) ;
    }

    /**
     * The URI is the identifier ; the short name is only how the serialization spells
     * the class. When both are present and disagree, the identifier wins.
     */
    public function testTheStatedUriWinsOverTheShortName(): void
    {
        $class = findEnumerationMember
        (
            [ 'additionalType' => EventStatusType::POSTPONED , '@type' => 'EventCancelled' ] ,
            self::MEMBERS ,
            EventStatusType::class
        );

        $this->assertSame( EventPostponed::class , $class ) ;
    }

    /**
     * A URI this vocabulary does not publish says nothing about the class : the short
     * name is still read, rather than the whole payload being given up on.
     */
    public function testAnUnknownUriFallsBackToTheShortName(): void
    {
        $class = findEnumerationMember
        (
            [ 'additionalType' => 'https://schema.org/EventHappened' , '@type' => 'EventCancelled' ] ,
            self::MEMBERS ,
            EventStatusType::class
        );

        $this->assertSame( EventCancelled::class , $class ) ;
    }

    public function testAnswersTheDefaultWhenNothingIsRecognized(): void
    {
        $this->assertSame( EventStatusType::class , findEnumerationMember( [] , self::MEMBERS , EventStatusType::class ) ) ;

        $this->assertSame
        (
            EventStatusType::class ,
            findEnumerationMember( [ '@type' => 'EventHappened' , 'description' => 'It went ahead.' ] , self::MEMBERS , EventStatusType::class )
        );
    }

    /**
     * Both keys are declared with types wide enough to hold something else — an array,
     * an object. Anything but a string names no class.
     */
    public function testIgnoresKeysThatAreNotStrings(): void
    {
        $class = findEnumerationMember
        (
            [ 'additionalType' => [ EventStatusType::POSTPONED ] , '@type' => [ 'EventCancelled' ] ] ,
            self::MEMBERS ,
            EventStatusType::class
        );

        $this->assertSame( EventStatusType::class , $class ) ;
    }
}
