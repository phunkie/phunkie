<?php

namespace spec\Phunkie\Types;

use Md\Unit\TestCase;
use Phunkie\Cats\Functor;
use Phunkie\Types\Pair;
use function Phunkie\Functions\functor\allAs;
use function Phunkie\Functions\functor\asVoid;
use function Phunkie\Functions\functor\fmap;
use function Phunkie\Functions\functor\zipWith;
use function Phunkie\Functions\show\show;

/**
 * @mixin \Phunkie\Types\ImmMap
 */
class ImmMapSpec extends TestCase
{
    /**
     * @test
     */
    public function it_can_be_created_with_associative_arrays()
    {
        $map = ImmMap(["hello" => "there"]);
        $this->assertIsLike($map->get("hello"), Some("there"));
    }

    /**
     * @test
     */
    public function it_returns_None_when_key_does_not_exist()
    {
        $map = ImmMap(["hello" => "there"]);
        $this->assertIsLike($map->get("hi"), None());
    }

    /**
     * @test
     */
    public function it_can_be_constructed_with_an_even_number_of_objects()
    {
        $map = ImmMap(
            new AccountNumber(1),
            new Account("John smith"),
            new AccountNumber(2),
            new Account("Chuck Norris")
        );

        $this->assertIsLike(
            $map->get(new AccountNumber(2)),
            Some(new Account("Chuck Norris"))
        );
    }

    /**
     * @test
     */
    public function it_cannot_be_constructed_with_an_odd_number()
    {
        $this->expectException(\Error::class);

        $map = ImmMap(
            new AccountNumber(1),
            new Account("John smith"),
            new AccountNumber(2),
            new Account("Chuck Norris"),
            new AccountNumber(3)
        );
    }

    /**
     * @test
     */
    public function it_also_complains_on_one_argument_if_it_is_not_an_array()
    {
        $this->expectException(\Error::class);

        $map = ImmMap(new AccountNumber(1));
    }

    /**
     * @test
     */
    public function it_lets_you_check_if_a_key_exists()
    {
        $map = ImmMap(["hello" => "there"]);
        $this->assertTrue($map->contains("hello"));
        $this->assertFalse($map->contains("hi"));
    }

    /**
     * @test
     */
    public function it_lets_you_set_a_default_value()
    {
        $map = ImmMap(["hello" => "there"]);
        $this->assertEquals("here", $map->getOrElse("hi", "here"));
        $this->assertEquals("there", $map->getOrElse("hello", "here"));
    }

    /**
     * @test
     */
    public function it_is_showable()
    {
        $map = ImmMap(["hi" => "here", "hello" => "there"]);
        $this->assertEquals($map->toString(), 'Map("hi" -> "here", "hello" -> "there")');
    }

    /**
     * @test
     */
    public function it_has_minus()
    {
        $map = ImmMap(["hello" => "there", "hi" => "here"]);
        $this->assertTrue(
            $map->minus("hi")->eqv(ImmMap(["hello" => "there"]))
        );
    }

    /**
     * @test
     */
    public function it_has_plus()
    {
        $map = ImmMap(["hello" => "there"]);
        $this->assertTrue(
            $map
                ->plus("hi", "here")
                ->eqv(ImmMap(["hello" => "there", "hi" => "here"]))
        );
    }

    /**
     * @test
     */
    public function it_replaces_value_when_adding_with_same_key()
    {
        $map = ImmMap(["hello" => "there", "hi" => "here"]);
        $this->assertTrue(
            $map
                ->plus("hi", "nowhere")
                ->eqv(ImmMap(["hello" => "there", "hi" => "nowhere"]))
        );
    }

    /**
     * @test
     */
    public function it_can_be_copied()
    {
        $map = ImmMap(["hello" => "there", "hi" => "here"]);
        $this->assertEquals($map->copy()->show(), $map->show());
    }

