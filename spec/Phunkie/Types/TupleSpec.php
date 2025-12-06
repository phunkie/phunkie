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

use Phunkie\Cats\Functor;
use Phunkie\Cats\Foldable;
use Md\Unit\TestCase;
use Md\PropertyTesting\TestTrait;
use Eris\Generator\IntegerGenerator as IntGen;
use function Phunkie\Functions\function1\compose;
use function Phunkie\Functions\functor\allAs;
use function Phunkie\Functions\functor\asVoid;
use function Phunkie\Functions\functor\fmap;
use function Phunkie\Functions\functor\zipWith;
use function Phunkie\Functions\tuple\assign;
use const Phunkie\Functions\numbers\increment;

class TupleSpec extends TestCase
{
    use TestTrait;

    /**
     * @test
     */
    public function it_lets_you_assign_the_return_values()
    {
        $name = $gender = $age = null;
        (compose(assign($name, $gender, $age)))(Tuple("Luigi", "male", 23));
        $this->assertEquals($name, "Luigi");
        $this->assertEquals($gender, "male");
        $this->assertEquals($age, 23);

        $name = $age = null;
        (compose(assign($name, $age)))(Pair("Luigi", 23));
        $this->assertEquals($name, "Luigi");
        $this->assertEquals($age, 23);
    }

    /**
     * @test
     */
    public function it_is_a_functor()
    {
        $tuple = Tuple(1, 2, 3);
        $this->assertInstanceOf(Functor::class, $tuple);

        $this->assertIsLike($tuple->map(increment), Tuple(2, 3, 4));
        $this->assertIsLike(fmap(increment, Tuple(1, 2, 3)), Tuple(2, 3, 4));

        $this->assertIsLike($tuple->as(0), Tuple(0, 0, 0));
        $this->assertIsLike(allAs(0, Tuple(1, 2, 3)), Tuple(0, 0, 0));

        $this->assertIsLike($tuple->void(), Tuple(Unit(), Unit(), Unit()));
        $this->assertIsLike(asVoid(Tuple(1, 2, 3)), Tuple(Unit(), Unit(), Unit()));

        $this->assertIsLike(
            $tuple->zipWith(fn ($x) => 2 * $x),
            Tuple(Pair(1, 2), Pair(2, 4), Pair(3, 6))
        );
        $this->assertIsLike(
            zipWith(fn ($x) => 2 * $x, Tuple(1, 2, 3)),
            Tuple(Pair(1, 2), Pair(2, 4), Pair(3, 6))
        );
    }

    /**
     * @test
     */
    public function it_is_foldable()
    {
        $tuple = Tuple(1, 2, 3, 4);
        $this->assertInstanceOf(Foldable::class, $tuple);
    }

    /**
     * @test
     */
    public function it_supports_fold_left()
    {
        $tuple = Tuple(1, 2, 3, 4);
        $add = fn($acc, $x) => $acc + $x;
        $result = ($tuple->foldLeft(0))($add);

        $this->assertEquals(10, $result);
    }

    /**
     * @test
     */
    public function it_supports_fold_right()
    {
        $tuple = Tuple(1, 2, 3, 4);
        $cons = fn($x, $acc) => [$x, ...$acc];
        $result = ($tuple->foldRight([]))($cons);

        $this->assertEquals([1, 2, 3, 4], $result);
    }

    /**
     * @test
     */
    public function it_supports_fold_map()
    {
        $tuple = Tuple(1, 2, 3);
        $result = $tuple->foldMap(fn($x) => [$x]);

        $this->assertEquals([1, 2, 3], $result);
    }

    /**
     * @test
     */
    public function it_converts_to_immlist()
    {
        $tuple = Tuple(1, 2, 3);
        $list = $tuple->toImmList();

        $this->assertIsLike($list, ImmList(1, 2, 3));
    }

    /**
     * @test
     */
    public function it_supports_get_by_index()
    {
        $tuple = Tuple("a", "b", "c");
        $this->assertEquals("a", $tuple->get(1));
        $this->assertEquals("b", $tuple->get(2));
        $this->assertEquals("c", $tuple->get(3));
    }

    /**
     * @test
     */
    public function get_throws_on_invalid_index()
    {
        $tuple = Tuple("a", "b", "c");

        $this->expectException(\OutOfBoundsException::class);
        $tuple->get(4);
    }

    /**
     * @test
     */
    public function it_supports_map_indexed()
    {
        $tuple = Tuple(1, 2, 3);
        $result = $tuple->mapIndexed(fn($x, $i) => $x * $i);

        $this->assertIsLike($result, Tuple(1, 4, 9));
    }

    /**
     * @test
     */
    public function it_supports_foreach_indexed()
    {
        $tuple = Tuple("a", "b", "c");
        $result = [];
        $tuple->forEachIndexed(function($x, $i) use (&$result) {
            $result[] = "$i:$x";
        });

        $this->assertEquals(["1:a", "2:b", "3:c"], $result);
    }

    /**
     * @test
     */
    public function it_supports_forall()
    {
        $tuple = Tuple(2, 4, 6, 8);
        $this->assertTrue($tuple->forall(fn($x) => $x % 2 === 0));

        $tuple = Tuple(2, 3, 4);
        $this->assertFalse($tuple->forall(fn($x) => $x % 2 === 0));
    }

