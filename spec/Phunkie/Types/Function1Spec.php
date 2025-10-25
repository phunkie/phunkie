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
use Eris\Generator\StringGenerator as StringGen;
use Phunkie\Cats\Show;
use Phunkie\Cats\Functor;
use Phunkie\Cats\Applicative;
use Phunkie\Cats\Monad;
use Phunkie\Ops\Function1\Function1ApplicativeOps;
use Phunkie\Ops\Function1\Function1FunctorOps;
use Phunkie\Ops\Function1\Function1MonadOps;
use Phunkie\Ops\Function1\Function1ProfunctorOps;
use Phunkie\Types\Function1;
use function Phunkie\Functions\show\showValue;
use function Phunkie\Functions\show\usesTrait;
use const Phunkie\Functions\function1\identity;

/**
 * Comprehensive test suite for Function1 type class operations.
 *
 * Tests:
 * - Basic function operations
 * - Functor operations (map, composition)
 * - Applicative operations (pure, apply, map2)
 * - Monad operations (flatMap, flatten)
 * - Profunctor operations (dimap, lmap, rmap)
 * - Utility operations (memoize, andThen, compose)
 * - Type class laws
 */
class Function1Spec extends TestCase
{
    use TestTrait;

    private $f;
    private $g;
    private $h;

    public function setUp(): void
    {
        $this->f = Function1(fn($x): int => $x + 1);
        $this->g = Function1(fn($x): int => $x * 2);
        $this->h = Function1(fn($x): string => (string)$x);
    }

    /**
     * @test
     */
    public function it_wraps_a_callable()
    {
        $f = Function1(fn($x) => $x + 1);
        $this->assertInstanceOf(Function1::class, $f);
        $this->assertEquals(43, $f(42));
    }

    /**
     * @test
     */
    public function it_runs_with_run_method()
    {
        $this->assertEquals(43, $this->f->run(42));
    }

    /**
     * @test
     */
    public function it_is_showable()
    {
        $f = Function1(fn(int $x): int => $x + 1);
        $this->assertTrue(usesTrait($f, Show::class));
        $this->assertEquals("Function1(Int=>Int)", showValue($f));
    }

    /**
     * @test
     */
    public function it_has_all_required_traits()
    {
        $this->assertTrue(usesTrait($this->f, Function1ApplicativeOps::class));
        $this->assertTrue(usesTrait($this->f, Function1FunctorOps::class));
        $this->assertTrue(usesTrait($this->f, Function1MonadOps::class));
        $this->assertTrue(usesTrait($this->f, Function1ProfunctorOps::class));
    }

    /**
     * @test
     */
    public function it_implements_type_class_interfaces()
    {
        $this->assertInstanceOf(Functor::class, $this->f);
        $this->assertInstanceOf(Applicative::class, $this->f);
        $this->assertInstanceOf(Monad::class, $this->f);
    }

    // =========================================================================
    // FUNCTOR OPERATIONS
    // =========================================================================

    /**
     * @test
     */
    public function it_is_a_functor()
    {
        $spec = $this;
        $this->forAll(
            new IntGen()
        )->then(function ($x) use ($spec) {
            $f = Function1(fn($n) => $n + 1);
            $g = fn($n) => $n * 2;

            $mapped = $f->map($g);

            $this->assertEquals(($x + 1) * 2, $mapped($x));
        });
    }

    /**
     * @test
     */
    public function it_composes_functions_with_map()
    {
        // map should compose: f->map(g) === g . f
        $double = Function1(fn($x) => $x * 2);
        $toString = fn($x) => (string)$x;

        $composed = $double->map($toString);

        $this->assertEquals("84", $composed(42));
    }

    /**
     * @test
     */
    public function functor_identity_law()
    {
        // map(id) === id
        $spec = $this;
        $this->forAll(
            new IntGen()
        )->then(function ($x) use ($spec) {
            $f = Function1(fn($n) => $n * 2);
            $id = fn($n) => $n;

            $this->assertEquals($f($x), $f->map($id)($x));
        });
    }

    /**
     * @test
     */
    public function functor_composition_law()
    {
        // map(f . g) === map(f) . map(g)
        $spec = $this;
        $this->forAll(
            new IntGen()
        )->then(function ($x) use ($spec) {
            $f = Function1(fn($n) => $n + 1);
            $g = fn($n) => $n * 2;
            $h = fn($n) => $n - 3;

            $composed = fn($n) => $h($g($n));

            $this->assertEquals(
                $f->map($composed)($x),
                $f->map($g)->map($h)($x)
            );
        });
    }

    // =========================================================================
    // APPLICATIVE OPERATIONS
    // =========================================================================

