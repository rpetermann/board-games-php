<?php

namespace App\Validator;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Validator
 */
class Validator
{
    /**
     * validator
     *
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * __construct
     *
     * @param  ValidatorInterface $validator
     * @return void
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * hasError
     *
     * @param  object $subject
     * @return ConstraintViolationListInterface
     */
    public function validate(object $subject): ConstraintViolationListInterface
    {
        return $this->validator->validate($subject);
    }
}
