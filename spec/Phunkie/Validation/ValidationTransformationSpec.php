<?php

namespace spec\Phunkie\Validation;

use Md\Unit\TestCase;

class ValidationTransformationSpec extends TestCase
{
    /**
     * @test
     */
    public function it_transforms_to_option()
    {
        $success = Success(42);
        $failure = Failure(Nel("error"));

        $this->assertIsLike($success->toOption(), Some(42));
        $this->assertIsLike($failure->toOption(), None());
    }

    /**
     * @test
     */
    public function it_folds_over_success_and_failure()
    {
        $success = Success(42);
        $failure = Failure("error");

        $this->assertEquals(
            "Success: 42",
            $success->fold(fn($e) => "Error: " . $e)(fn($v) => "Success: " . $v)
        );

        $this->assertEquals(
            "Error: error",
            $failure->fold(fn($e) => "Error: " . $e)(fn($v) => "Success: " . $v)
        );
    }
} 