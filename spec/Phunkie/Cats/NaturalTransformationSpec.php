<?php

namespace spec\Phunkie\Cats;

use PhpSpec\ObjectBehavior;
use Phunkie\Cats\Show;
use const Phunkie\Functions\option\optionToList;
use function Phunkie\Functions\show\usesTrait;

class NaturalTransformationSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(optionToList);
    }

    function it_applies_a_natural_transformation()
    {
        $this(Some(42))->shouldBeLike(Immlist(42));
    }

    function it_is_showable()
    {
        $this->shouldBeShowable();
        $this->showType()->shouldReturn("~>[Option, ImmList]");
    }

    function getMatchers()
    {
        return ["beShowable" => function($sus){
            return usesTrait($sus, Show::class);
        }];
    }
}