    /**
     * @test
     */
    public function it_is_an_applicative()
    {
        $this->assertInstanceOf(Applicative::class, $this->f);
    }

    /**
     * @test
     */
    public function it_lifts_values_with_pure()
    {
        $pure42 = $this->f->pure(42);
        $this->assertInstanceOf(Function1::class, $pure42);

        // pure creates a constant function
        $this->assertEquals(42, $pure42(0));
        $this->assertEquals(42, $pure42(100));
        $this->assertEquals(42, $pure42("anything"));
    }

    /**
     * @test
     */
    public function it_applies_functions_with_apply()
    {
        // This test matches the existing behavior
        // apply: f->apply(g) means g(f(x))
        $f = Function1(fn($x) => $x + 1);
        $g = Function1(fn($x) => $x * 2);

        $result = $f->apply($g);

        // With input 1: f(1) = 2, then g(2) = 4
        $this->assertEquals(4, $result(1));
        // With input 10: f(10) = 11, then g(11) = 22
        $this->assertEquals(22, $result(10));
    }

    /**
     * @test
     */
    public function it_combines_two_functions_with_map2()
    {
        $add = Function1(fn($x) => $x + 1);
        $mult = Function1(fn($x) => $x * 2);

        $combined = $add->map2($mult, fn($a, $b) => $a + $b);

        // map2: applies both functions to the same input and combines results
        // fn($x) => $f($add($x), $mult($x))
        // With input 5: $f(6, 10) = 16
        $this->assertEquals(16, $combined(5));
    }

    /**
     * @test
     */
    public function applicative_identity_law()
    {
        // pure(id)->apply(v) === v
        $spec = $this;
        $this->forAll(
            new IntGen()
        )->then(function ($x) use ($spec) {
            $f = Function1(fn($n) => $n * 2);
            $id = Function1(identity);

            $this->assertEquals($f($x), $f->apply($id)($x));
        });
    }

    /**
     * @test
     */
    public function it_returns_an_identity_when_identity_is_applied_to_itself()
    {
        $f = Function1(identity);
        $this->assertTrue(
            $f->apply(Function1(identity))
                ->eqv(Function1::identity(), Some(42))
        );
    }

    // =========================================================================
    // MONAD OPERATIONS
    // =========================================================================

    /**
     * @test
     */
    public function it_is_a_monad()
    {
        $this->assertInstanceOf(Monad::class, $this->f);
    }

    /**
     * @test
     */
    public function it_chains_computations_with_flatMap()
    {
        // flatMap for Kleisli composition
        $addX = fn($x) => Function1(fn($y) => $x + $y);

        $double = Function1(fn($x) => $x * 2);

        // Double the input, then create an adder with that result
        $composed = $double->flatMap($addX);

        // Input 5: double(5) = 10, then returns fn($y) => 10 + $y, apply to 5 => 15
        $result = $composed(5);
        $this->assertEquals(15, $result);
    }

    /**
     * @test
     */
    public function it_flattens_nested_functions()
    {
        // Create a function that returns a function
        $nested = Function1(fn($x) => Function1(fn($y) => $x + $y));

        $flattened = $nested->flatten();

        // When flattened, both use the same input
        $this->assertEquals(10, $flattened(5)); // 5 + 5 = 10
    }

    /**
     * @test
     */
    public function flatMap_throws_on_non_function1_return()
    {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage("flatMap callback must return a Function1");

        $f = Function1(fn($x) => $x * 2);
        $bad = $f->flatMap(fn($x) => $x); // Returns int, not Function1
        $bad(5);
    }

    /**
     * @test
     */
    public function monad_left_identity_law()
    {
        // pure(a)->flatMap(f) === f(a)
        $spec = $this;
        $this->forAll(
            new IntGen()
        )->then(function ($a) use ($spec) {
            $f = fn($x) => Function1(fn($y) => $x + $y);
            $pure = Function1(fn($ignored) => $a);

            $input = 10;
            $this->assertEquals(
                $f($a)($input),
                $pure->flatMap($f)($input)
            );
        });
    }

    /**
     * @test
     */
    public function monad_right_identity_law()
    {
        // m->flatMap(pure) === m
        $spec = $this;
        $this->forAll(
            new IntGen()
        )->then(function ($x) use ($spec) {
            $m = Function1(fn($n) => $n * 2);
            $pure = fn($a) => Function1(fn($ignored) => $a);

            $this->assertEquals(
                $m($x),
                $m->flatMap($pure)($x)
            );
        });
    }

