<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Phunkie\Types;

use Md\Unit\TestCase;
use Md\PropertyTesting\TestTrait;
use Eris\Generator\IntegerGenerator as IntGen;
use Phunkie\Cats\Applicative;
use Phunkie\Cats\Functor;
use Phunkie\Cats\Monad;
use Phunkie\Types\Left;
use Phunkie\Types\Right;
use function Phunkie\Functions\applicative\ap;
use function Phunkie\Functions\applicative\map2;
use function Phunkie\Functions\applicative\pure;
use function Phunkie\Functions\functor\fmap;
use function Phunkie\Functions\monad\bind;
use function Phunkie\Functions\monad\flatten;

class EitherSpec extends TestCase
{
    use TestTrait;
    /**
     * @test
     */
    public function it_creates_a_right_either()
    {
        $either = Right(42);

        $this->assertInstanceOf(Right::class, $either);
        $this->assertTrue($either->isRight());
        $this->assertFalse($either->isLeft());
    }

    /**
     * @test
     */
    public function it_creates_a_left_either()
    {
        $either = Left("error");

        $this->assertInstanceOf(Left::class, $either);
        $this->assertTrue($either->isLeft());
        $this->assertFalse($either->isRight());
    }

    /**
     * @test
     */
    public function it_is_showable()
    {
        $this->assertEquals("Right(42)", (string)Right(42));
        $this->assertEquals('Left("error")', (string)Left("error"));
    }

    /**
     * @test
     */
    public function it_is_a_functor()
    {
        $right = Right(5);
        $this->assertInstanceOf(Functor::class, $right);

        $this->assertIsLike(
            $right->map(fn($x) => $x * 2),
            Right(10)
        );

        // Left is not mapped
        $left = Left("error");
        $this->assertIsLike(
            $left->map(fn($x) => $x * 2),
            Left("error")
        );
    }

    /**
     * @test
     */
    public function it_maps_right_values_only()
    {
        $right = Right(10);
        $left = Left("error");

        $this->assertIsLike(
            $right->map(fn($x) => $x + 5),
            Right(15)
        );

        $this->assertIsLike(
            $left->map(fn($x) => $x + 5),
            Left("error")
        );
    }

    /**
     * @test
     */
    public function it_is_a_monad()
    {
        $right = Right(5);
        $this->assertInstanceOf(Monad::class, $right);

        // flatMap on Right
        $this->assertIsLike(
            $right->flatMap(fn($x) => Right($x * 2)),
            Right(10)
        );

        // flatMap on Left returns Left
        $left = Left("error");
        $this->assertIsLike(
            $left->flatMap(fn($x) => Right($x * 2)),
            Left("error")
        );

        // flatMap can change to Left
        $this->assertIsLike(
            $right->flatMap(fn($x) => Left("failed")),
            Left("failed")
        );
    }

    /**
     * @test
     */
    public function it_supports_bind()
    {
        $this->assertIsLike(
            (bind(fn($x) => Right($x + 1)))(Right(41)),
            Right(42)
        );

        $this->assertIsLike(
            (bind(fn($x) => Right($x + 1)))(Left("error")),
            Left("error")
        );
    }

    /**
     * @test
     */
    public function it_supports_flatten()
    {
        $this->assertIsLike(
            flatten(Right(Right(42))),
            Right(42)
        );

        $this->assertIsLike(
            flatten(Left(Left("error"))),
            Left("error")
        );

        $this->assertIsLike(
            flatten(Right(Left("inner error"))),
            Left("inner error")
        );
    }

    /**
     * @test
     */
    public function it_is_an_applicative()
    {
        $right = Right(5);
        $this->assertInstanceOf(Applicative::class, $right);

        // Apply function wrapped in Right
        $this->assertIsLike(
            (ap(Right(fn($x) => $x * 2)))(Right(5)),
            Right(10)
        );

        // Apply to Left returns Left
        $this->assertIsLike(
            (ap(Right(fn($x) => $x * 2)))(Left("error")),
            Left("error")
        );

        // Left function returns Left
        $this->assertIsLike(
            (ap(Left("function error")))(Right(5)),
            Left("function error")
        );
    }

    /**
     * @test
     */
    public function it_supports_pure()
    {
        $this->assertIsLike(
            (pure("Right"))(42),
            Right(42)
        );

        $this->assertIsLike(
            (pure("Left"))("error"),
            Left("error")
        );
    }

    /**
     * @test
     */
    public function it_supports_map2()
    {
        $this->assertIsLike(
            ((map2(fn($x, $y) => $x + $y))(Right(5)))(Right(10)),
            Right(15)
        );

        // Left short-circuits
        $this->assertIsLike(
            ((map2(fn($x, $y) => $x + $y))(Left("error 1")))(Right(10)),
            Left("error 1")
        );

        $this->assertIsLike(
            ((map2(fn($x, $y) => $x + $y))(Right(5)))(Left("error 2")),
            Left("error 2")
        );
    }

