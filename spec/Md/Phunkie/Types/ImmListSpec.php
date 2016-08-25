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
        expect(get_value_to_show(ImmList(1,2,3)))->toReturn("List(1, 2, 3)");
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
        $this->map(function($x) { return $x + 1; })->shouldBeEmpty();
    }

    function it_is_has_applicative_ops()
    {
        expect(object_class_uses_trait($this->getWrappedObject(), ImmListApplicativeOps::class))->toBe(true);
    }

    function it_returns_an_empty_list_when_an_empty_list_is_applied()
    {
        $this->beAnInstanceOf(Nil::class);
        $this->beConstructedWith();
        $this->apply(ImmList(function($x) { return $x + 1; }))->shouldBeEmpty();
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

    function it_returns_its_length()
    {
        $this->beAnInstanceOf(Cons::class);
        $this->beConstructedWith(1,ImmList(2,3));
        $this->length->shouldBe(3);
    }

    function it_has_filter()
    {
        $this->beAnInstanceOf(Cons::class);
        $this->beConstructedWith(1,ImmList(2,3));
        $this->filter(function($x){return $x == 2;})->shouldBeLike(ImmList(2));
    }

    function it_can_be_casted_to_array()
    {
        $this->beAnInstanceOf(Cons::class);
        $this->beConstructedWith(1,ImmList(2,3));
        $this->toArray()->shouldBe([1,2,3]);
    }

    function it_zips()
    {
        $this->beAnInstanceOf(Cons::class);
        $this->beConstructedWith(1,ImmList(2,3));
        $this->zip(ImmList("A", "B", "C"))->shouldBeLike(
            ImmList(Pair(1,"A"), Pair(2,"B"), Pair(3,"C"))
        );
    }

    function it_takes_n_elements_from_list()
    {
        $this->beAnInstanceOf(Cons::class);
        $this->beConstructedWith(1,ImmList(2,3));
        $this->take(2)->shouldBeLike(ImmList(1,2));
    }

    function it_drops_n_elements_from_list()
    {
        $this->beAnInstanceOf(Cons::class);
        $this->beConstructedWith(1,ImmList(2,3));
        $this->drop(2)->shouldBeLike(ImmList(3));
    }

    function it_implements_head()
    {
        $this->beAnInstanceOf(Cons::class);
        $this->beConstructedWith(1,ImmList(2,3));
        $this->head->shouldBe(1);
    }

    function it_implements_tail()
    {
        $this->beAnInstanceOf(Cons::class);
        $this->beConstructedWith(1,ImmList(2,3));
        $this->tail->shouldBeLike(ImmList(2,3));
    }

    function it_implements_init()
    {
        $this->beAnInstanceOf(Cons::class);
        $this->beConstructedWith(1,ImmList(2,3));
        $this->init->shouldBeLike(ImmList(1,2));
    }

    function it_implements_last()
    {
        $this->beAnInstanceOf(Cons::class);
        $this->beConstructedWith(1,ImmList(2,3));
        $this->last->shouldBe(3);
    }

    function getMatchers()
    {
        return ["beShowable" => function($sus){
            return object_class_uses_trait($sus, Show::class);
        }];
    }
}