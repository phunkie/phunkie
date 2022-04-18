<?php

namespace spec\Phunkie\Cats;

use Phunkie\Cats\Id;
use Md\Unit\TestCase;

class IdSpec extends TestCase
{
    /**
     * @test
     */
    public function it_implements_map()
    {
        $f = new Id(42);
        $increment = function ($x) {
            return $x + 1;
        };
        $this->assertEquals(43, $f->map($increment));
    }

    /**
     * @test
     */
    public function it_implements_flatMap()
    {
        $this->assertIsLike(
            new Id(43),
            (new Id(42))->map(function ($x) {
                return new Id($x + 1);
            })
        );
    }

    /**
     * @test
     */
    public function it_implements_andThen()
    {
        $f = new Id("a");
        $this->assertEquals("ab", $f->andThen("b"));
    }

    /**
     * @test
     */
    public function it_implements_compose()
    {
        $f = new Id("a");
        $this->assertEquals("ba", $f->compose("b"));
    }
}