    /**
     * @test
     */
    public function it_is_a_functor()
    {
        $f = ImmMap(["a" => 1, "b" => 2, "c" => 3]);
        $increment = fn (Pair $keyValue) => Pair($keyValue->_1, $keyValue->_2 + 1);

        $this->assertTrue(
            $f->map($increment)->eqv(ImmMap(["a" => 2, "b" => 3, "c" => 4]))
        );
        $this->assertTrue(
            fmap(
                $increment,
                ImmMap(["a" => 1, "b" => 2, "c" => 3])
            )
                    ->eqv(
                        ImmMap(["a" => 2, "b" => 3, "c" => 4])
                    )
        );

        $this->assertTrue($f->as(Pair("a", 0))->eqv(ImmMap(["a" => 0])));
        $this->assertTrue(
            allAs(Pair("a", 0), ImmMap(["a" => 1, "b" => 2, "c" => 3]))
                ->eqv(ImmMap(["a" => 0]))
        );

        $this->assertTrue(
            $f->as(Pair(_, 0))->eqv(ImmMap(["a" => 0, "b" => 0, "c" => 0]))
        );
        $this->assertTrue(
            allAs(Pair(_, 0), ImmMap(["a" => 1, "b" => 2, "c" => 3]))
                ->eqv(ImmMap(["a" => 0, "b" => 0, "c" => 0]))
        );

        $this->assertTrue(
            $f->void()->eqv(ImmMap(["a" => Unit(), "b" => Unit(), "c" => Unit()]))
        );
        $this->assertTrue(
            asVoid(
                ImmMap(["a" => 1, "b" => 2, "c" => 3])
            )
                    ->eqv(
                        ImmMap(["a" => Unit(), "b" => Unit(), "c" => Unit()])
                    )
        );

        $this->assertTrue(
            $f
                ->zipWith(fn ($x) => 2 * $x)
                ->eqv(
                    ImmMap(
                        [
                            "a" => Pair(1, 2),
                            "b" => Pair(2, 4),
                            "c" => Pair(3, 6)
                        ]
                    )
                )
        );
        $this->assertTrue(
            zipWith(
                fn ($x) => 2 * $x,
                ImmMap(["a" => 1, "b" => 2, "c" => 3])
            )->eqv(
                ImmMap(
                    [
                        "a" => Pair(1, 2),
                        "b" => Pair(2, 4),
                        "c" => Pair(3, 6)
                    ]
                )
            )
        );
    }

    /**
     * @test
     */
    public function it_can_map_values()
    {
        $map = ImmMap(["a" => 1, "b" => 2, "c" => 3]);
        $result = $map->mapValues(fn($v) => $v * 2);
        $this->assertTrue($result->eqv(ImmMap(["a" => 2, "b" => 4, "c" => 6])));
    }

    /**
     * @test
     */
    public function it_can_map_keys()
    {
        $map = ImmMap([1 => "a", 2 => "b", 3 => "c"]);
        $result = $map->mapKeys(fn($k) => "key_" . $k);
        $this->assertTrue($result->eqv(ImmMap(["key_1" => "a", "key_2" => "b", "key_3" => "c"])));
    }

    /**
     * @test
     */
    public function it_is_foldable()
    {
        $map = ImmMap(["a" => 1, "b" => 2, "c" => 3]);

        // foldLeft
        $sum = $map->foldLeft(0)(fn($acc, $pair) => $acc + $pair->_2);
        $this->assertEquals(6, $sum);

        // foldRight
        $sum2 = $map->foldRight(0)(fn($pair, $acc) => $acc + $pair->_2);
        $this->assertEquals(6, $sum2);

        // fold
        $sum3 = $map->fold(0)(fn($acc, $pair) => $acc + $pair->_2);
        $this->assertEquals(6, $sum3);
    }

    /**
     * @test
     */
    public function it_can_foldMap()
    {
        $map = ImmMap(["a" => 1, "b" => 2, "c" => 3]);
        $result = $map->foldMap(fn($pair) => ImmList($pair->_2));
        $this->assertTrue($result->eqv(ImmList(1, 2, 3)));
    }

    /**
     * @test
     */
    public function it_can_filter()
    {
        $map = ImmMap(["a" => 1, "b" => 2, "c" => 3, "d" => 4]);
        $result = $map->filter(fn($pair) => $pair->_2 % 2 === 0);
        $this->assertTrue($result->eqv(ImmMap(["b" => 2, "d" => 4])));
    }

    /**
     * @test
     */
    public function it_can_filter_keys()
    {
        $map = ImmMap(["a" => 1, "b" => 2, "c" => 3]);
        $result = $map->filterKeys(fn($k) => $k !== "b");
        $this->assertTrue($result->eqv(ImmMap(["a" => 1, "c" => 3])));
    }

    /**
     * @test
     */
    public function it_can_filter_values()
    {
        $map = ImmMap(["a" => 1, "b" => 2, "c" => 3, "d" => 4]);
        $result = $map->filterValues(fn($v) => $v > 2);
        $this->assertTrue($result->eqv(ImmMap(["c" => 3, "d" => 4])));
    }

    /**
     * @test
     */
    public function it_can_sequence()
    {
        $map = ImmMap(["a" => Some(1), "b" => Some(2), "c" => Some(3)]);
        $result = $map->sequence();
        $this->assertIsLike($result, Some(ImmMap(["a" => 1, "b" => 2, "c" => 3])));

        $map2 = ImmMap(["a" => Some(1), "b" => None(), "c" => Some(3)]);
        $result2 = $map2->sequence();
        $this->assertIsLike($result2, None());
    }

