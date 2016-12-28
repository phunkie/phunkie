<?php

namespace spec\Md\Phunkie\Utils;

use Md\Phunkie\Utils\Iterator;
use PhpSpec\ObjectBehavior;

class IteratorSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(new \SplObjectStorage());
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Md\Phunkie\Utils\Iterator');
    }

    /////////////////////
    // Lists           //
    /////////////////////
    function it_can_be_created_from_lists()
    {
        expect(ImmList(1,2,3)->iterator())->toHaveType(Iterator::class);
    }

    function it_is_countable_after_created_from_lists()
    {
        expect(ImmList(1,2,3)->iterator())->toHaveCount(3);
    }

    function it_is_array_access()
    {
        expect(ImmList(1,2,3)->iterator())->toHaveType(\ArrayAccess::class);
    }

    function it_lets_you_access_lists_elements_via_array_notation()
    {
        expect(ImmList(1,2,3)->iterator()[0])->toBeLike(Some(1));
        expect(ImmList(1,2,3)->iterator()[1])->toBeLike(Some(2));
        expect(ImmList(1,2,3)->iterator()[2])->toBeLike(Some(3));
        expect(ImmList(1,2,3)->iterator()[3])->toBeLike(None());
    }

    function it_is_immutable_after_created_from_lists()
    {
        expect(ImmList(1,2,3)->iterator())->toThrow()->duringOffsetSet(42);
        expect(ImmList(1,2,3)->iterator())->toThrow()->duringOffsetUnset();
    }

    function it_lets_you_check_if_a_list_index_exists()
    {
        expect(isset(ImmList(1,2,3)->iterator()[2]))->toBeLike(true);
        expect(isset(ImmList(1,2,3)->iterator()[3]))->toBeLike(false);
    }

    function it_is_foreachable_after_created_from_lists()
    {
        $dataProvider = [ 1, 2, 3];

        foreach(ImmList(1,2,3)->iterator() as $v) {
            expect($v)->toBe(current($dataProvider)); next($dataProvider);
        }
    }

    function it_is_foreachable_with_key_and_value_after_created_from_lists()
    {
        $dataProvider = [
            0, 1,
            1, 2,
            2, 3
        ];

        foreach(ImmList(1,2,3)->iterator() as $k => $v) {
            expect($k)->toBe(current($dataProvider)); next($dataProvider);
            expect($v)->toBe(current($dataProvider)); next($dataProvider);
        }
    }
}
