<?php

namespace spec\Md\Phunkie\Cats;

use Md\Phunkie\Cats\StateT;
use function Md\Phunkie\Functions\state\put;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class StateTSpec extends ObjectBehavior
{
    function it_runs_function_under_a_context()
    {
        $increment = new StateT(Some(function($n) { return Some(Pair($n + 1, $n)); }));
        expect($increment->run(1))->toBeLike(Some(Pair(2,1)));
    }
}