    /**
     * @test
     */
    public function monad_associativity_law()
    {
        // m->flatMap(f)->flatMap(g) === m->flatMap(x => f(x)->flatMap(g))
        $spec = $this;
        $this->forAll(
            new IntGen()
        )->then(function ($x) use ($spec) {
            $m = Function1(fn($n) => $n + 1);
            $f = fn($a) => Function1(fn($b) => $a * 2);
            $g = fn($a) => Function1(fn($b) => $a + 3);

            $left = $m->flatMap($f)->flatMap($g);
            $right = $m->flatMap(fn($a) => $f($a)->flatMap($g));

            $this->assertEquals($left($x), $right($x));
        });
    }

    // =========================================================================
    // PROFUNCTOR OPERATIONS
    // =========================================================================

    /**
     * @test
     */
    public function it_maps_over_input_with_lmap()
    {
        // lmap preprocesses input (contravariant)
        $double = Function1(fn(int $x): int => $x * 2);

        $doubleString = $double->lmap(fn(string $s) => (int)$s);

        $this->assertEquals(84, $doubleString("42"));
    }

    /**
     * @test
     */
    public function it_maps_over_output_with_rmap()
    {
        // rmap postprocesses output (covariant)
        $double = Function1(fn(int $x): int => $x * 2);

        $doubleToString = $double->rmap(fn(int $x) => (string)$x);

        $this->assertEquals("84", $doubleToString(42));
    }

    /**
     * @test
     */
    public function it_maps_over_both_with_dimap()
    {
        // dimap does both pre and post processing
        $double = Function1(fn(int $x): int => $x * 2);

        $stringDoubleString = $double->dimap(
            fn(string $s) => (int)$s,    // preprocess input
            fn(int $x) => (string)$x     // postprocess output
        );

        $this->assertEquals("84", $stringDoubleString("42"));
    }

    /**
     * @test
     */
    public function profunctor_identity_law()
    {
        // dimap(id, id) === id
        $spec = $this;
        $this->forAll(
            new IntGen()
        )->then(function ($x) use ($spec) {
            $f = Function1(fn($n) => $n * 2);
            $id = fn($n) => $n;

            $this->assertEquals(
                $f($x),
                $f->dimap($id, $id)($x)
            );
        });
    }

    /**
     * @test
     */
    public function profunctor_composition_law()
    {
        // dimap(f . g, h . i) === dimap(g, h)->dimap(f, i)
        // Note: For contravariant (input), composition is reversed
        $spec = $this;
        $this->forAll(
            new IntGen()
        )->then(function ($x) use ($spec) {
            $base = Function1(fn($n) => $n * 2);

            // Contravariant (left/input) - order reversed in composition
            $f = fn($n) => $n + 1;
            $g = fn($n) => $n - 1;
            // Covariant (right/output) - normal order
            $h = fn($n) => $n * 3;
            $i = fn($n) => $n + 5;

            // Left side: apply both transformations at once
            $left = $base->dimap(
                fn($n) => $g($f($n)),  // f then g (contravariant)
                fn($n) => $h($i($n))   // i then h (covariant)
            );

            // Right side: apply in sequence
            $right = $base->dimap($f, $i)->dimap($g, $h);

            $this->assertEquals($left($x), $right($x));
        });
    }

    /**
     * @test
     */
    public function lmap_equals_dimap_with_identity_on_output()
    {
        $spec = $this;
        $this->forAll(
            new IntGen()
        )->then(function ($x) use ($spec) {
            $f = Function1(fn($n) => $n * 2);
            $g = fn($n) => $n + 3;
            $id = fn($n) => $n;

            $this->assertEquals(
                $f->lmap($g)($x),
                $f->dimap($g, $id)($x)
            );
        });
    }

    /**
     * @test
     */
    public function rmap_equals_dimap_with_identity_on_input()
    {
        $spec = $this;
        $this->forAll(
            new IntGen()
        )->then(function ($x) use ($spec) {
            $f = Function1(fn($n) => $n * 2);
            $g = fn($n) => $n + 3;
            $id = fn($n) => $n;

            $this->assertEquals(
                $f->rmap($g)($x),
                $f->dimap($id, $g)($x)
            );
        });
    }

    // =========================================================================
    // COMPOSITION OPERATIONS
    // =========================================================================

    /**
     * @test
     */
    public function it_composes_with_andThen()
    {
        $add1 = Function1(fn($x) => $x + 1);
        $double = Function1(fn($x) => $x * 2);

        $composed = $add1->andThen($double);

        // (5 + 1) * 2 = 12
        $this->assertEquals(12, $composed(5));
    }

    /**
     * @test
     */
    public function it_composes_with_compose()
    {
        $double = Function1(fn($x) => $x * 2);
        $add1 = Function1(fn($x) => $x + 1);

        $composed = $double->compose($add1);

        // (5 + 1) * 2 = 12
        $this->assertEquals(12, $composed(5));
    }

