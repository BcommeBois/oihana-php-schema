<?php

namespace xyz\oihana\schema;

use org\schema\Thing;
use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\constants\traits\LogTrait;

/**
 * @package oihana\logging
 */
class Log extends Thing
{
    use LogTrait ;

    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;

    /**
     * The date of the log (YYYY-MM-DD).
     * @var string|null
     */
    public ?string $date ;

    /**
     * The level of the log (e.g., INFO, ERROR, DEBUG).
     * @var int|string|null
     */
    public int|string|null $level ;

    /**
     * The message of the log.
     * @var string|null
     */
    public ?string $message ;

    /**
     * The time of the log.
     * @var string|null
     */
    public ?string $time ;

    /**
     * Returns the array representation of the log definition.
     * @return array
     */
    public function toArray() : array
    {
        return
            [
                self::DATE    => $this->date    ?? null ,
                self::TIME    => $this->time    ?? null ,
                self::LEVEL   => $this->level   ?? null ,
                self::MESSAGE => $this->message ?? null ,
            ];
    }

    /**
     * Returns the string representation of the thing.
     * @return string
     */
    public function __toString():string
    {
        return implode( ' ' , [ $this->date , $this->time , $this->level , $this->message ] ) ;
    }
}