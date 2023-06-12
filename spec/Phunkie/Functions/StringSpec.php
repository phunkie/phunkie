<?php

namespace spec\Phunkie\Functions;

use PHPUnit\Framework\TestCase;
use function Phunkie\Functions\immlist\concat;
use function Phunkie\Functions\immlist\drop;
use function Phunkie\Functions\immlist\dropWhile;
use function Phunkie\Functions\immlist\filter;
use function Phunkie\Functions\immlist\head;
use function Phunkie\Functions\immlist\init;
use function Phunkie\Functions\immlist\last;
use function Phunkie\Functions\immlist\length;
use function Phunkie\Functions\immlist\nth;
use function Phunkie\Functions\immlist\reduce;
use function Phunkie\Functions\immlist\reject;
use function Phunkie\Functions\immlist\reverse;
use function Phunkie\Functions\immlist\tail;
use function Phunkie\Functions\immlist\take;
use function Phunkie\Functions\immlist\takeWhile;
use function Phunkie\Functions\string\lines;
use function Phunkie\Functions\string\unlines;
use function Phunkie\Functions\string\unwords;
use function Phunkie\Functions\string\words;

class StringSpec extends TestCase
{
    /**
     * @test
     */
    public function it_has_head()
    {
        $this->assertEquals(head('hello'), 'h');
    }

    /**
     * @test
     */
    public function it_has_init()
    {
        $this->assertEquals(init('hello'), 'hell');
    }

    /**
     * @test
     */
    public function it_has_tail()
    {
        $this->assertEquals(tail('hello'), 'ello');
    }

    /**
     * @test
     */
    public function it_has_last()
    {
        $this->assertEquals(last('hello'), 'o');
    }

    /**
     * @test
     */
    public function it_has_reverse()
    {
        $this->assertEquals(reverse("hello"), "olleh");
    }

    /**
     * @test
     */
    public function it_has_length()
    {
        $this->assertEquals(length("hello"), 5);
    }

    /**
     * @test
     */
    public function it_has_concat()
    {
        $this->assertEquals(concat('h', 3, "llo"), "h3llo");
    }

    /**
     * @test
     */
    public function it_has_take()
    {
        $this->assertEquals((take(3))("hello"), "hel");
    }

    /**
     * @test
     */
    public function it_has_takeWhile()
    {
        $this->assertEquals((takeWhile(fn ($char) => $char != 'l'))("hello"), "he");
    }

    /**
     * @test
     */
    public function it_has_drop()
    {
        $this->assertEquals((drop(3))("hello"), "lo");
    }

    /**
     * @test
     */
    public function it_has_dropWhile()
    {
        $this->assertEquals((dropWhile(fn ($char) => $char != 'l'))("hello"), "llo");
    }

    /**
     * @test
     */
    public function it_has_nth()
    {
        $this->assertTrue((nth(4))("hello") == Some("o"));
        $this->assertTrue((nth(6))("hello") == None());
    }

    /**
     * @test
     */
    public function it_has_filter()
    {
        $this->assertEquals((filter(fn ($c) => $c == 'l'))('hello'), 'll');
    }

    /**
     * @test
     */
    public function it_has_reject()
    {
        $this->assertEquals((reject(fn ($c) => $c == 'l'))('hello'), 'heo');
    }

    /**
     * @test
     */
    public function it_has_reduce()
    {
        $this->assertEquals((reduce(fn ($a, $b) => $a < $b ? $a : $b))('hello'), 'e');
    }

    /**
     * @test
     */
    public function it_has_lines()
    {
        $this->assertTrue(lines("hello
how are you
every thing ok") == ImmList('hello', 'how are you', 'every thing ok'));

        $this->assertEquals(
            unlines(ImmList('hello', 'how are you', 'every thing ok')),
            "hello
how are you
every thing ok"
        );
    }

    /**
     * @test
     */
    public function it_has_words()
    {
        $this->assertTrue(words("hello how are you") == ImmList('hello', 'how', 'are', 'you'));
        $this->assertEquals(unwords(ImmList('hello', 'how', 'are', 'you')), "hello how are you");
    }
}
