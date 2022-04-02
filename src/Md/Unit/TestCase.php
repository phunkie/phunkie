<?php

namespace Md\Unit;

use PHPUnit\Framework\TestCase as UnitTestCase;

abstract class TestCase extends UnitTestCase
{
    use AssertIsLike;

    protected function setUp(): void
    {
        parent::setUp();
    }
}
