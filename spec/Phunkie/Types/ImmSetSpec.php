<?php

namespace spec\Phunkie\Types;

use Md\Unit\TestCase;
use Phunkie\Cats\Functor;
use Phunkie\Types\ImmSet;
use Md\PropertyTesting\TestTrait;
use Eris\Generator\SequenceGenerator as SeqGen;
use Eris\Generator\IntegerGenerator as IntGen;
use function Phunkie\Functions\applicative\ap;
use function Phunkie\Functions\applicative\pure;
use function Phunkie\Functions\applicative\map2;
use function Phunkie\Functions\functor\allAs;
use function Phunkie\Functions\functor\asVoid;
use function Phunkie\Functions\functor\fmap;
use function Phunkie\Functions\functor\zipWith;
use function Phunkie\Functions\monad\bind;

use function Phunkie\Functions\monad\flatten;
use function Phunkie\Functions\monad\mcompose;
use const Phunkie\Functions\numbers\increment;

class ImmSetSpec extends TestCase
{
    use TestTrait;

    /**
     * @test
     */
    public function it_is_initializable()
    {
        $ref = new \ReflectionClass(ImmSet::class);
        $this->assertEquals($ref->getName(), 'Phunkie\\Types\\ImmSet');
    }

    /**
     * @test
     */
    public function it_does_not_keep_duplicates()
    {
        $set = ImmSet(1, 2, 3, 2);
        $this->assertIsLike($set, ImmSet(1, 2, 3));
    }

    /**
     * @test
     */
    public function it_does_not_keep_object_duplicates()
    {
        $set = ImmSet(Item(1), Item(2), Item(3), Item(2));
        $this->assertIsLike($set, ImmSet(Item(1), Item(2), Item(3)));
    }

    /**
     * @test
     */
    public function it_lets_you_test_if_an_element_is_part_of_the_set()
    {
        $set = ImmSet(1, 2, 3);
        $this->assertTrue($set->contains(3));
        $this->assertFalse($set->contains(42));
    }

    /**
     * @test
     */
    public function it_has_minus()
    {
        $set = ImmSet(1, 2, 3);
        $this->assertIsLike($set->minus(2), ImmSet(1, 3));
        $this->assertIsLike($set->minus(42), ImmSet(1, 2, 3));
    }

    /**
     * @test
     */
    public function it_has_minus_with_objects()
    {
        $set = ImmSet(Item(1), Item(2), Item(3));
        $this->assertIsLike($set->minus(Item(2)), ImmSet(Item(1), Item(3)));
        $this->assertIsLike(
            $set->minus(Item(42)),
            ImmSet(Item(1), Item(2), Item(3))
        );
    }

    /**
     * @test
     */
    public function it_has_plus()
    {
        $set = ImmSet(1, 2, 3);
        $this->assertIsLike($set->plus(2), ImmSet(1, 2, 3));
        $this->assertIsLike($set->plus(42), ImmSet(1, 2, 3, 42));
    }

    /**
     * @test
     */
    public function it_has_plus_with_objects()
    {
        $set = ImmSet(Item(1), Item(2), Item(3));
        $this->assertIsLike(
            $set->plus(Item(2)),
            ImmSet(Item(1), Item(2), Item(3))
        );
        $this->assertIsLike(
            $set->plus(Item(42)),
            ImmSet(Item(1), Item(2), Item(3), Item(42))
        );
    }

    /**
     * @test
     */
    public function it_has_the_union_operation()
    {
        $set = ImmSet(1, 2, 3);
        $this->assertIsLike(
            $set->union(ImmSet(3, 4, 5)),
            ImmSet(1, 2, 3, 4, 5)
        );
    }

    /**
     * @test
     */
    public function it_has_the_intersect_operation()
    {
        $set = ImmSet(1, 2, 3);
        $this->assertIsLike($set->intersect(ImmSet(3, 4, 5)), ImmSet(3));
    }

