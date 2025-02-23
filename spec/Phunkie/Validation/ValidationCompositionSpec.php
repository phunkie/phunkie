<?php

namespace spec\Phunkie\Validation;

use Md\Unit\TestCase;
use function Phunkie\Functions\applicative\map2;

class ValidationCompositionSpec extends TestCase
{
    /**
     * @test
     */
    public function it_composes_validations_with_flatMap()
    {
        $trim = fn($s) => Success(trim($s));
        $notEmpty = fn($s) => 
            strlen($s) > 0 ? Success($s) : Failure(Nel("Empty string"));

        $result = Success("  test  ")
            ->flatMap($trim)
            ->flatMap($notEmpty);

        $this->assertIsLike($result, Success("test"));

        $result = Success("")
            ->flatMap($trim)
            ->flatMap($notEmpty);

        $this->assertIsLike($result, Failure(Nel("Empty string")));
    }

    /**
     * @test
     */
    public function it_combines_validations_with_map2()
    {
        $validateUsername = fn($u) => 
            strlen($u) >= 3 ? Success($u) : Failure(Nel("Username too short"));
            
        $validatePassword = fn($p) =>
            strlen($p) >= 8 ? Success($p) : Failure(Nel("Password too short"));

        $result = map2(
            fn($u, $p) => ["username" => $u, "password" => $p]
        )($validateUsername("usr"))($validatePassword("short"));

        $this->assertIsLike(
            $result,
            Failure(Nel("Password too short"))
        );
    }
} 