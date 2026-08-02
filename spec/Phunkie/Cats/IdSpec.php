<?php

namespace spec\Phunkie\Cats;

use Phunkie\Cats\Id;
use Md\Unit\TestCase;
use PHPUnit\Framework\Attributes\Test;

class IdSpec extends TestCase
{
    #[Test]
    public function it_implements_map()
    {
        $f = new Id(42);
        $increment = fn ($x) => $x + 1;
        $this->assertEquals(43, $f->map($increment));
    }

    #[Test]
    public function it_implements_flatMap()
    {
        $this->assertEquals(
            43,
            (new Id(42))->flatMap(fn ($x) => new Id($x + 1))
        );
    }

    #[Test]
    public function it_implements_andThen()
    {
        $f = new Id("a");
        $this->assertEquals("ab", $f->andThen("b"));
    }

    #[Test]
    public function it_implements_compose()
    {
        $f = new Id("a");
        $this->assertEquals("ba", $f->compose("b"));
    }
}
