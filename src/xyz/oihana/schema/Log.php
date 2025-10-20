<?php

namespace xyz\oihana\schema;

use org\schema\Thing;
use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\constants\traits\LogTrait;

/**
 * Represents a log entry.
 *
 * This class defines a structured representation of a log message — including its date,
 * time, level, and message — following the {@see Oihana::SCHEMA} context for JSON-LD interoperability.
 *
 * It extends the {@see Thing} base type from Schema.org and uses {@see LogTrait} to provide
 * common logging-related constants and methods.
 *
 * ### Example
 * ```php
 * use xyz\oihana\schema\Log;
 *
 * $log = new Log();
 * $log->date    = '2025-10-20';
 * $log->time    = '14:32:10';
 * $log->level   = 'INFO';
 * $log->message = 'Application started successfully.';
 *
 * echo $log;
 * // Output: "2025-10-20 14:32:10 INFO Application started successfully."
 *
 * print_r($log->toArray());
 * // [
 * //   'date'    => '2025-10-20',
 * //   'time'    => '14:32:10',
 * //   'level'   => 'INFO',
 * //   'message' => 'Application started successfully.'
 * // ]
 * ```
 *
 * @package xyz\oihana\schema
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.1
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