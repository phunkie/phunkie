<?php

namespace spec\Phunkie\Utils;

use PhpSpec\ObjectBehavior;
use Phunkie\Utils\Trampoline\Done;
use Phunkie\Utils\Trampoline\More;
use Phunkie\Utils\Trampoline\Trampoline;

class TrampolineSpec extends ObjectBehavior
{
    function it_can_help_avoid_stack_overflow()
    {
        expect(odd(5)->run())->toBe(true);

        // uncomment to test locally <- test takes 4 seconds on my computer i7 intel core 2.8 GHz :-)
        // with trampoline this works
        // expect(odd(10000000)->run())->toBe(false);

        // comment the trampolined even/odd functions and uncomment the bare ones,
        // then run this. It should cause stack overflow even with 16GB RAM
        // expect(odd(10000000))->toBe(false);
    }
}

function even($number): Trampoline {
    return $number == 0 ? new Done(true) : new More(function() use ($number) { return odd($number - 1); });
}

function odd($number): Trampoline {
    return $number == 0 ? new Done(false) : new More(function() use ($number) { return even($number - 1); });
}

//function even($number) {
//    return $number == 0 ? true : odd($number - 1);
//}
//
//function odd($number) {
//    return $number == 0 ? false : even($number - 1);
//}