    /**
     * @test
     */
    public function it_has_the_diff_operation()
    {
        $set = ImmSet(1, 2, 3);
        $this->assertIsLike($set->diff(ImmSet(3, 4, 5)), ImmSet(1, 2, 4, 5));
    }

    /**
     * @test
     */
    public function it_is_a_functor()
    {
        $set = ImmSet(1, 2, 3);
        $this->assertInstanceOf(Functor::class, $set);

        $this->assertIsLike($set->map(increment), ImmSet(2, 3, 4));
        $this->assertIsLike(fmap(increment, ImmSet(1, 2, 3)), ImmSet(2, 3, 4));

        $this->assertIsLike($set->as(0), ImmSet(0, 0, 0));
        $this->assertIsLike(allAs(0, ImmSet(1, 2, 3)), ImmSet(0, 0, 0));

        $this->assertIsLike($set->void(), ImmSet(Unit(), Unit(), Unit()));
        $this->assertIsLike(
            asVoid(ImmSet(1, 2, 3)),
            ImmSet(Unit(), Unit(), Unit())
        );

        $this->assertIsLike(
            $set->zipWith(function ($x) {
                return 2 * $x;
            }),
            ImmSet(Pair(1, 2), Pair(2, 4), Pair(3, 6))
        );
        $this->assertIsLike(
            zipWith(function ($x) {
                return 2 * $x;
            }, ImmSet(1, 2, 3)),
            ImmSet(Pair(1, 2), Pair(2, 4), Pair(3, 6))
        );
    }

    /**
     * @test
     */
    public function it_is_an_applicative()
    {
        $xs = (ap(ImmSet(function ($a) {
            return $a +1;
        })))(ImmSet(1));
        $this->assertIsLike($xs, ImmSet(2));

        $xs = (pure(ImmSet))(42);
        $this->assertIsLike($xs, ImmSet(42));

        $xs = ((map2(function ($x, $y) {
            return $x + $y;
        }))(ImmSet(1)))(ImmSet(2));
        $this->assertIsLike($xs, ImmSet(3));
    }

    /**
     * @test
     */
    public function it_is_a_monad()
    {
        $xs = (bind(function ($a) {
            return ImmSet($a +1);
        }))(ImmSet(1));
        $this->assertIsLike($xs, ImmSet(2));

        $xs = flatten(ImmSet(ImmSet(1)));
        $this->assertIsLike($xs, ImmSet(1));

        $xs = flatten(ImmSet(ImmSet(1), ImmSet(2)));
        $this->assertIsLike($xs, ImmSet(1, 2));

        $xs = ImmSet("h");
        $f = function (string $s) {
            return ImmSet($s . "e");
        };
        $g = function (string $s) {
            return ImmSet($s . "l");
        };
        $h = function (string $s) {
            return ImmSet($s . "o");
        };
        $hello = mcompose($f, $g, $g, $h);
        $this->assertIsLike($hello($xs), ImmSet("hello"));
    }

    /**
     * @test
     */
    public function it_returns_an_empty_set_when_an_empty_set_is_applied()
    {
        $set = ImmSet();
        $this->assertPropertyCount(
            0,
            $set->apply(ImmSet(function ($x) {
                return $x + 1;
            }))
        );
    }

    /**
     * @test
     */
    public function it_applies_the_result_of_the_function_to_a_Set()
    {
        $this->forAll(
            new SeqGen(new IntGen())
        )->then(function ($list) {
            $this->assertIsLike(
                ImmSet(...$list)
                    ->apply(ImmSet(function ($x) {
                        return $x + 1;
                    })),
                ImmSet(...array_map(function ($x) {
                    return $x + 1;
                }, $list))
            );
        });
    }
}

class Item
{
    private $item;
    public function __construct($item)
    {
        $this->item = $item;
    }
}
function Item($item)
{
    return new Item($item);
}