    /**
     * @test
     */
    public function it_is_a_monoid()
    {
        $map1 = ImmMap(["a" => 1, "b" => 2]);
        $map2 = ImmMap(["b" => 3, "c" => 4]);

        // zero
        $empty = $map1->zero();
        $this->assertTrue($empty->eqv(ImmMap()));

        // combine
        $combined = $map1->combine($map2);
        $this->assertTrue($combined->eqv(ImmMap(["a" => 1, "b" => 3, "c" => 4])));

        // left identity: combine(zero, a) == a
        $this->assertTrue($map1->zero()->combine($map1)->eqv($map1));

        // right identity: combine(a, zero) == a
        $this->assertTrue($map1->combine($map1->zero())->eqv($map1));
    }

    /**
     * @test
     */
    public function it_can_convert_to_list()
    {
        $map = ImmMap(["a" => 1, "b" => 2]);
        $list = $map->toList();
        $this->assertTrue($list->eqv(ImmList(Pair("a", 1), Pair("b", 2))));
    }

    /**
     * @test
     */
    public function it_supports_updated_and_removed()
    {
        $map = ImmMap(["a" => 1, "b" => 2]);

        $updated = $map->updated("c", 3);
        $this->assertTrue($updated->eqv(ImmMap(["a" => 1, "b" => 2, "c" => 3])));

        $removed = $map->removed("a");
        $this->assertTrue($removed->eqv(ImmMap(["b" => 2])));
    }

    /**
     * @test
     */
    public function it_can_map_with_key()
    {
        $map = ImmMap(["a" => 1, "b" => 2]);
        $result = $map->mapWithKey(fn($k, $v) => "$k:$v");
        $this->assertTrue($result->eqv(ImmMap(["a" => "a:1", "b" => "b:2"])));
    }

    /**
     * @test
     */
    public function it_can_flat_map()
    {
        $map = ImmMap(["a" => 1, "b" => 2]);
        $result = $map->flatMap(fn($pair) => ImmMap([
            $pair->_1 . "1" => $pair->_2,
            $pair->_1 . "2" => $pair->_2 * 2
        ]));
        $this->assertTrue(
            $result->eqv(ImmMap(["a1" => 1, "a2" => 2, "b1" => 2, "b2" => 4]))
        );
    }

    /**
     * @test
     */
    public function it_can_zip_maps()
    {
        $map1 = ImmMap(["a" => 1, "b" => 2, "c" => 3]);
        $map2 = ImmMap(["a" => "x", "b" => "y", "d" => "z"]);
        $result = $map1->zip($map2);
        $this->assertTrue(
            $result->eqv(ImmMap(["a" => Pair(1, "x"), "b" => Pair(2, "y")]))
        );
    }

    /**
     * @test
     */
    public function it_can_zip_with_function()
    {
        $map1 = ImmMap(["a" => 1, "b" => 2]);
        $map2 = ImmMap(["a" => 10, "b" => 20]);
        $result = $map1->zipWithMap($map2, fn($v1, $v2) => $v1 + $v2);
        $this->assertTrue($result->eqv(ImmMap(["a" => 11, "b" => 22])));
    }

    /**
     * @test
     */
    public function it_can_partition()
    {
        $map = ImmMap(["a" => 1, "b" => 2, "c" => 3, "d" => 4]);
        $partitioned = $map->partition(fn($pair) => $pair->_2 % 2 === 0);
        $this->assertTrue($partitioned->_1->eqv(ImmMap(["b" => 2, "d" => 4])));
        $this->assertTrue($partitioned->_2->eqv(ImmMap(["a" => 1, "c" => 3])));
    }

    /**
     * @test
     */
    public function it_can_group_by()
    {
        $map = ImmMap(["a" => 1, "b" => 2, "c" => 1, "d" => 3]);
        $grouped = $map->groupBy(fn($pair) => $pair->_2);

        $this->assertTrue(
            $grouped->get(1)->get()->eqv(ImmList(Pair("a", 1), Pair("c", 1)))
        );
        $this->assertTrue(
            $grouped->get(2)->get()->eqv(ImmList(Pair("b", 2)))
        );
        $this->assertTrue(
            $grouped->get(3)->get()->eqv(ImmList(Pair("d", 3)))
        );
    }

    /**
     * @test
     */
    public function it_can_check_if_empty()
    {
        $empty = ImmMap();
        $this->assertTrue($empty->isEmpty());

        $nonEmpty = ImmMap(["a" => 1]);
        $this->assertFalse($nonEmpty->isEmpty());
    }

    /**
     * @test
     */
    public function it_can_get_size()
    {
        $map = ImmMap(["a" => 1, "b" => 2, "c" => 3]);
        $this->assertEquals(3, $map->size());

        $empty = ImmMap();
        $this->assertEquals(0, $empty->size());
    }
}

class AccountNumber
{
    private $number;
    public function __construct(int $number)
    {
        $this->number = $number;
    }
}

class Account
{
    private $name;
    public function __construct(string $name)
    {
        $this->name = $name;
    }
}
