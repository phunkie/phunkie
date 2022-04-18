<?php

namespace spec\Phunkie\Functions;

use PHPUnit\Framework\TestCase;
use function Phunkie\Functions\numbers\even;
use function Phunkie\Functions\numbers\negate;
use function Phunkie\Functions\numbers\odd;
use function Phunkie\Functions\numbers\increment;
use function Phunkie\Functions\numbers\decrement;
use function Phunkie\Functions\numbers\signum;

class NumbersSpec extends TestCase
{
    /**
     * @test
     */
    public function it_implements_even()
    {
        $this->assertEquals(even(1), false);
        $this->assertEquals(even(2), true);
    }

    /**
     * @test
     */
    public function it_implements_odd()
    {
        $this->assertEquals(odd(1), true);
        $this->assertEquals(odd(2), false);
    }

    /**
     * @test
     */
    public function it_implements_increment()
    {
        $this->assertEquals(increment(1), 2);
        $this->assertEquals(increment(2), 3);
    }

    /**
     * @test
     */
    public function it_implements_decrement()
    {
        $this->assertEquals(decrement(1), 0);
        $this->assertEquals(decrement(2), 1);
    }

    /**
     * @test
     */
    public function it_implements_negate()
    {
        $this->assertEquals(negate(1), -1);
        $this->assertEquals(negate(-1), 1);
    }

    /**
     * @test
     */
    public function it_implements_signum()
    {
        $this->assertEquals(signum(32), 1);
        $this->assertEquals(signum(0), 0);
        $this->assertEquals(signum(-32), -1);
    }
}
