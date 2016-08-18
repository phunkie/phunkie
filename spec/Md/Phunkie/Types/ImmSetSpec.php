<?php

namespace spec\Md\Phunkie\Types;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ImmSetSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Md\Phunkie\Types\ImmSet');
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
}

class Item { private $item;
    public function __construct($item) { $this->item = $item; }
}
function Item($item) { return new Item($item); }