<?php

namespace spec\Phunkie\Utils;

use Phunkie\Utils\Iterator;
use Md\Unit\TestCase;

class IteratorSpec extends TestCase
{
    private $it;

    public function setUp(): void
    {
        $this->it = new Iterator(new \SplObjectStorage());
    }

    /**
     * @test
     */
    public function it_is_initializable()
    {
        $ref = new \ReflectionClass($this->it);
        $this->assertEquals('Phunkie\\Utils\\Iterator', $ref->getName());
    }

    /////////////////////
    // Lists           //
    /////////////////////

    /**
     * @test
     */
    public function it_can_be_created_from_lists()
    {
        $this->assertInstanceOf(\Iterator::class, ImmList(1, 2, 3)->iterator());
    }

    /**
     * @test
     */
    public function it_is_countable_after_created_from_lists()
    {
        $this->assertCount(3, ImmList(1, 2, 3)->iterator());
    }

    /**
     * @test
     */
    public function it_is_array_access()
    {
        $this->assertInstanceOf(\ArrayAccess::class, ImmList(1, 2, 3)->iterator());
    }

    /**
     * @test
     */
    public function it_lets_you_access_lists_elements_via_array_notation()
    {
        $this->assertIsLike(ImmList(1, 2, 3)->iterator()[0], Some(1));
        $this->assertIsLike(ImmList(1, 2, 3)->iterator()[1], Some(2));
        $this->assertIsLike(ImmList(1, 2, 3)->iterator()[2], Some(3));
        $this->assertIsLike(ImmList(1, 2, 3)->iterator()[3], None());
    }

    /**
     * @test
     */
    public function it_is_immutable_after_created_from_lists()
    {
        $this->expectException(\TypeError::class);
        $list = ImmList(1, 2, 3)->iterator();
        $list[42] = 32;
    }

    /**
     * @test
     */
    public function it_lets_you_check_if_a_list_index_exists()
    {
        $this->assertTrue(isset(ImmList(1, 2, 3)->iterator()[2]));
        $this->assertFalse(isset(ImmList(1, 2, 3)->iterator()[3]));
    }

    /**
     * @test
     */
    public function it_is_foreachable_after_created_from_lists()
    {
        $dataProvider = [1, 2, 3];

        foreach (ImmList(1, 2, 3)->iterator() as $v) {
            $this->assertEquals(current($dataProvider), $v);
            next($dataProvider);
        }
    }

    /**
     * @test
     */
    public function it_is_foreachable_with_key_and_value_after_created_from_lists()
    {
        $dataProvider = [
            0, 1,
            1, 2,
            2, 3
        ];

        foreach (ImmList(1, 2, 3)->iterator() as $k => $v) {
            $this->assertEquals($k, current($dataProvider));
            next($dataProvider);
            $this->assertEquals($v, current($dataProvider));
            next($dataProvider);
        }
    }

    /////////////////////
    // Maps            //
    /////////////////////

    /**
     * @test
     */
    public function it_can_be_created_from_Maps()
    {
        $this->assertInstanceOf(Iterator::class, ImmMap([1,2,3,4])->iterator());
    }

    /**
     * @test
     */
    public function it_is_countable_after_created_from_maps()
    {
        $this->assertCount(3, ImmMap([1,2,3])->iterator());
    }

    /**
     * @test
     */
    public function it_lets_you_access_map_elements_via_array_notation()
    {
        $this->assertIsLike(ImmMap([1,2,3])->iterator()[0], Some(1));
        $this->assertIsLike(ImmMap([1,2,3])->iterator()[1], Some(2));
        $this->assertIsLike(ImmMap([1,2,3])->iterator()[2], Some(3));
        $this->assertIsLike(ImmMap([1,2,3])->iterator()[3], None());

        $this->assertIsLike(ImmMap(["a" => 1,"b" => 2])->iterator()["a"], Some(1));

        $ob1 = new \stdClass();
        $ob2 = new \stdClass();
        $this->assertIsLike(ImmMap($ob1, 1, $ob2, 2)->iterator()[$ob1], Some(1));
    }

    /**
     * @test
     */
    public function it_is_immutable_after_created_from_maps()
    {
        $this->expectException(\TypeError::class);
        $map = ImmMap([1, 2, 3])->iterator();
        $map[42] = 32;
    }

