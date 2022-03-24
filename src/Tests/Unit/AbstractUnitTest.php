<?php

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Tests\Common\MockTrait;
use App\Tests\Common\AssertTrait;

/**
 * AbstractUnitTest
 */
abstract class AbstractUnitTest extends TestCase
{
    use MockTrait;
    use AssertTrait;
}
