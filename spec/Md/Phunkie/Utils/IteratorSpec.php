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
        expect(isset(ImmList(1,2,3)->iterator()[2]))->toBe(true);
        expect(isset(ImmList(1,2,3)->iterator()[3]))->toBe(false);
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

    /////////////////////
    // Maps            //
    /////////////////////
    function it_can_be_created_from_Maps()
    {
        expect(ImmMap([1,2,3,4])->iterator())->toHaveType(Iterator::class);
    }

    function it_is_countable_after_created_from_maps()
    {
        expect(ImmMap([1,2,3])->iterator())->toHaveCount(3);
    }

    function it_lets_you_access_map_elements_via_array_notation()
    {
        expect(ImmMap([1,2,3])->iterator()[0])->toBeLike(Some(1));
        expect(ImmMap([1,2,3])->iterator()[1])->toBeLike(Some(2));
        expect(ImmMap([1,2,3])->iterator()[2])->toBeLike(Some(3));
        expect(ImmMap([1,2,3])->iterator()[3])->toBeLike(None());

        expect(ImmMap(["a" => 1,"b" => 2])->iterator()["a"])->toBeLike(Some(1));

        $ob1 = new \stdClass(); $ob2 = new \stdClass();
        expect(ImmMap($ob1, 1, $ob2, 2)->iterator()[$ob1])->toBeLike(Some(1));
    }

    function it_is_immutable_after_created_from_maps()
    {
        expect(ImmMap([1,2,3])->iterator())->toThrow()->duringOffsetSet(42);
        expect(ImmMap([1,2,3])->iterator())->toThrow()->duringOffsetUnset();
    }

    function it_lets_you_check_if_a_map_key_exists()
    {
        expect(isset(ImmMap([1,2,3])->iterator()[2]))->toBe(true);
        expect(isset(ImmMap([1,2,3])->iterator()[3]))->toBe(false);

        // string key
        expect(isset(ImmMap(["a" => 1,"b" => 2])->iterator()["a"]))->toBe(true);
        expect(isset(ImmMap(["a" => 1,"b" => 2])->iterator()["c"]))->toBe(false);

        // object key
        $ob1 = new \stdClass(); $ob2 = new \stdClass();
        expect(isset(ImmMap($ob1, 1, $ob2, 2)->iterator()[$ob1]))->toBe(true);
    }

    function it_is_foreachable_after_created_from_maps()
    {
        $dataProvider = [ 1, 2, 3];

        foreach(ImmMap([1,2,3])->iterator() as $v) {
            expect($v)->toBe(current($dataProvider)); next($dataProvider);
        }
    }

    function it_is_foreachable_with_key_and_value_after_created_from_maps()
    {
        $map = [
            "a" => 1,
            "b" => 2,
            "c" => 3
        ];

        foreach(ImmMap($map)->iterator() as $k => $v) {
            expect($k)->toBe(key($map));
            expect($v)->toBe(current($map));
            next($map);
        }
    }
}
