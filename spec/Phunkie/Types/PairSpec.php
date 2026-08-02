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
use Eris\Generator\StringGenerator as StrGen;
use Phunkie\Cats\Bifunctor;
use Phunkie\Cats\Comonad;
use Phunkie\Cats\Foldable;
use Phunkie\Cats\Functor;
use Phunkie\Utils\Copiable;
use PHPUnit\Framework\Attributes\Test;
use const Phunkie\Functions\numbers\increment;

class PairSpec extends TestCase
{
    use TestTrait;

    private $pair;

    public function setUp(): void
    {
        $this->pair = Pair("a", 1);
    }

    #[Test]
    public function it_is_copiable()
    {
        $this->assertInstanceOf(Copiable::class, $this->pair);
    }

    #[Test]
    public function it_implements_bifunctor()
    {
        $this->assertInstanceOf(Bifunctor::class, $this->pair);
    }

    #[Test]
    public function it_implements_functor()
    {
        $this->assertInstanceOf(Functor::class, $this->pair);
    }

    #[Test]
    public function it_implements_foldable()
    {
        $this->assertInstanceOf(Foldable::class, $this->pair);
    }

    #[Test]
    public function it_implements_comonad()
    {
        $this->assertInstanceOf(Comonad::class, $this->pair);
    }

    #[Test]
    public function it_can_access_elements()
    {
        $pair = Pair("hello", 42);
        $this->assertEquals("hello", $pair->_1);
        $this->assertEquals(42, $pair->_2);
    }

    #[Test]
    public function it_is_immutable()
    {
        $this->expectException(\TypeError::class);
        $this->pair->_1 = "b";
    }

    #[Test]
    public function it_supports_copy()
    {
        $original = Pair("a", 1);
        $copied = $original->copy(["_1" => "b"]);

        $this->assertEquals("a", $original->_1);
        $this->assertEquals(1, $original->_2);
        $this->assertEquals("b", $copied->_1);
        $this->assertEquals(1, $copied->_2);
    }

    // Bifunctor Operations Tests

    #[Test]
    public function it_supports_bimap()
    {
        $pair = Pair("hello", 42);
        $result = $pair->bimap(
            fn($s) => strtoupper($s),
            fn($n) => $n * 2
        );

        $this->assertIsLike($result, Pair("HELLO", 84));
    }

    #[Test]
    public function it_supports_first()
    {
        $pair = Pair("hello", 42);
        $result = $pair->first(fn($s) => strtoupper($s));

        $this->assertIsLike($result, Pair("HELLO", 42));
    }

    #[Test]
    public function it_supports_second()
    {
        $pair = Pair("hello", 42);
        $result = $pair->second(fn($n) => $n * 2);

        $this->assertIsLike($result, Pair("hello", 84));
    }

    #[Test]
    public function bifunctor_identity_law()
    {
        $this->forAll(
            new StrGen(),
            new IntGen()
        )->then(function($s, $n) {
            $pair = Pair($s, $n);
            $identity = fn($x) => $x;
            $this->assertIsLike(
                $pair->bimap($identity, $identity),
                $pair
            );
        });
    }

    #[Test]
    public function bifunctor_composition_law()
    {
        $this->forAll(
            new StrGen(),
            new IntGen()
        )->then(function($s, $n) {
            $pair = Pair($s, $n);
            $f1 = fn($x) => strtoupper($x);
            $f2 = fn($x) => $x . "!";
            $g1 = fn($x) => $x * 2;
            $g2 = fn($x) => $x + 1;

            $composed = $pair->bimap(
                fn($x) => $f2($f1($x)),
                fn($x) => $g2($g1($x))
            );
            $separated = $pair->bimap($f1, $g1)->bimap($f2, $g2);

            $this->assertIsLike($composed, $separated);
        });
    }

    // Functor Operations Tests

    #[Test]
    public function it_supports_map_on_second_element()
    {
        $pair = Pair("label", 42);
        $result = $pair->map(fn($n) => $n * 2);

        $this->assertIsLike($result, Pair("label", 84));
    }

    #[Test]
    public function functor_identity_law()
    {
        $this->forAll(
            new StrGen(),
            new IntGen()
        )->then(function($s, $n) {
            $pair = Pair($s, $n);
            $identity = fn($x) => $x;
            $this->assertIsLike($pair->map($identity), $pair);
        });
    }

    #[Test]
    public function functor_composition_law()
    {
        $this->forAll(
            new StrGen(),
            new IntGen()
        )->then(function($s, $n) {
            $pair = Pair($s, $n);
            $f = fn($x) => $x * 2;
            $g = fn($x) => $x + 1;

            $composed = $pair->map(fn($x) => $g($f($x)));
            $separated = $pair->map($f)->map($g);

            $this->assertIsLike($composed, $separated);
        });
    }

    // Foldable Operations Tests

    #[Test]
    public function it_supports_fold_left()
    {
        $pair = Pair("label", 42);
        $result = ($pair->foldLeft(10))(fn($acc, $x) => $acc + $x);

        $this->assertEquals(52, $result);
    }

