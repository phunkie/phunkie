<?php

namespace spec\Md\Phunkie;

use DomainException;
use function Md\Phunkie\Functions\pattern_matching\matching;
use function Md\Phunkie\Functions\pattern_matching\on;
use Md\Phunkie\Types\Function1;
use PhpSpec\Exception\Example\FailureException;
use PhpSpec\ObjectBehavior;

class PatternMatchingSpec extends ObjectBehavior
{
    function it_behaves_like_a_switch()
    {
        $result = matching(1 + 1,
            on(3)->returns(2),
            on(2)->returns(3)
        );

        expect($result)->toBe(3);
    }
    function it_supports_a_default_clause_with_underscore()
    {
        $result = matching(1 + 1,
            on(3)->returns(2),
            on(4)->returns(4),
            on(_)->returns(6)
        );

        expect($result)->toBe(6);
    }

    function it_complains_when_nothing_matches()
    {
        try {
            matching(1 + 1,
                on(3)->returns(2),
                on(4)->returns(4)
            );
            throw new FailureException("Should throw but didn't.");
        } catch (\Exception $e) {
            expect($e)->toHaveType(\Md\Phunkie\Functions\pattern_matching\MatchError::class);
        }
    }

    function it_does_not_break_when_comparing_objects_to_scalars()
    {
        $result = matching(1 + 1,
            on(Some(3))->returns(2),
            on(2)->returns(8)
        );

        expect($result)->toBe(8);
    }

    function it_supports_wildcard_for_options()
    {
        $result = matching(Some(1 + 1),
            on(Some(3))->returns(2),
            on(Some(_))->returns(10)
        );

        expect($result)->toBe(10);
    }

    function it_supports_wildcard_for_function1()
    {
        $result = matching(Function1::identity(),
            on(Some(3))->returns(2),
            on(Function1(_))->returns(10)
        );

        expect($result)->toBe(10);
    }

    function it_supports_wildcard_for_lists()
    {
        $result = matching(ImmList(1,3,4),
            on(ImmList(1,2,3))->returns(2),
            on(ImmList(_))->returns(10)
        );

        expect($result)->toBe(10);
    }

    function it_supports_wildcard_for_failure()
    {
        $boom = function () { return Failure(new \Exception("Boom!")); };
        $result = matching($boom(),
            on(Success(_))->returns(2),
            on(Failure(_))->returns(10)
        );

        expect($result)->toBe(10);
    }

    function it_supports_wildcard_for_success()
    {
        $yay = function () { return Success("yay!"); };
        $result = matching($yay(),
            on(Success(_))->returns(2),
            on(Failure(_))->returns(10)
        );

        expect($result)->toBe(2);
    }

    function it_compares_none()
    {
        $result = matching(None(),
            on(None)->returns(2),
            on(Some(_))->returns(3)
        );

        expect($result)->toBe(2);
    }

    function it_throws()
    {
        try {
            matching(1 + 1,
                on(3)->returns(2),
                on(2)->throws(new DomainException())
            );
            throw new FailureException("Should throw but didn't.");
        } catch (DomainException $e) {
            expect($e)->toHaveType(DomainException::class);
        }
    }

    function it_can_take_just_conditions()
    {
        $result = matching(
            on(false)->returns(2),
            on(true)->returns(3)
        );

        expect($result)->toBe(3);
    }
    
    function it_can_return_on_more_than_one_condition()
    {
        $result = matching(42,
            on(2)->or(42)->returns(3)
        );

        expect($result)->toBe(3);
    }
}