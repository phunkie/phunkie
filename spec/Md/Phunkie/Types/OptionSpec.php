<?php

namespace spec\Md\Phunkie\Types;

use Md\Phunkie\Cats\Show;
use function Md\Phunkie\Functions\get_value_to_show;
use function Md\Phunkie\Functions\object_class_uses_trait;
use Md\Phunkie\Types\None;
use Md\Phunkie\Types\Some;
use PhpSpec\ObjectBehavior;

use Md\Phunkie\Ops\Option\OptionApplicativeOps;

use Eris\TestTrait;
use Eris\Generator\IntegerGenerator as IntGen;

/**
 * @mixin OptionApplicativeOps
 */
class OptionSpec extends ObjectBehavior
{
    use TestTrait;

    function let()
    {
        $this->beAnInstanceOf(Some::class);
        $this->beConstructedThrough('instance', [1]);
    }

    function it_is_showable()
    {
        $this->shouldBeShowable();
        expect(get_value_to_show(Option(2)))->toReturn("Some(2)");
        expect(get_value_to_show(None()))->toReturn("None");
    }

    function it_is_a_functor()
    {
        $spec = $this;
        $this->forAll(
            new IntGen()
        )->then(function($a) use ($spec) {
            expect(Option($a)->map(function ($x) {
                return $x + 1;
            }))->toBeLike(Some($a + 1));
        });
    }

    function it_returns_none_when_none_is_mapped()
    {
        $this->beAnInstanceOf(None::class);
        $this->beConstructedThrough('instance', []);

        $this->map(function($x) { return $x + 1; })->shouldBeLike(None());
    }

    function it_has_applicative_ops()
    {
        $this->shouldBeUsing(OptionApplicativeOps::class);
    }

    function it_returns_none_when_none_is_applied()
    {
        $this->beAnInstanceOf(None::class);
        $this->beConstructedThrough('instance', []);
        $this->apply(Option(function($x) { return $x + 1; }))->shouldBeLike(None());
    }

    function it_applies_the_result_of_the_function_to_a_List()
    {
        $spec = $this;
        $this->forAll(
            new IntGen()
        )->then(function($a) use ($spec) {
            expect(Option($a)->apply(Option(function($x) { return $x + 1; })))
                ->toBeLike(Some($a + 1));
        });
    }

    function getMatchers()
    {
        return [
            "beUsing" => function($sus, $trait){
                return object_class_uses_trait($sus, $trait);
            },
            "beShowable" => function($sus){
                return object_class_uses_trait($sus, Show::class);
            }
        ];
    }
}