<?php

namespace spec\Phunkie\Cats;

use Md\Unit\TestCase;
// use Phunkie\Cats\OptionT;
use Prophecy\Argument;

/**
 * @mixin \Phunkie\Cats\OptionT
 */
class OptionTSpec extends TestCase
{
    /**
     * @test
     */
    public function it_implements_map()
    {
        $m = OptionT(ImmList(Some(1), None(), Some(2)));
        $this->assertIsLike(
            $m->map(function ($x) {
                return $x + 1;
            }),
            OptionT(ImmList(Some(2), None(), Some(3)))
        );
    }

    /**
     * @test
     */
    public function it_implements_flatMap()
    {
        $m = OptionT(ImmList(Some(1), None(), Some(2)));
        $this->assertIsLike(
            $m->flatMap(function ($x) {
                return OptionT(ImmList(Some($x + 1)));
            }),
            OptionT(ImmList(Some(2), None(), Some(3)))
        );
    }

    /**
     * @test
     */
    public function it_implements_isDefined()
    {
        $m = OptionT(ImmList(Some(1), None(), Some(2)));
        $this->assertIsLike($m->isDefined(), ImmList(true, false, true));
    }

    /**
     * @test
     */
    public function it_immplements_isEmpty()
    {
        $m = OptionT(ImmList(Some(1), None(), Some(2)));
        $this->assertIsLike($m->isEmpty(), ImmList(false, true, false));
    }

    /**
     * @test
     */
    public function it_immplements_getOrElse()
    {
        $m = OptionT(ImmList(Some(1), None(), Some(2)));
        $this->assertIsLike($m->getOrElse(42), ImmList(1, 42, 2));
    }
}
