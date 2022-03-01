<?php

namespace App\Services\Workflow\Exception;

use Symfony\Component\HttpFoundation\Response;
use Exception;

/**
 *  WorkflowException
 */
class WorkflowException extends Exception
{
    const INVALID_TRANSITION = 'Transition "%s" cannot be applied from state "%s"';

    /**
     * fromTransitionApply
     *
     * @param  string $transition
     * @param  string $from
     * @return void
     */
    public static function fromTransitionApply(string $transition, string $from)
    {
        return new self(
            sprintf(self::INVALID_TRANSITION, $transition, $from),
            Response::HTTP_UNPROCESSABLE_ENTITY
        );
    }
}
