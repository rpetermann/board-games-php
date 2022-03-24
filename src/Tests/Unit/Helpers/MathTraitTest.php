<?php

namespace App\Tests\Unit\Helpers;

use App\Tests\Unit\AbstractUnitTest;
use App\Helpers\MathTrait;

/**
 * MathTraitTest
 */
class MathTraitTest extends AbstractUnitTest
{
    /**
     * @var MathTrait
     */
    protected $mathTrait;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->mathTrait = $this->getMockForTrait(MathTrait::class);
    }

    /**
     * dataProviderIsSumOfValuesEven
     *
     * @return array
     */
    public function dataProviderIsSumOfValuesEven(): array
    {
        return [
            'even' => [
                'value1' => 0,
                'value2' => 0,
                'expectedResponse' => true,
            ],
            'even' => [
                'value1' => 2,
                'value2' => 0,
                'expectedResponse' => true,
            ],
            'even' => [
                'value1' => 2,
                'value2' => 200,
                'expectedResponse' => true,
            ],
            'odd' => [
                'value1' => 0,
                'value2' => 1,
                'expectedResponse' => false,
            ],
            'odd' => [
                'value1' => 3,
                'value2' => 3,
                'expectedResponse' => false,
            ],
            'odd' => [
                'value1' => 8,
                'value2' => 201,
                'expectedResponse' => false,
            ],
        ];
    }

    /**
     * testIsSumOfValuesEven
     * 
     * @dataProvider dataProviderIsSumOfValuesEven
     *
     * @param integer $value1
     * @param integer $value2
     * @param boolean $expectedResponse
     * @return void
     */
    public function testIsSumOfValuesEven(int $value1, int $value2, bool $expectedResponse): void
    {
        $this->assertSame(
            $expectedResponse,
            $this->mathTrait->isSumOfValuesEven($value1, $value2)
        );
    }

    /**
     * dataProviderIsSubtractionOfValuesPositive
     *
     * @return array
     */
    public function dataProviderIsSubtractionOfValuesPositive(): array
    {
        return [
            'positive' => [
                'value1' => 0,
                'value2' => 0,
                'expectedResponse' => true,
            ],
            'positive' => [
                'value1' => 2,
                'value2' => 0,
                'expectedResponse' => true,
            ],
            'positive' => [
                'value1' => 200,
                'value2' => 2,
                'expectedResponse' => true,
            ],
            'negative' => [
                'value1' => 0,
                'value2' => 2,
                'expectedResponse' => false,
            ],
            'negative' => [
                'value1' => 3,
                'value2' => 4,
                'expectedResponse' => false,
            ],
            'negative' => [
                'value1' => 8,
                'value2' => 201,
                'expectedResponse' => false,
            ],
        ];
    }

    /**
     * testIsSubtractionOfValuesPositive
     * 
     * @dataProvider dataProviderIsSubtractionOfValuesPositive
     *
     * @param integer $value1
     * @param integer $value2
     * @param boolean $expectedResponse
     * @return void
     */
    public function testIsSubtractionOfValuesPositive(int $value1, int $value2, bool $expectedResponse): void
    {
        $this->assertSame(
            $expectedResponse,
            $this->mathTrait->isSubtractionOfValuesPositive($value1, $value2)
        );
    }
}