    /**
     * @test
     */
    public function it_supports_exists()
    {
        $tuple = Tuple(1, 2, 3, 4);
        $this->assertTrue($tuple->exists(fn($x) => $x > 3));

        $tuple = Tuple(1, 2, 3);
        $this->assertFalse($tuple->exists(fn($x) => $x > 5));
    }

    /**
     * @test
     */
    public function it_supports_contains()
    {
        $tuple = Tuple("a", 42, true);
        $this->assertTrue($tuple->contains(42));
        $this->assertFalse($tuple->contains("42"));
        $this->assertFalse($tuple->contains(false));
    }

    /**
     * @test
     */
    public function it_supports_find()
    {
        $tuple = Tuple(1, 2, 3, 4, 5);
        $result = $tuple->find(fn($x) => $x > 3);

        $this->assertIsLike($result, Some(4));

        $result = $tuple->find(fn($x) => $x > 10);
        $this->assertIsLike($result, None());
    }

    /**
     * @test
     */
    public function it_supports_head()
    {
        $tuple = Tuple("a", "b", "c");
        $this->assertEquals("a", $tuple->head());
    }

    /**
     * @test
     */
    public function it_supports_last()
    {
        $tuple = Tuple("a", "b", "c");
        $this->assertEquals("c", $tuple->last());
    }

    /**
     * @test
     */
    public function it_supports_tail()
    {
        $tuple = Tuple("a", "b", "c");
        $tail = $tuple->tail();

        $this->assertIsLike($tail, Tuple("b", "c"));
    }

    /**
     * @test
     */
    public function tail_throws_on_single_element()
    {
        $tuple = Tuple("a");
        $this->expectException(\RuntimeException::class);
        $tuple->tail();
    }

    /**
     * @test
     */
    public function it_supports_init()
    {
        $tuple = Tuple("a", "b", "c");
        $init = $tuple->init();

        $this->assertIsLike($init, Tuple("a", "b"));
    }

    /**
     * @test
     */
    public function init_throws_on_single_element()
    {
        $tuple = Tuple("a");
        $this->expectException(\RuntimeException::class);
        $tuple->init();
    }

    /**
     * @test
     */
    public function it_supports_take()
    {
        $tuple = Tuple("a", "b", "c", "d");
        $result = $tuple->take(2);

        $this->assertIsLike($result, Tuple("a", "b"));
    }

    /**
     * @test
     */
    public function take_throws_on_invalid_n()
    {
        $tuple = Tuple("a", "b", "c");

        $this->expectException(\InvalidArgumentException::class);
        $tuple->take(0);
    }

    /**
     * @test
     */
    public function it_supports_drop()
    {
        $tuple = Tuple("a", "b", "c", "d");
        $result = $tuple->drop(2);

        $this->assertIsLike($result, Tuple("c", "d"));
    }

    /**
     * @test
     */
    public function drop_throws_when_dropping_all()
    {
        $tuple = Tuple("a", "b", "c");

        $this->expectException(\InvalidArgumentException::class);
        $tuple->drop(3);
    }

    /**
     * @test
     */
    public function it_converts_to_array()
    {
        $tuple = Tuple(1, 2, 3);
        $this->assertEquals([1, 2, 3], $tuple->toArray());
    }

    /**
     * @test
     */
    public function it_gets_arity()
    {
        $tuple = Tuple(1, 2, 3);
        $this->assertEquals(3, $tuple->getArity());

        $pair = Pair("a", "b");
        $this->assertEquals(2, $pair->getArity());
    }

    /**
     * @test
     */
    public function it_supports_copy()
    {
        $original = Tuple("a", 1, true);
        $copied = $original->copy(["_2" => 2]);

        $this->assertEquals("a", $original->_1);
        $this->assertEquals(1, $original->_2);
        $this->assertEquals(true, $original->_3);

        $this->assertEquals("a", $copied->_1);
        $this->assertEquals(2, $copied->_2);
        $this->assertEquals(true, $copied->_3);
    }

    /**
     * @test
     */
    public function it_is_immutable()
    {
        $tuple = Tuple("a", 1);
        $this->expectException(\TypeError::class);
        $tuple->_1 = "b";
    }

    /**
     * @test
     */
    public function functor_identity_law()
    {
        $this->forAll(
            new IntGen(),
            new IntGen(),
            new IntGen()
        )->then(function($a, $b, $c) {
            $tuple = Tuple($a, $b, $c);
            $identity = fn($x) => $x;
            $this->assertIsLike($tuple->map($identity), $tuple);
        });
    }

    /**
     * @test
     */
    public function functor_composition_law()
    {
        $this->forAll(
            new IntGen(),
            new IntGen(),
            new IntGen()
        )->then(function($a, $b, $c) {
            $tuple = Tuple($a, $b, $c);
            $f = fn($x) => $x * 2;
            $g = fn($x) => $x + 1;

            $composed = $tuple->map(fn($x) => $g($f($x)));
            $separated = $tuple->map($f)->map($g);

            $this->assertIsLike($composed, $separated);
        });
    }

    /**
     * @test
     */
    public function it_shows_correctly()
    {
        $tuple = Tuple("name", 25, true);
        $this->assertEquals('("name", 25, true)', $tuple->toString());
    }

    /**
     * @test
     */
    public function it_shows_type_correctly()
    {
        $tuple = Tuple("hello", 42, true);
        $this->assertEquals('(String, Int, Boolean)', $tuple->showType());
    }
}
