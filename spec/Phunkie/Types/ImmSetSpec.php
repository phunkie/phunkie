<?php

namespace spec\Phunkie\Types;

use Md\Unit\TestCase;
use Phunkie\Cats\Foldable;
use Phunkie\Cats\Functor;
use Phunkie\Types\ImmSet;
use Md\PropertyTesting\TestTrait;
use Eris\Generator\SequenceGenerator as SeqGen;
use Eris\Generator\IntegerGenerator as IntGen;
use PHPUnit\Framework\Attributes\Test;
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

    #[Test]
    public function it_is_initializable()
    {
        $ref = new \ReflectionClass(ImmSet::class);
        $this->assertEquals($ref->getName(), 'Phunkie\\Types\\ImmSet');
    }

    #[Test]
    public function it_does_not_keep_duplicates()
    {
        $set = ImmSet(1, 2, 3, 2);
        $this->assertIsLike($set, ImmSet(1, 2, 3));
    }

    #[Test]
    public function it_does_not_keep_object_duplicates()
    {
        $set = ImmSet(Item(1), Item(2), Item(3), Item(2));
        $this->assertIsLike($set, ImmSet(Item(1), Item(2), Item(3)));
    }

    #[Test]
    public function it_lets_you_test_if_an_element_is_part_of_the_set()
    {
        $set = ImmSet(1, 2, 3);
        $this->assertTrue($set->contains(3));
        $this->assertFalse($set->contains(42));
    }

    #[Test]
    public function it_has_minus()
    {
        $set = ImmSet(1, 2, 3);
        $this->assertIsLike($set->minus(2), ImmSet(1, 3));
        $this->assertIsLike($set->minus(42), ImmSet(1, 2, 3));
    }

    #[Test]
    public function it_has_minus_with_objects()
    {
        $set = ImmSet(Item(1), Item(2), Item(3));
        $this->assertIsLike($set->minus(Item(2)), ImmSet(Item(1), Item(3)));
        $this->assertIsLike(
            $set->minus(Item(42)),
            ImmSet(Item(1), Item(2), Item(3))
        );
    }

    #[Test]
    public function it_has_plus()
    {
        $set = ImmSet(1, 2, 3);
        $this->assertIsLike($set->plus(2), ImmSet(1, 2, 3));
        $this->assertIsLike($set->plus(42), ImmSet(1, 2, 3, 42));
    }

    #[Test]
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

    #[Test]
    public function it_has_the_union_operation()
    {
        $set = ImmSet(1, 2, 3);
        $this->assertIsLike(
            $set->union(ImmSet(3, 4, 5)),
            ImmSet(1, 2, 3, 4, 5)
        );
    }

    #[Test]
    public function it_has_the_intersect_operation()
    {
        $set = ImmSet(1, 2, 3);
        $this->assertIsLike($set->intersect(ImmSet(3, 4, 5)), ImmSet(3));
    }

    #[Test]
    public function it_has_the_diff_operation()
    {
        $set = ImmSet(1, 2, 3);
        $this->assertIsLike($set->diff(ImmSet(3, 4, 5)), ImmSet(1, 2, 4, 5));
    }

    #[Test]
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
            $set->zipWith(fn ($x) => 2 * $x),
            ImmSet(Pair(1, 2), Pair(2, 4), Pair(3, 6))
        );
        $this->assertIsLike(
            zipWith(fn ($x) => 2 * $x, ImmSet(1, 2, 3)),
            ImmSet(Pair(1, 2), Pair(2, 4), Pair(3, 6))
        );
    }

    #[Test]
    public function it_is_an_applicative()
    {
        $xs = (ap(ImmSet(fn ($a) => $a +1)))(ImmSet(1));
        $this->assertIsLike($xs, ImmSet(2));

        $xs = (pure(ImmSet))(42);
        $this->assertIsLike($xs, ImmSet(42));

        $xs = ((map2(fn ($x, $y) => $x + $y))(ImmSet(1)))(ImmSet(2));
        $this->assertIsLike($xs, ImmSet(3));
    }

    #[Test]
    public function it_is_a_monad()
    {
        $xs = (bind(fn ($a) => ImmSet($a +1)))(ImmSet(1));
        $this->assertIsLike($xs, ImmSet(2));

        $xs = flatten(ImmSet(ImmSet(1)));
        $this->assertIsLike($xs, ImmSet(1));

        $xs = flatten(ImmSet(ImmSet(1), ImmSet(2)));
        $this->assertIsLike($xs, ImmSet(1, 2));

        $xs = ImmSet("h");
        $f = fn (string $s) => ImmSet($s . "e");
        $g = fn (string $s) => ImmSet($s . "l");
        $h = fn (string $s) => ImmSet($s . "o");
        $hello = mcompose($f, $g, $g, $h);
        $this->assertIsLike($hello($xs), ImmSet("hello"));
    }

    #[Test]
    public function it_returns_an_empty_set_when_an_empty_set_is_applied()
    {
        $set = ImmSet();
        $this->assertPropertyCount(
            0,
            $set->apply(ImmSet(fn ($x) => $x + 1))
        );
    }

    #[Test]
    public function it_applies_the_result_of_the_function_to_a_Set()
    {
        $this->forAll(
            new SeqGen(new IntGen())
        )->then(function ($list) {
            $this->assertIsLike(
                ImmSet(...$list)
                    ->apply(ImmSet(fn ($x) => $x + 1)),
                ImmSet(...array_map(fn ($x) => $x + 1, $list))
            );
        });
    }

    #[Test]
    public function it_is_foldable()
    {
        $set = ImmSet(1, 2, 3);
        $this->assertInstanceOf(Foldable::class, $set);
    }

    #[Test]
    public function it_can_fold_left()
    {
        $set = ImmSet(1, 2, 3);
        $sum = ($set->foldLeft(0))(fn ($acc, $x) => $acc + $x);
        $this->assertEquals(6, $sum);

        $concat = ($set->foldLeft(""))(fn ($acc, $x) => $acc . $x);
        $this->assertEquals("123", $concat);

        // Empty set
        $empty = ImmSet();
        $result = ($empty->foldLeft(10))(fn ($acc, $x) => $acc + $x);
        $this->assertEquals(10, $result);
    }

    #[Test]
    public function it_can_fold_right()
    {
        $set = ImmSet(1, 2, 3);
        $sum = ($set->foldRight(0))(fn ($x, $acc) => $x + $acc);
        $this->assertEquals(6, $sum);

        $concat = ($set->foldRight(""))(fn ($x, $acc) => $x . $acc);
        $this->assertEquals("321", $concat);

        // Empty set
        $empty = ImmSet();
        $result = ($empty->foldRight(10))(fn ($x, $acc) => $x + $acc);
        $this->assertEquals(10, $result);
    }

    #[Test]
    public function it_can_fold_map()
    {
        $set = ImmSet(1, 2, 3);
        $result = $set->foldMap(fn ($x) => [$x]);
        $this->assertEquals([1, 2, 3], $result);

        $strings = ImmSet("a", "b", "c");
        $concat = $strings->foldMap(fn ($x) => $x);
        $this->assertEquals("abc", $concat);
    }

    #[Test]
    public function it_can_fold()
    {
        $set = ImmSet(1, 2, 3);
        $sum = ($set->fold(0))(fn ($acc, $x) => $acc + $x);
        $this->assertEquals(6, $sum);

        // Empty set returns initial
        $empty = ImmSet();
        $result = ($empty->fold(42))(fn ($acc, $x) => $acc + $x);
        $this->assertEquals(42, $result);
    }

    #[Test]
    public function it_has_zero_as_empty_set()
    {
        $set = ImmSet(1, 2, 3);
        $zero = $set->zero();
        $this->assertIsLike($zero, ImmSet());
        $this->assertTrue($zero->isEmpty());
    }

    #[Test]
    public function it_can_combine_sets()
    {
        $s1 = ImmSet(1, 2);
        $s2 = ImmSet(2, 3);
        $combined = $s1->combine($s2);
        $this->assertIsLike($combined, ImmSet(1, 2, 3));
    }

    #[Test]
    public function monoid_left_identity()
    {
        $set = ImmSet(1, 2, 3);
        $zero = $set->zero();
        $this->assertIsLike($zero->combine($set), $set);
    }

    #[Test]
    public function monoid_right_identity()
    {
        $set = ImmSet(1, 2, 3);
        $zero = $set->zero();
        $this->assertIsLike($set->combine($zero), $set);
    }

    #[Test]
    public function monoid_associativity()
    {
        $s1 = ImmSet(1, 2);
        $s2 = ImmSet(2, 3);
        $s3 = ImmSet(3, 4);

        $left = $s1->combine($s2)->combine($s3);
        $right = $s1->combine($s2->combine($s3));

        $this->assertIsLike($left, $right);
    }

    #[Test]
    public function it_can_filter()
    {
        $set = ImmSet(1, 2, 3, 4, 5);
        $evens = $set->filter(fn ($x) => $x % 2 === 0);
        $this->assertIsLike($evens, ImmSet(2, 4));

        $all = $set->filter(fn ($x) => true);
        $this->assertIsLike($all, $set);

        $none = $set->filter(fn ($x) => false);
        $this->assertIsLike($none, ImmSet());
    }

    #[Test]
    public function it_can_filter_not()
    {
        $set = ImmSet(1, 2, 3, 4, 5);
        $odds = $set->filterNot(fn ($x) => $x % 2 === 0);
        $this->assertIsLike($odds, ImmSet(1, 3, 5));

        $none = $set->filterNot(fn ($x) => true);
        $this->assertIsLike($none, ImmSet());

        $all = $set->filterNot(fn ($x) => false);
        $this->assertIsLike($all, $set);
    }

    #[Test]
    public function it_can_partition()
    {
        $set = ImmSet(1, 2, 3, 4, 5);
        $pair = $set->partition(fn ($x) => $x % 2 === 0);

        $this->assertIsLike($pair->_1, ImmSet(2, 4));
        $this->assertIsLike($pair->_2, ImmSet(1, 3, 5));
    }

    #[Test]
    public function it_has_add_method()
    {
        $set = ImmSet(1, 2, 3);
        $this->assertIsLike($set->add(4), ImmSet(1, 2, 3, 4));
        $this->assertIsLike($set->add(2), ImmSet(1, 2, 3));
    }

    #[Test]
    public function it_has_remove_method()
    {
        $set = ImmSet(1, 2, 3);
        $this->assertIsLike($set->remove(2), ImmSet(1, 3));
        $this->assertIsLike($set->remove(5), ImmSet(1, 2, 3));
    }

    #[Test]
    public function it_can_check_subset()
    {
        $s1 = ImmSet(1, 2);
        $s2 = ImmSet(1, 2, 3, 4);
        $s3 = ImmSet(5, 6);

        $this->assertTrue($s1->subsetOf($s2));
        $this->assertTrue($s1->subsetOf($s1));
        $this->assertFalse($s2->subsetOf($s1));
        $this->assertFalse($s3->subsetOf($s1));
        $this->assertTrue(ImmSet()->subsetOf($s1));
    }

    #[Test]
    public function it_can_check_proper_subset()
    {
        $s1 = ImmSet(1, 2);
        $s2 = ImmSet(1, 2, 3);

        $this->assertTrue($s1->properSubsetOf($s2));
        $this->assertFalse($s1->properSubsetOf($s1));
        $this->assertFalse($s2->properSubsetOf($s1));
    }

    #[Test]
    public function it_can_check_superset()
    {
        $s1 = ImmSet(1, 2, 3, 4);
        $s2 = ImmSet(1, 2);
        $s3 = ImmSet(5, 6);

        $this->assertTrue($s1->supersetOf($s2));
        $this->assertTrue($s1->supersetOf($s1));
        $this->assertFalse($s2->supersetOf($s1));
        $this->assertFalse($s3->supersetOf($s1));
        $this->assertTrue($s1->supersetOf(ImmSet()));
    }

    #[Test]
    public function it_can_check_proper_superset()
    {
        $s1 = ImmSet(1, 2, 3);
        $s2 = ImmSet(1, 2);

        $this->assertTrue($s1->properSupersetOf($s2));
        $this->assertFalse($s1->properSupersetOf($s1));
        $this->assertFalse($s2->properSupersetOf($s1));
    }

    #[Test]
    public function it_can_find_min()
    {
        $set = ImmSet(3, 1, 4, 1, 5);
        $this->assertTrue($set->min()->isDefined());
        $this->assertEquals(1, $set->min()->get());

        $empty = ImmSet();
        $this->assertTrue($empty->min()->isEmpty());
    }

    #[Test]
    public function it_can_find_max()
    {
        $set = ImmSet(3, 1, 4, 1, 5);
        $this->assertTrue($set->max()->isDefined());
        $this->assertEquals(5, $set->max()->get());

        $empty = ImmSet();
        $this->assertTrue($empty->max()->isEmpty());
    }

    #[Test]
    public function it_can_convert_to_list()
    {
        $set = ImmSet(3, 1, 4);
        $list = $set->toList();
        $this->assertIsLike($list, ImmList(3, 1, 4));
    }

    #[Test]
    public function it_has_size_method()
    {
        $set = ImmSet(1, 2, 3);
        $this->assertEquals(3, $set->size());

        $empty = ImmSet();
        $this->assertEquals(0, $empty->size());
    }

    #[Test]
    public function it_has_length_method()
    {
        $set = ImmSet(1, 2, 3);
        $this->assertEquals(3, $set->length());
    }

    #[Test]
    public function it_has_head_method()
    {
        $set = ImmSet(1, 2, 3);
        $this->assertTrue($set->head()->isDefined());
        $this->assertEquals(1, $set->head()->get());

        $empty = ImmSet();
        $this->assertTrue($empty->head()->isEmpty());
    }

    #[Test]
    public function it_can_check_disjoint()
    {
        $s1 = ImmSet(1, 2);
        $s2 = ImmSet(3, 4);
        $s3 = ImmSet(2, 3);

        $this->assertTrue($s1->disjoint($s2));
        $this->assertFalse($s1->disjoint($s3));
        $this->assertTrue(ImmSet()->disjoint($s1));
    }

    #[Test]
    public function functor_identity_law()
    {
        $this->forAll(
            new SeqGen(new IntGen())
        )->then(function ($list) {
            $set = ImmSet(...$list);
            $this->assertIsLike($set->map(fn ($x) => $x), $set);
        });
    }

    #[Test]
    public function functor_composition_law()
    {
        $this->forAll(
            new SeqGen(new IntGen())
        )->then(function ($list) {
            $set = ImmSet(...$list);
            $f = fn ($x) => $x + 1;
            $g = fn ($x) => $x * 2;

            $composed = $set->map(fn ($x) => $g($f($x)));
            $separate = $set->map($f)->map($g);

            $this->assertIsLike($composed, $separate);
        });
    }

    #[Test]
    public function foldable_left_fold_consistency()
    {
        $this->forAll(
            new SeqGen(new IntGen())
        )->then(function ($list) {
            if (empty($list)) {
                $this->assertTrue(true);
                return;
            }

            $set = ImmSet(...$list);
            $expected = array_reduce(
                $set->toArray(),
                fn ($acc, $x) => $acc + $x,
                0
            );
            $actual = ($set->foldLeft(0))(fn ($acc, $x) => $acc + $x);

            $this->assertEquals($expected, $actual);
        });
    }

    #[Test]
    public function monoid_law_associativity_property()
    {
        $this->forAll(
            new SeqGen(new IntGen()),
            new SeqGen(new IntGen()),
            new SeqGen(new IntGen())
        )->then(function ($list1, $list2, $list3) {
            $s1 = ImmSet(...$list1);
            $s2 = ImmSet(...$list2);
            $s3 = ImmSet(...$list3);

            $left = $s1->combine($s2)->combine($s3);
            $right = $s1->combine($s2->combine($s3));

            $this->assertIsLike($left, $right);
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
