<?php

namespace spec\Phunkie\Cats;

use Phunkie\Cats\StateT;
use PhpSpec\ObjectBehavior;

class StateTSpec extends ObjectBehavior
{
    function it_runs_function_under_a_context()
    {
        $this->beConstructedWith(Some(function($n) { return Some(Pair($n + 1, $n)); }));
        $this->run(1)->shouldBeLike(Some(Pair(2,1)));
    }
}