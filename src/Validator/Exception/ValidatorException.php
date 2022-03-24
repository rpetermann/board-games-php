<?php

namespace App\Validator\Exception;

use Symfony\Component\HttpFoundation\Response;
use Exception;

/**
 *  ValidatorException
 */
class ValidatorException extends Exception
{
    const VALIDATION_FAILED = 'Validation failed with errors %s';

    /**
     * Undocumented function
     *
     * @param string $transition
     * @param string $from
     * @return void
     */
    public static function fromValidationError(string $errors)
    {
        return new self(
            sprintf(self::VALIDATION_FAILED, $errors),
            Response::HTTP_UNPROCESSABLE_ENTITY
        );
    }
}
