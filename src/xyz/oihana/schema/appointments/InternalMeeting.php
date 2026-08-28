<?php

namespace xyz\oihana\schema\appointments;

use xyz\oihana\schema\constants\Oihana;

/**
 * A meeting with nobody outside — colleagues around a table, a room, a call.
 *
 * An {@see Appointment} that declares nothing of its own, and that is the whole
 * point : a meeting between colleagues has no counterpart, so
 * {@see Appointment::$about} stays empty, and everything else a diary needs — the
 * moment, the place, who is expected, what became of it, what was written
 * afterwards — is already there.
 *
 * 🔑 **What it carries is its stored type.** A family is told apart by the type a
 * document carries, which is what filters, facets and permissions read : a class
 * of its own is what lets « the meetings between colleagues » be a clause rather
 * than a guess.
 *
 * 🔑 **Its attendees are accounts.** Nobody outside is expected, so the people
 * around the table are the ones who hold a diary here.
 *
 * ⚠️ **It is not a leaf.** The internal side is meant to branch — a management
 * meeting, a team one, and the occasions that are not meetings at all — and each
 * of those will carry its own stored type. Nothing of that is open today.
 *
 * @package xyz\oihana\schema\appointments
 * @author  Marc Alcaraz (eKameleon)
 * @since   1.5.0
 */
class InternalMeeting extends Appointment
{
    /**
     * The @context of the json-ld representation of the thing.
     */
    public const string CONTEXT = Oihana::SCHEMA ;
}