    /**
     * @test
     */
    public function it_lets_you_check_if_a_map_key_exists()
    {
        $this->assertTrue(isset(ImmMap([1,2,3])->iterator()[2]));
        $this->assertFalse(isset(ImmMap([1,2,3])->iterator()[3]));

        // string key
        $this->assertTrue(isset(ImmMap(["a" => 1,"b" => 2])->iterator()["a"]));
        $this->assertFalse(isset(ImmMap(["a" => 1,"b" => 2])->iterator()["c"]));

        // object key
        $ob1 = new \stdClass();
        $ob2 = new \stdClass();
        $this->assertTrue(isset(ImmMap($ob1, 1, $ob2, 2)->iterator()[$ob1]));
    }

    /**
     * @test
     */
    public function it_is_foreachable_after_created_from_maps()
    {
        $dataProvider = [1, 2, 3];

        foreach (ImmMap([1, 2, 3])->iterator() as $v) {
            $this->assertEquals($v, current($dataProvider));
            next($dataProvider);
        }
    }

    /**
     * @test
     */
    public function it_is_foreachable_with_key_and_value_after_created_from_maps()
    {
        $map = [
            "a" => 1,
            "b" => 2,
            "c" => 3
        ];

        foreach (ImmMap($map)->iterator() as $k => $v) {
            $this->assertEquals($k, key($map));
            $this->assertEquals($v, current($map));
            next($map);
        }
    }

    /////////////////////
    // Sets            //
    /////////////////////

    /**
     * @test
     */
    public function it_can_be_created_from_Sets()
    {
        $this->assertInstanceOf(Iterator::class, ImmSet(1, 2, 3, 4)->iterator());
    }

    /**
     * @test
     */
    public function it_is_countable_after_created_from_sets()
    {
        $this->assertCount(3, ImmSet(1, 2, 3)->iterator());
        // expect(ImmSet(1, 2, 3)->iterator())->toHaveCount(3);
    }

    /**
     * @test
     */
    public function it_is_array_access_after_created_from_sets()
    {
        $this->assertInstanceOf(\ArrayAccess::class, ImmSet(1, 2, 3)->iterator());
        // expect(ImmSet(1, 2, 3)->iterator())->toHaveType(\ArrayAccess::class);
    }

    /**
     * @test
     */
    public function it_lets_you_access_sets_elements_via_array_notation()
    {
        $this->assertIsLike(ImmSet(1, 2, 3)->iterator()[0], Some(1));
        $this->assertIsLike(ImmSet(1, 2, 3)->iterator()[1], Some(2));
        $this->assertIsLike(ImmSet(1, 2, 3)->iterator()[2], Some(3));
        $this->assertIsLike(ImmSet(1, 2, 3)->iterator()[3], None());
    }

    /**
     * @test
     */
    public function it_is_immutable_after_created_from_sets()
    {
        $this->expectException(\TypeError::class);
        $set = ImmSet(1, 2, 3)->iterator();
        $set[42] = 32;
        // expect(ImmSet(1, 2, 3)->iterator())->toThrow()->duringOffsetSet(42);
        // expect(ImmSet(1, 2, 3)->iterator())->toThrow()->duringOffsetUnset();
    }

    /**
     * @test
     */
    public function it_lets_you_check_if_a_set_index_exists()
    {
        $this->assertTrue(isset(ImmSet(1, 2, 3)->iterator()[2]));
        $this->assertFalse(isset(ImmSet(1, 2, 3)->iterator()[3]));
    }

    /**
     * @test
     */
    public function it_is_foreachable_after_created_from_sets()
    {
        $dataProvider = [1, 2, 3];

        foreach (ImmSet(1, 2, 3)->iterator() as $v) {
            $this->assertEquals($v, current($dataProvider));
            next($dataProvider);
        }
    }

    /**
     * @test
     */
    public function it_is_foreachable_with_key_and_value_after_created_from_sets()
    {
        $dataProvider = [
            0, 1,
            1, 2,
            2, 3
        ];

        foreach (ImmSet(1, 2, 3)->iterator() as $k => $v) {
            $this->assertEquals($k, current($dataProvider));
            next($dataProvider);
            $this->assertEquals($v, current($dataProvider));
            next($dataProvider);
        }
    }
}