    /**
     * @test
     */
    public function it_provides_getOrElse()
    {
        $this->assertEquals(42, Right(42)->getOrElse(0));
        $this->assertEquals(0, Left("error")->getOrElse(0));
    }

    /**
     * @test
     */
    public function it_converts_to_option()
    {
        $this->assertIsLike(
            Right(42)->toOption(),
            Some(42)
        );

        $this->assertIsLike(
            Left("error")->toOption(),
            None()
        );
    }

    /**
     * @test
     */
    public function it_provides_orElse()
    {
        $this->assertIsLike(
            Right(42)->orElse(Right(0)),
            Right(42)
        );

        $this->assertIsLike(
            Left("error")->orElse(Right(0)),
            Right(0)
        );

        $this->assertIsLike(
            Left("error 1")->orElse(Left("error 2")),
            Left("error 2")
        );
    }

    /**
     * @test
     */
    public function it_supports_fold()
    {
        $fold = fn($error) => "Error: $error";
        $success = fn($value) => "Success: $value";

        $this->assertEquals(
            "Success: 42",
            (Right(42)->fold($fold))($success)
        );

        $this->assertEquals(
            "Error: not found",
            (Left("not found")->fold($fold))($success)
        );
    }

    /**
     * @test
     */
    public function it_can_swap_left_and_right()
    {
        $this->assertIsLike(
            Right(42)->swap(),
            Left(42)
        );

        $this->assertIsLike(
            Left("error")->swap(),
            Right("error")
        );
    }

    /**
     * @test
     */
    public function it_supports_filter_or_else()
    {
        $isEven = fn($x) => $x % 2 === 0;

        $this->assertIsLike(
            Right(42)->filterOrElse($isEven, "not even"),
            Right(42)
        );

        $this->assertIsLike(
            Right(41)->filterOrElse($isEven, "not even"),
            Left("not even")
        );

        $this->assertIsLike(
            Left("error")->filterOrElse($isEven, "not even"),
            Left("error")
        );
    }

    /**
     * @test
     */
    public function it_provides_contains()
    {
        $this->assertTrue(Right(42)->contains(42));
        $this->assertFalse(Right(42)->contains(0));
        $this->assertFalse(Left("error")->contains(42));
    }

    /**
     * @test
     */
    public function it_provides_exists()
    {
        $isPositive = fn($x) => $x > 0;

        $this->assertTrue(Right(42)->exists($isPositive));
        $this->assertFalse(Right(-1)->exists($isPositive));
        $this->assertFalse(Left("error")->exists($isPositive));
    }

    /**
     * @test
     */
    public function it_provides_forall()
    {
        $isPositive = fn($x) => $x > 0;

        $this->assertTrue(Right(42)->forall($isPositive));
        $this->assertFalse(Right(-1)->forall($isPositive));
        $this->assertTrue(Left("error")->forall($isPositive)); // vacuously true for Left
    }

    /**
     * @test
     */
    public function it_can_be_used_for_error_handling()
    {
        $divide = function($a, $b) {
            return $b === 0
                ? Left("Division by zero")
                : Right($a / $b);
        };

        $this->assertIsLike($divide(10, 2), Right(5));
        $this->assertIsLike($divide(10, 0), Left("Division by zero"));
    }

    /**
     * @test
     */
    public function it_chains_computations_with_flatMap()
    {
        $safeDivide = fn($a, $b) => $b === 0 ? Left("div by zero") : Right($a / $b);
        $safeSqrt = fn($x) => $x < 0 ? Left("negative sqrt") : Right(sqrt($x));

        $result = Right(16)
            ->flatMap(fn($x) => $safeDivide($x, 4))
            ->flatMap($safeSqrt);

        $this->assertIsLike($result, Right(2.0));

        $errorResult = Right(16)
            ->flatMap(fn($x) => $safeDivide($x, 0))
            ->flatMap($safeSqrt);

        $this->assertIsLike($errorResult, Left("div by zero"));
    }

    /**
     * @test
     */
    public function it_bimap_transforms_both_sides()
    {
        $this->assertIsLike(
            Right(5)->bimap(fn($e) => "Error: $e", fn($v) => $v * 2),
            Right(10)
        );

        $this->assertIsLike(
            Left("fail")->bimap(fn($e) => "Error: $e", fn($v) => $v * 2),
            Left("Error: fail")
        );
    }

