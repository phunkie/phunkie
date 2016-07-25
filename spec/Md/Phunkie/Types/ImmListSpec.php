<?php

namespace spec\Md\Phunkie\Types;

use Md\Phunkie\Cats\Show;
use function Md\Phunkie\Functions\show\get_value_to_show;
use function Md\Phunkie\Functions\show\object_class_uses_trait;
use Md\Phunkie\Ops\ImmList\ImmListApplicativeOps;
use Md\Phunkie\Types\Cons;
use Md\Phunkie\Types\Nil;
use PhpSpec\ObjectBehavior;

use Eris\TestTrait;
use Eris\Generator\SequenceGenerator as SeqGen;
use Eris\Generator\IntegerGenerator as IntGen;

/**
 * @mixin ImmListApplicativeOps
 */
class ImmListSpec extends ObjectBehavior
{
    use TestTrait;

    function let()
    {
        $this->beAnInstanceOf(Cons::class);
        $this->beConstructedWith(1,ImmList(2,3));
    }

    function it_is_showable()
    {
        $this->shouldBeShowable();
        expect(get_value_to_show(ImmList(1,2,3)))->toReturn("List(1,2,3)");
    }

    function it_is_a_functor()
    {
        $spec = $this;
        $this->forAll(
            new SeqGen(new IntGen())
        )->then(function($list) use ($spec) {
            expect(ImmList(...$list)->map(function ($x) {
                return $x + 1;
            }))->toBeLike(ImmList(...array_map(function($x) { return $x + 1; }, $list)));
        });
    }

    function it_returns_an_empty_list_when_an_empty_list_is_mapped()
    {
        $this->beAnInstanceOf(Nil::class);
        $this->beConstructedWith();
        $this->map(function($x) { return $x + 1; })->shouldBeLike(ImmList());
    }

    function it_is_has_applicative_ops()
    {
        expect(object_class_uses_trait($this->getWrappedObject(), ImmListApplicativeOps::class))->toBe(true);
    }

    function it_returns_an_empty_list_when_an_empty_list_is_applied()
    {
        $this->beAnInstanceOf(Nil::class);
        $this->beConstructedWith();
        $this->apply(ImmList(function($x) { return $x + 1; }))->shouldBeLike(ImmList());
    }

    function it_applies_the_result_of_the_function_to_a_List()
    {
        $spec = $this;
        $this->forAll(
            new SeqGen(new IntGen())
        )->then(function($list) use ($spec) {
            expect(ImmList(...$list)->apply(ImmList(function($x) { return $x + 1; })))
                ->toBeLike(ImmList(...array_map(function($x) { return $x + 1; }, $list)));
        });
    }

    function getMatchers()
    {
        return ["beShowable" => function($sus){
            return object_class_uses_trait($sus, Show::class);
        }];
    }
}