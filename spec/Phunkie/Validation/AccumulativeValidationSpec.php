<?php

namespace spec\Phunkie\Validation;

use Md\Unit\TestCase;
use PHPUnit\Framework\Attributes\Test;
use function Phunkie\Functions\semigroup\combine;

class AccumulativeValidationSpec extends TestCase
{
    #[Test]
    public function it_accumulates_failures_with_combine()
    {
        $validateName = fn($name) => 
            strlen($name) > 0 ? Success($name) : Failure(Nel("Name empty"));

        $validateEmail = fn($email) =>
            str_contains($email, '@') ? Success($email) : Failure(Nel("Invalid email"));

        // Using semigroup combine for Nel
        $result = combine(
            $validateName(""),
            $validateEmail("invalid")
        );

        $this->assertIsLike(
            $result,
            Failure(Nel("Name empty", "Invalid email"))
        );
    }

    #[Test]
    public function it_succeeds_when_all_validations_succeed()
    {
        $result = combine(
            Success("John"),
            Success("john@email.com")
        );

        $this->assertIsLike(
            $result,
            Success(Pair("John", "john@email.com"))
        );
    }
}
