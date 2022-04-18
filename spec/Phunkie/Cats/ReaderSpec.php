<?php

namespace spec\Phunkie\Cats;

use PHPUnit\Framework\TestCase;
use Phunkie\Cats\Reader;
use Prophecy\Argument;

class ReaderSpec extends TestCase
{
    /**
     * @test
     */
    public function it_wraps_a_function()
    {
        $r = new Reader(function (string $a) {
            return strrev($a);
        });
        $this->assertEquals("olleh", $r->run("hello"));
    }

    /**
     * @test
     */
    public function it_implements_map()
    {
        $r = new Reader(function (string $a) {
            return strrev($a);
        });
        $this->assertEquals(
            "OLLEH",
            $r
                ->map(function (string $a) {
                    return strtoupper($a);
                })
                ->run("hello")
        );
    }
}
