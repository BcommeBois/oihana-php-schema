<?php

namespace xyz\oihana\schema\enumerations;

use org\schema\Enumeration;

/**
 * Enumeration of additionalType values for AuditAction.
 *
 * Maps audit events to their corresponding Schema.org Action types.
 * Each constant is the full Schema.org URL used as additionalType.
 *
 * @package xyz\oihana\schema\auth\enums
 * @author  Marc Alcaraz
 *
 * @see https://schema.org/Action
 */
class AuditActionType extends Enumeration
{
    // ------- Document operations

    /**
     * A new resource was created (POST).
     * @see https://schema.org/CreateAction
     */
    public const string CREATE = 'https://schema.org/CreateAction' ;

    /**
     * An existing resource was updated (PATCH/PUT).
     * @see https://schema.org/UpdateAction
     */
    public const string UPDATE = 'https://schema.org/UpdateAction' ;

    /**
     * A resource was deleted (DELETE).
     * @see https://schema.org/DeleteAction
     */
    public const string DELETE = 'https://schema.org/DeleteAction' ;

    // ------- Edge operations (relationships)

    /**
     * A relationship was added (POST on edge route).
     * @see https://schema.org/AddAction
     */
    public const string ADD = 'https://schema.org/AddAction' ;

    // ------- Authentication

    /**
     * A user logged in.
     * @see https://schema.org/LoginAction
     */
    public const string LOGIN = 'https://schema.org/LoginAction' ;

    /**
     * A user logged out (session deactivated).
     * @see https://schema.org/DeactivateAction
     */
    public const string LOGOUT = 'https://schema.org/DeactivateAction' ;

    // ------- Authorization

    /**
     * An action was rejected (403 Forbidden).
     * @see https://schema.org/RejectAction
     */
    public const string REJECT = 'https://schema.org/RejectAction' ;
}
