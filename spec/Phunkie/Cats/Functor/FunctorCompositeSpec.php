<?php

namespace spec\Phunkie\Cats\Functor;

use Phunkie\Cats\Functor\FunctorComposite;
use function Phunkie\Functions\show\is_showable;
use Phunkie\Types\Function1;
use Phunkie\Types\ImmList;
use Phunkie\Types\Option;
use PhpSpec\ObjectBehavior;

/**
 * @mixin FunctorComposite
 */
class FunctorCompositeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(Option::kind);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(FunctorComposite::class);
    }

    function it_works_like_a_functor()
    {
        $this->map(Option(1), function($x) {return $x + 1;})->shouldBeLike(Some(2));
    }

    function it_is_showable()
    {
        $this->shouldBeShowable();
        $this->toString()->shouldReturn('Functor(Option)');
    }

    function it_is_composable()
    {
        $this->compose(ImmList::kind)->toString()->shouldReturn('Functor(Option(List))');
        $this->compose(ImmList::kind)->compose(Function1::kind)->toString('Functor(Option(List(Function1)))');
    }

    function it_composes_functor_functionality()
    {
        $this->beConstructedWith(ImmList::kind);
        $fa = $this->compose(Option::kind);
        $xs = ImmList(Some(1), None(), Some(2));
        $fa->map($xs, function($x) { return $x + 1;})->shouldBeLike(ImmList(Some(2), None(), Some(3)));
    }

    function getMatchers()
    {
        return ['beShowable' => function($object) {
            return is_showable($object);
        }];
    }
}