    /**
     * @test
     */
    public function it_leftMap_transforms_left_values()
    {
        $this->assertIsLike(
            Right(42)->leftMap(fn($e) => "Error: $e"),
            Right(42)
        );

        $this->assertIsLike(
            Left("fail")->leftMap(fn($e) => "Error: $e"),
            Left("Error: fail")
        );
    }

    /**
     * @test
     */
    public function it_supports_traversal()
    {
        $result = null;
        Right(42)->foreach(function($x) use (&$result) { $result = $x * 2; });
        $this->assertEquals(84, $result);

        $result = null;
        Left("error")->foreach(function($x) use (&$result) { $result = $x * 2; });
        $this->assertNull($result);
    }

    /**
     * @test
     */
    public function it_obeys_functor_identity_law()
    {
        $this->forAll(new IntGen())->then(function ($x) {
            // Right case
            $right = Right($x);
            $this->assertIsLike(
                $right->map(fn($a) => $a),
                $right
            );

            // Left case
            $left = Left("error");
            $this->assertIsLike(
                $left->map(fn($a) => $a * 2),
                $left
            );
        });
    }

    /**
     * @test
     */
    public function it_obeys_functor_composition_law()
    {
        $this->forAll(new IntGen())->then(function ($x) {
            $f = fn($a) => $a + 1;
            $g = fn($a) => $a * 2;

            // Right case
            $right = Right($x);
            $this->assertIsLike(
                $right->map($f)->map($g),
                $right->map(fn($a) => $g($f($a)))
            );

            // Left case
            $left = Left("error");
            $this->assertIsLike(
                $left->map($f)->map($g),
                $left
            );
        });
    }

    /**
     * @test
     */
    public function it_obeys_monad_left_identity_law()
    {
        $this->forAll(new IntGen())->then(function ($x) {
            $f = fn($a) => Right($a * 2);

            // pure(a).flatMap(f) == f(a)
            $this->assertIsLike(
                Right($x)->flatMap($f),
                $f($x)
            );
        });
    }

    /**
     * @test
     */
    public function it_obeys_monad_right_identity_law()
    {
        $this->forAll(new IntGen())->then(function ($x) {
            // m.flatMap(pure) == m
            $right = Right($x);
            $this->assertIsLike(
                $right->flatMap(fn($a) => Right($a)),
                $right
            );

            $left = Left("error");
            $this->assertIsLike(
                $left->flatMap(fn($a) => Right($a)),
                $left
            );
        });
    }

    /**
     * @test
     */
    public function it_obeys_monad_associativity_law()
    {
        $this->forAll(new IntGen())->then(function ($x) {
            $f = fn($a) => Right($a + 1);
            $g = fn($a) => Right($a * 2);

            // m.flatMap(f).flatMap(g) == m.flatMap(x => f(x).flatMap(g))
            $right = Right($x);
            $this->assertIsLike(
                $right->flatMap($f)->flatMap($g),
                $right->flatMap(fn($a) => $f($a)->flatMap($g))
            );

            // Left short-circuits
            $left = Left("error");
            $this->assertIsLike(
                $left->flatMap($f)->flatMap($g),
                $left
            );
        });
    }

    /**
     * @test
     */
    public function it_obeys_applicative_identity_law()
    {
        $this->forAll(new IntGen())->then(function ($x) {
            // pure(id).apply(v) == v
            $right = Right($x);
            $this->assertIsLike(
                $right->apply(Right(fn($a) => $a)),
                $right
            );
        });
    }

    /**
     * @test
     */
    public function it_obeys_applicative_homomorphism_law()
    {
        $this->forAll(new IntGen())->then(function ($x) {
            $f = fn($a) => $a * 2;

            // pure(f).apply(pure(x)) == pure(f(x))
            $this->assertIsLike(
                Right($x)->apply(Right($f)),
                Right($f($x))
            );
        });
    }

    /**
     * @test
     */
    public function it_obeys_applicative_interchange_law()
    {
        $this->forAll(new IntGen())->then(function ($x) {
            $f = fn($a) => $a * 2;

            // u.apply(pure(y)) == pure(fn => fn(y)).apply(u)
            $this->assertIsLike(
                Right($x)->apply(Right($f)),
                Right($f)->apply(Right(fn($fn) => $fn($x)))
            );
        });
    }

    /**
     * @test
     */
    public function it_handles_errors_consistently_in_property_tests()
    {
        $this->forAll(new IntGen())->then(function ($x) {
            $safeDivide = fn($a, $b) => $b === 0 ? Left("div by zero") : Right($a / $b);

            // Division by zero always returns Left
            $this->assertIsLike(
                $safeDivide($x, 0),
                Left("div by zero")
            );

            // Division by non-zero returns Right
            if ($x !== 0) {
                $result = $safeDivide(10, $x);
                $this->assertTrue($result->isRight());
            }
        });
    }
}
