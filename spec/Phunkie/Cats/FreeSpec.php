<?php

namespace spec\Phunkie\Cats;

use PhpSpec\ObjectBehavior;

use Phunkie\Cats\Free\Pure;

class FreeSpec extends ObjectBehavior
{
    function it_implements_pure()
    {
        $this->beAnInstanceOf(Pure::class);
        $this->beConstructedWith(42);
        $this->shouldBeLike($this->pure(42));
    }
}
