<?php

namespace spec\Phunkie\Types;

use PhpSpec\ObjectBehavior;
use Phunkie\Cats\Functor;
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
use Phunkie\Types\ImmSet;

use Md\PropertyTesting\TestTrait;
use Eris\Generator\SequenceGenerator as SeqGen;
use Eris\Generator\IntegerGenerator as IntGen;

class ImmSetSpec extends ObjectBehavior
{
    use TestTrait;

    function it_is_initializable()
    {
        $this->shouldHaveType('Phunkie\Types\ImmSet');
    }

    function it_does_not_keep_duplicates()
    {
        $this->beConstructedWith(1,2,3,2);
        $this->shouldBeLike(ImmSet(1,2,3));
    }

    function it_does_not_keep_object_duplicates()
    {
        $this->beConstructedWith(Item(1),Item(2),Item(3),Item(2));
        $this->shouldBeLike(ImmSet(Item(1),Item(2),Item(3)));
    }

    function it_lets_you_test_if_an_element_is_part_of_the_set()
    {
        $this->beConstructedWith(1,2,3);
        $this->contains(3)->shouldBe(true);
        $this->contains(42)->shouldBe(false);
    }

    function it_has_minus()
    {
        $this->beConstructedWith(1,2,3);
        $this->minus(2)->shouldBeLike(ImmSet(1,3));
        $this->minus(42)->shouldBeLike(ImmSet(1,2,3));
    }

    function it_has_minus_with_objects()
    {
        $this->beConstructedWith(Item(1),Item(2),Item(3));
        $this->minus(Item(2))->shouldBeLike(ImmSet(Item(1),Item(3)));
        $this->minus(Item(42))->shouldBeLike(ImmSet(Item(1),Item(2),Item(3)));
    }

    function it_has_plus()
    {
        $this->beConstructedWith(1,2,3);
        $this->plus(2)->shouldBeLike(ImmSet(1,2,3));
        $this->plus(42)->shouldBeLike(ImmSet(1,2,3,42));
    }

    function it_has_plus_with_objects()
    {
        $this->beConstructedWith(Item(1),Item(2),Item(3));
        $this->plus(Item(2))->shouldBeLike(ImmSet(Item(1),Item(2),Item(3)));
        $this->plus(Item(42))->shouldBeLike(ImmSet(Item(1),Item(2),Item(3),Item(42)));
    }

    function it_has_the_union_operation()
    {
        $this->beConstructedWith(1,2,3);
        $this->union(ImmSet(3,4,5))->shouldBeLike(ImmSet(1,2,3,4,5));
    }

    function it_has_the_intersect_operation()
    {
        $this->beConstructedWith(1,2,3);
        $this->intersect(ImmSet(3,4,5))->shouldBeLike(ImmSet(3));
    }
    
    function it_has_the_diff_operation()
    {
        $this->beConstructedWith(1,2,3);
        $this->diff(ImmSet(3,4,5))->shouldBeLike(ImmSet(1,2,4,5));
    }

    function it_is_a_functor()
    {
        $this->beConstructedWith(1,2,3);
        $this->shouldHaveType(Functor::class);

        $this->map(increment)->shouldBeLike(ImmSet(2,3,4));
        expect(fmap(increment, ImmSet(1,2,3)))->toBeLike(ImmSet(2,3,4));

        $this->as(0)->shouldBeLike(ImmSet(0,0,0));
        expect(allAs(0, ImmSet(1,2,3)))->toBeLike(ImmSet(0,0,0));

        $this->void()->shouldBeLike(ImmSet(Unit(), Unit(), Unit()));
        expect(asVoid(ImmSet(1,2,3)))->toBeLike(ImmSet(Unit(), Unit(), Unit()));

        $this->zipWith(function($x) { return 2 * $x; })->shouldBeLike(ImmSet(Pair(1,2), Pair(2,4), Pair(3,6)));
        expect(zipWith(function($x) { return 2 * $x; }, ImmSet(1,2,3)))->toBeLike(
            ImmSet(Pair(1,2), Pair(2,4), Pair(3,6))
        );
    }

    function it_is_an_applicative()
    {
        $xs = (ap (ImmSet(function($a) { return $a +1; }))) (ImmSet(1));
        expect($xs)->toBeLike(ImmSet(2));

        $xs = (pure (ImmSet)) (42);
        expect($xs)->toBeLike(ImmSet(42));

        $xs = ((map2 (function($x, $y) { return $x + $y; })) (ImmSet(1))) (ImmSet(2));
        expect($xs)->toBeLike(ImmSet(3));
    }

    function it_is_a_monad()
    {
        $xs = (bind (function($a) { return ImmSet($a +1); })) (ImmSet(1));
        expect($xs)->toBeLike(ImmSet(2));

        $xs = flatten (ImmSet(ImmSet(1)));
        expect($xs)->toBeLike(ImmSet(1));

        $xs = flatten (ImmSet(ImmSet(1), ImmSet(2)));
        expect($xs)->toBeLike(ImmSet(1, 2));

        $xs = ImmSet("h");
        $f = function(string $s) { return ImmSet($s . "e"); };
        $g = function(string $s) { return ImmSet($s . "l"); };
        $h = function(string $s) { return ImmSet($s . "o"); };
        $hello = mcompose($f, $g, $g, $h);
        expect($hello($xs))->toBeLike(ImmSet("hello"));
    }

    function it_returns_an_empty_set_when_an_empty_set_is_applied()
    {
        $this->beAnInstanceOf(ImmSet::class);
        $this->beConstructedWith();
        $this->apply(ImmSet(function ($x) {return $x + 1;}))->shouldBeEmpty();
    }

    function it_applies_the_result_of_the_function_to_a_Set()
    {
        $this->forAll(
            new SeqGen(new IntGen())
        )->then(function($list) {
            expect(ImmSet(...$list)->apply(ImmSet(function($x) { return $x + 1; })))
                ->toBeLike(ImmSet(...array_map(function($x) { return $x + 1; }, $list)));
        });
    }
}

class Item { private $item;
    public function __construct($item) { $this->item = $item; }
}
function Item($item) { return new Item($item); }