<?php

namespace spec\Md\Phunkie\Types;

use Md\Phunkie\Cats\Show;
use const Md\Phunkie\Functions\function1\identity;
use function Md\Phunkie\Functions\show\get_value_to_show;
use function Md\Phunkie\Functions\show\object_class_uses_trait;
use Md\Phunkie\Ops\Function1\Function1ApplicativeOps;
use Md\Phunkie\Types\Function1;
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
        expect(get_value_to_show($this->getWrappedObject()))->toReturn("Function1(int=>int)");
    }

    function it_is_has_applicative_ops()
    {
        expect(object_class_uses_trait($this->getWrappedObject(), Function1ApplicativeOps::class))->toBe(true);
    }

    function it_returns_an_identity_when_identity_is_applied_to_itself()
    {
        $this->beConstructedWith(identity);
        $this->apply(Function1(identity))->eqv(Function1::identity(), Some(42))->shouldBe(true);
    }

    function getMatchers()
    {
        return ["beShowable" => function($sus){
            return object_class_uses_trait($sus, Show::class);
        }];
    }
}