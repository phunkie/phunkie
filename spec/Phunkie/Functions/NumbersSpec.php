<?php

namespace spec\Phunkie\Functions;

use function Phunkie\Functions\numbers\even;
use function Phunkie\Functions\numbers\negate;
use function Phunkie\Functions\numbers\odd;
use function Phunkie\Functions\numbers\increment;
use function Phunkie\Functions\numbers\decrement;
use PhpSpec\ObjectBehavior;
use function Phunkie\Functions\numbers\signum;

class NumbersSpec extends ObjectBehavior
{
    function it_implements_even()
    {
        expect(even(1))->toBe(false);
        expect(even(2))->toBe(true);
    }

    function it_implements_odd()
    {
        expect(odd(1))->toBe(true);
        expect(odd(2))->toBe(false);
    }

    function it_implements_increment()
    {
        expect(increment(1))->toBe(2);
        expect(increment(2))->toBe(3);
    }

    function it_implements_decrement()
    {
        expect(decrement(1))->toBe(0);
        expect(decrement(2))->toBe(1);
    }

    function it_implements_negate()
    {
        expect(negate(1))->toBe(-1);
        expect(negate(-1))->toBe(1);
    }

    function it_implements_signum()
    {
        expect(signum(32))->toBe(1);
        expect(signum(0))->toBe(0);
        expect(signum(-32))->toBe(-1);
    }
}