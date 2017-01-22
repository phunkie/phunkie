<?php

namespace spec\Phunkie\Types;

use Phunkie\Cats\Show;
use const Phunkie\Functions\function1\identity;
use function Phunkie\Functions\show\showValue;
use function Phunkie\Functions\show\usesTrait;
use Phunkie\Ops\Function1\Function1ApplicativeOps;
use Phunkie\Types\Function1;
use PhpSpec\ObjectBehavior;

/**
 * @mixin Function1ApplicativeOps
 */
class Function1Spec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(function($x) { return $x + 1; });
    }

    function it_is_showable()
    {
        $this->beConstructedWith(function(int $x):int { return $x + 1; });
        $this->shouldBeShowable();
        expect(showValue($this->getWrappedObject()))->toReturn("Function1(int=>int)");
    }

    function it_is_has_applicative_ops()
    {
        expect(usesTrait($this->getWrappedObject(), Function1ApplicativeOps::class))->toBe(true);
    }

    function it_returns_an_identity_when_identity_is_applied_to_itself()
    {
        $this->beConstructedWith(identity);
        $this->apply(Function1(identity))->eqv(Function1::identity(), Some(42))->shouldBe(true);
    }

    function getMatchers()
    {
        return ["beShowable" => function($sus){
            return usesTrait($sus, Show::class);
        }];
    }
}