<?php

namespace org\schema\actions ;

use org\schema\constants\traits\PlanAction as PlanActionProperties ;

/**
 * The act of planning the execution of an event/task/action.
 *
 * @see https://schema.org/PlanAction
 */
class PlanAction extends OrganizeAction
{
    use PlanActionProperties ;

    /**
     * The time the object is scheduled to.
     *
     * The moment the planned thing is due, which is not the moment the planning was
     * done : `startTime` and `endTime`, inherited from {@see \org\schema\Action}, bound
     * the act of planning itself.
     *
     * @see https://schema.org/scheduledTime
     */
    public null|string|int $scheduledTime ;
}