    /**
     * @test
     */
    public function andThen_and_compose_are_inverses()
    {
        $spec = $this;
        $this->forAll(
            new IntGen()
        )->then(function ($x) use ($spec) {
            $f = Function1(fn($n) => $n + 1);
            $g = Function1(fn($n) => $n * 2);

            // f->andThen(g) === g->compose(f)
            $this->assertEquals(
                $f->andThen($g)($x),
                $g->compose($f)($x)
            );
        });
    }

    /**
     * @test
     */
    public function it_provides_identity_function()
    {
        $id = Function1::identity();

        $this->assertEquals(42, $id(42));
        $this->assertEquals("test", $id("test"));
        $this->assertEquals([1, 2], $id([1, 2]));
    }

    /**
     * @test
     */
    public function identity_is_left_identity_for_composition()
    {
        $spec = $this;
        $this->forAll(
            new IntGen()
        )->then(function ($x) use ($spec) {
            $f = Function1(fn($n) => $n * 2);
            $id = Function1::identity();

            // id . f === f
            $this->assertEquals($f($x), $id->compose($f)($x));
        });
    }

    /**
     * @test
     */
    public function identity_is_right_identity_for_composition()
    {
        $spec = $this;
        $this->forAll(
            new IntGen()
        )->then(function ($x) use ($spec) {
            $f = Function1(fn($n) => $n * 2);
            $id = Function1::identity();

            // f . id === f
            $this->assertEquals($f($x), $f->compose($id)($x));
        });
    }

    /**
     * @test
     */
    public function it_has_combine_as_alias_for_compose()
    {
        $spec = $this;
        $this->forAll(
            new IntGen()
        )->then(function ($x) use ($spec) {
            $f = Function1(fn($n) => $n * 2);
            $g = Function1(fn($n) => $n + 1);

            $this->assertEquals(
                $f->compose($g)($x),
                $f->combine($g)($x)
            );
        });
    }

    // =========================================================================
    // MEMOIZATION
    // =========================================================================

    /**
     * @test
     */
    public function it_memoizes_function_results()
    {
        $callCount = 0;
        $expensive = Function1(function($x) use (&$callCount) {
            $callCount++;
            return $x * $x;
        });

        $memoized = $expensive->memoize();

        // First call - should execute
        $this->assertEquals(25, $memoized(5));
        $this->assertEquals(1, $callCount);

        // Second call with same input - should use cache
        $this->assertEquals(25, $memoized(5));
        $this->assertEquals(1, $callCount, "Function should not be called again");

        // Different input - should execute
        $this->assertEquals(36, $memoized(6));
        $this->assertEquals(2, $callCount);

        // Previous input - should use cache
        $this->assertEquals(25, $memoized(5));
        $this->assertEquals(2, $callCount, "Function should still use cache");
    }

    /**
     * @test
     */
    public function memoized_function_is_still_a_function1()
    {
        $f = Function1(fn($x) => $x * 2);
        $memoized = $f->memoize();

        $this->assertInstanceOf(Function1::class, $memoized);
    }

    /**
     * @test
     */
    public function memoization_works_with_string_keys()
    {
        $callCount = 0;
        $f = Function1(function($s) use (&$callCount) {
            $callCount++;
            return strtoupper($s);
        });

        $memoized = $f->memoize();

        $this->assertEquals("HELLO", $memoized("hello"));
        $this->assertEquals(1, $callCount);

        $this->assertEquals("HELLO", $memoized("hello"));
        $this->assertEquals(1, $callCount);
    }

    // =========================================================================
    // TYPE SAFETY
    // =========================================================================

    /**
     * @test
     */
    public function it_requires_exactly_one_parameter()
    {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage("Function1 takes a callable with 1 parameter");

        Function1(fn($x, $y) => $x + $y);
    }

    /**
     * @test
     */
    public function it_rejects_zero_parameter_functions()
    {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage("Function1 takes a callable with 1 parameter");

        Function1(fn() => 42);
    }

    // =========================================================================
    // HELPER METHODS
    // =========================================================================

    /**
     * @test
     */
    public function it_has_zero_returning_identity()
    {
        $zero = $this->f->zero();
        $this->assertInstanceOf(Function1::class, $zero);

        // zero should be identity
        $this->assertEquals(42, $zero(42));
    }

    /**
     * @test
     */
    public function it_reports_correct_type_arity()
    {
        $this->assertEquals(2, $this->f->getTypeArity());
    }

    /**
     * @test
     */
    public function it_returns_type_variables()
    {
        $f = Function1(fn(int $x): string => (string)$x);
        $vars = $f->getTypeVariables();

        $this->assertIsArray($vars);
        $this->assertCount(2, $vars);
    }
}
