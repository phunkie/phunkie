<?php

namespace spec\Md\Phunkie;

use Md\Phunkie\Types\Function1;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use function Md\Phunkie\PatternMatching\Referenced\ListWithTail as ListWithTail;
use function Md\Phunkie\PatternMatching\Referenced\Some as Just;
use function Md\Phunkie\PatternMatching\Referenced\Success as Valid;
use function Md\Phunkie\PatternMatching\Referenced\Failure as Invalid;
use function Md\Phunkie\PatternMatching\Wildcarded\ImmList as WildcardedImmList;

class PatternMatchingSpec extends ObjectBehavior
{
    function it_behaves_like_a_switch() {
        $result = null;
        $on = match(1 + 1); switch (true) {
            case $on(3): $result = 2; break;
            case $on(2): $result = 3; break;
        }

        expect($result)->toBe(3);
    }

    function it_supports_a_default_clause_with_underscore()
    {
        $result = null;
        $on = match(1 + 1); switch (true) {
            case $on(3): $result = 2; break;
            case $on(4): $result = 4; break;
            case $on(_): $result = 6; break;
        }
        expect($result)->toBe(6);
    }
    
    function it_does_not_break_when_comparing_objects_to_scalars()
    {
        $result = null;
        $on = match(1 + 1); switch (true) {
            case $on(Some(3)): $result = 2; break;
            case $on(2): $result = 8; break;
        }
        expect($result)->toBe(8);
    }

    function it_supports_wildcard_for_options()
    {
        $result = null;
        $on = match(Some(1 + 1)); switch (true) {
            case $on(Some(3)): $result = 2; break;
            case $on(Some(_)): $result = 10; break;
        }
        expect($result)->toBe(10);
    }

    function it_supports_wildcard_for_none()
    {
        $result = null;
        $on = match(None()); switch (true) {
            case $on(None): $result = 10; break;
            case $on(Some(_)): $result = 2; break;
        }
        expect($result)->toBe(10);
    }

    function it_supports_wildcard_for_function1()
    {
        $result = null;
        $on = match(Function1::identity()); switch (true) {
            case $on(Some(3)): $result = 2; break;
            case $on(Function1(_)): $result = 10; break;
        }
        expect($result)->toBe(10);
    }

    function it_supports_wildcard_for_failure()
    {
        $boom = function () { return Failure(Nel(new \Exception("Boom!"))); };
        $result = null;
        $on = match($boom()); switch (true) {
            case $on(Success(_)): $result = 2; break;
            case $on(Failure(_)): $result = 10; break;
        }
        expect($result)->toBe(10);
    }

    function it_supports_wildcard_for_success()
    {
        $yay = function () { return Success("yay!"); };
        $result = null;
        $on = match($yay()); switch (true) {
            case $on(Failure(_)): $result = 2; break;
            case $on(Success(_)): $result = 10; break;
        }
        expect($result)->toBe(10);
    }

    function it_supports_nil_constant_for_comparing_lists()
    {
        $result = null;
        $on = match(Nil()); switch (true) {
            case $on(Nil): $result = 10; break;
            case $on(Nel(_)): $result = 2; break;
        }
        expect($result)->toBe(10);
    }

    function it_accepts_wildcard_for_head_when_comparing_lists()
    {
        $result = null;
        $on = match(ImmList(1,2)); switch (true) {
            case $on(Nil): $result = 10; break;
            case $on(WildcardedImmList(_, Cons(2, Nil))): $result = 2; break;
        }
        expect($result)->toBe(2);

        $result = null;
        $on = match(ImmList(1)); switch (true) {
            case $on(Nil): $result = 10; break;
            case $on(WildcardedImmList(_, Nil)): $result = 2; break;
        }
        expect($result)->toBe(2);

        $result = null;
        $on = match(ImmList(1, 2)); switch (true) {
            case $on(Nil): $result = 10; break;
            case $on(WildcardedImmList(_, Nil)): $result = 2; break;
            case $on(WildcardedImmList(_, WildcardedImmList(_, Nil))): $result = 3; break;
        }
        expect($result)->toBe(3);
    }

    function it_accepts_wildcard_for_tail_when_comparing_lists()
    {
        $result = null;
        $on = match(ImmList(1,2)); switch (true) {
            case $on(Nil): $result = 10; break;
            case $on(WildcardedImmList(1, _)): $result = 2; break;
        }
        expect($result)->toBe(2);
    }

    function it_accepts_wildcard_for_both_head_and_tail_when_comparing_lists()
    {
        $result = null;
        $on = match(ImmList(1,2)); switch (true) {
            case $on(Nil): $result = 10; break;
            case $on(WildcardedImmList(_, _)): $result = 2; break;
        }
        expect($result)->toBe(2);
    }

    function it_accepts_wildcard_for_nel_when_comparing_lists()
    {
        $result = null;
        $on = match(Nel(1,2)); switch (true) {
            case $on(Nil): $result = 10; break;
            case $on(Nel(_)): $result = 2; break;
        }
        expect($result)->toBe(2);
    }

    function it_accepts_reference_when_comparing_lists()
    {
        $result = null;
        $on = match(ImmList(1,2)); switch (true) {
            case $on(ListWithTail($x, $xs)): $result = $x + $xs->head; break;
        }
        expect($result)->toBe(3);
    }

    function it_accepts_reference_when_comparing_options()
    {
        $result = null;
        $on = match(Some(42)); switch (true) {
            case $on(Just($x)): $result = $x; break;
        }
        expect($result)->toBe(42);
    }

    function it_accepts_reference_when_comparing_successes()
    {
        $yay = function () { return Success("yay!"); };
        $result = null;
        $on = match($yay()); switch (true) {
            case $on(Valid($x)): $result = $x; break;
        }
        expect($result)->toBe($x);
    }

    function it_accepts_reference_when_comparing_failures()
    {
        $boom = function () { return Failure("boom!"); };
        $result = null;
        $on = match($boom()); switch (true) {
        case $on(Invalid($x)): $result = $x; break;
    }
        expect($result)->toBe($x);
    }
}
