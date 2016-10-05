<?php

namespace spec\Md\Phunkie\Cats;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin \Md\Phunkie\Cats\OptionT
 */
class OptionTSpec extends ObjectBehavior
{
    function it_implements_map()
    {
        $this->beConstructedWith(ImmList(Some(1), None(), Some(2)));
        $this->map(function ($x) { return $x + 1; })
           ->shouldBeLike(OptionT(ImmList(Some(2), None(), Some(3))));
    }


    function it_implements_flatMap()
    {
        $this->beConstructedWith(ImmList(Some(1), None(), Some(2)));
        $this->flatMap(function ($x) { return OptionT(ImmList(Some($x + 1))); })
            ->shouldBeLike(OptionT(ImmList(Some(2), None(), Some(3))));
    }

    function it_immplements_isDefined()
    {
        $this->beConstructedWith(ImmList(Some(1), None(), Some(2)));
        $this->isDefined()->shouldBeLike(ImmList(true, false, true));
    }

    function it_immplements_isEmpty()
    {
        $this->beConstructedWith(ImmList(Some(1), None(), Some(2)));
        $this->isEmpty()->shouldBeLike(ImmList(false, true, false));
    }

    function it_immplements_getOrElse()
    {
        $this->beConstructedWith(ImmList(Some(1), None(), Some(2)));
        $this->getOrElse(42)->shouldBeLike(ImmList(1, 42, 2));
    }
}