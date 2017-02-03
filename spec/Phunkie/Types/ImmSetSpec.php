<?php

namespace spec\Phunkie\Types;

use PhpSpec\ObjectBehavior;
use Phunkie\Cats\Functor;
use function Phunkie\Functions\functor\allAs;
use function Phunkie\Functions\functor\asVoid;
use function Phunkie\Functions\functor\fmap;
use function Phunkie\Functions\functor\zipWith;
use const Phunkie\Functions\numbers\increment;

class ImmSetSpec extends ObjectBehavior
{
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
}

class Item { private $item;
    public function __construct($item) { $this->item = $item; }
}
function Item($item) { return new Item($item); }