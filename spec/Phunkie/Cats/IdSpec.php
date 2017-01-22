<?php

namespace spec\Phunkie\Cats;

use Phunkie\Cats\Id;
use PhpSpec\ObjectBehavior;

class IdSpec extends ObjectBehavior
{
    function it_implements_map()
    {
        $this->beConstructedWith(42);
        $increment = function($x) { return $x + 1; };
        $this->map($increment)->shouldReturn(43);
    }

    function it_implements_flatMap()
    {
        $this->beConstructedWith(42);
        $increment = function($x) { return new Id($x + 1); };
        $this->map($increment)->shouldBeLike(new Id(43));
    }

    function it_implements_andThen()
    {
        $this->beConstructedWith("a");
        $this->andThen("b")->shouldReturn("ab");
    }

    function it_implements_compose()
    {
        $this->beConstructedWith("a");
        $this->compose("b")->shouldReturn("ba");
    }
}