    #[Test]
    public function it_supports_fold_right()
    {
        $pair = Pair("label", 42);
        $result = ($pair->foldRight([]))(fn($x, $acc) => [$x, ...$acc]);

        $this->assertEquals([42], $result);
    }

    #[Test]
    public function it_supports_fold_map()
    {
        $pair = Pair("label", 5);
        $result = $pair->foldMap(fn($x) => [$x]);

        $this->assertEquals([5], $result);
    }

    #[Test]
    public function it_converts_to_list()
    {
        $pair = Pair("label", 42);
        $list = $pair->toList();

        $this->assertIsLike($list, ImmList(42));
    }

    // Comonad Operations Tests

    #[Test]
    public function it_extracts_second_element()
    {
        $pair = Pair("context", 42);
        $this->assertEquals(42, $pair->extract());
    }

    #[Test]
    public function it_supports_duplicate()
    {
        $pair = Pair("x", 42);
        $result = $pair->duplicate();

        $this->assertEquals("x", $result->_1);
        $this->assertIsLike($result->_2, Pair("x", 42));
    }

    #[Test]
    public function it_supports_extend()
    {
        $pair = Pair("prefix: ", 42);
        $result = $pair->extend(fn($p) => $p->_1 . $p->extract());

        $this->assertIsLike($result, Pair("prefix: ", "prefix: 42"));
    }

    #[Test]
    public function it_supports_coflatmap_alias()
    {
        $pair = Pair(10, 5);
        $result = $pair->coflatMap(fn($p) => $p->_1 + $p->_2);

        $this->assertIsLike($result, Pair(10, 15));
    }

    #[Test]
    public function comonad_left_identity_law()
    {
        $this->forAll(
            new StrGen(),
            new IntGen()
        )->then(function($s, $n) {
            $pair = Pair($s, $n);
            $this->assertIsLike(
                $pair->duplicate()->extract(),
                $pair
            );
        });
    }

    #[Test]
    public function comonad_right_identity_law()
    {
        $this->forAll(
            new StrGen(),
            new IntGen()
        )->then(function($s, $n) {
            $pair = Pair($s, $n);
            $duplicated = $pair->duplicate();
            $mapped = $duplicated->map(fn($p) => $p->extract());

            $this->assertIsLike($mapped, $pair);
        });
    }

    // Utility Operations Tests

    #[Test]
    public function it_supports_swap()
    {
        $pair = Pair("name", 42);
        $swapped = $pair->swap();

        $this->assertIsLike($swapped, Pair(42, "name"));
    }

    #[Test]
    public function swap_is_involution()
    {
        $this->forAll(
            new StrGen(),
            new IntGen()
        )->then(function($s, $n) {
            $pair = Pair($s, $n);
            $this->assertIsLike($pair->swap()->swap(), $pair);
        });
    }

    #[Test]
    public function it_supports_merge()
    {
        $pair = Pair(10, 32);
        $result = $pair->merge(fn($a, $b) => $a + $b);

        $this->assertEquals(42, $result);
    }

    #[Test]
    public function it_supports_map_both()
    {
        $pair = Pair(3, 4);
        $result = $pair->mapBoth(fn($x) => $x * $x);

        $this->assertIsLike($result, Pair(9, 16));
    }

    #[Test]
    public function it_supports_fst_accessor()
    {
        $pair = Pair("name", 42);
        $this->assertEquals("name", $pair->fst());
    }

    #[Test]
    public function it_supports_snd_accessor()
    {
        $pair = Pair("name", 42);
        $this->assertEquals(42, $pair->snd());
    }

    #[Test]
    public function it_supports_foreach()
    {
        $pair = Pair("user", 42);
        $result = [];
        $pair->forEach(function($k, $v) use (&$result) {
            $result = [$k, $v];
        });

        $this->assertEquals(["user", 42], $result);
    }

    #[Test]
    public function it_supports_forall_both()
    {
        $pair = Pair(10, 20);
        $this->assertTrue($pair->forallBoth(
            fn($x) => $x > 5,
            fn($y) => $y > 15
        ));

        $this->assertFalse($pair->forallBoth(
            fn($x) => $x > 15,
            fn($y) => $y > 15
        ));
    }

    #[Test]
    public function it_supports_exists_both()
    {
        $pair = Pair(10, 5);
        $this->assertTrue($pair->existsBoth(
            fn($x) => $x > 15,
            fn($y) => $y > 3
        ));

        $this->assertFalse($pair->existsBoth(
            fn($x) => $x > 15,
            fn($y) => $y > 10
        ));
    }

    #[Test]
    public function it_shows_correctly()
    {
        $pair = Pair("name", 25);
        $this->assertEquals('Pair("name", 25)', $pair->toString());
    }

    #[Test]
    public function it_shows_type_correctly()
    {
        $pair = Pair("hello", 42);
        $this->assertEquals('(String, Int)', $pair->showType());
    }
}
