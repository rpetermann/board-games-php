<?php

namespace App\Helpers;

/**
 * MathTrait
 */
trait MathTrait
{
    /**
     * isSumOfValuesEven
     *
     * @param integer $value1
     * @param integer $value2
     * @return boolean
     */
    public function isSumOfValuesEven(int $value1, int $value2): bool
    {
        return (($value1 + $value2) % 2) === 0;
    }

    /**
     * isSubtractionOfValuesPositive
     *
     * @param integer $value1
     * @param integer $value2
     * @return boolean
     */
    public function isSubtractionOfValuesPositive(int $value1, int $value2): bool
    {
        return ($value1 - $value2) > 0;
    }
}
