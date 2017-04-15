<?php

namespace spec\Phunkie\Functions;

use PhpSpec\ObjectBehavior;
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

class StringSpec extends ObjectBehavior
{
    function it_has_head()
    {
        expect(head('hello'))->toBe('h');
    }

    function it_has_init()
    {
        expect(init('hello'))->toBe('hell');
    }

    function it_has_tail()
    {
        expect(tail('hello'))->toBe('ello');
    }

    function it_has_last()
    {
        expect(last('hello'))->toBe('o');
    }

    function it_has_reverse()
    {
        expect(reverse("hello"))->toBe("olleh");
    }

    function it_has_length()
    {
        expect(length("hello"))->toBe(5);
    }

    function it_has_concat()
    {
        expect(concat('h', 3, "llo"))->toBe("h3llo");
    }

    function it_has_take()
    {
        expect((take(3))("hello"))->toBe("hel");
    }

    function it_has_takeWhile()
    {
        expect((takeWhile(function($char) { return $char != 'l'; }))("hello"))->toBe("he");
    }

    function it_has_drop()
    {
        expect((drop(3))("hello"))->toBe("lo");
    }

    function it_has_dropWhile()
    {
        expect((dropWhile(function($char) { return $char != 'l'; }))("hello"))->toBe("llo");
    }

    function it_has_nth()
    {
        expect((nth(4))("hello"))->toBeLike(Some("o"));
        expect((nth(6))("hello"))->toBeLike(None());
    }

    function it_has_filter()
    {
        expect((filter(function($c) { return $c == 'l';}))('hello'))->toBe('ll');
    }

    function it_has_reject()
    {
        expect((reject(function($c) { return $c == 'l';}))('hello'))->toBe('heo');
    }

    function it_has_reduce()
    {
        expect((reduce(function($a, $b) { return $a < $b ? $a : $b;}))('hello'))->toBe('e');
    }

    function it_has_lines()
    {
        expect(lines("hello
how are you
every thing ok"))->toBeLike(ImmList('hello', 'how are you', 'every thing ok'));

        expect(unlines(ImmList('hello', 'how are you', 'every thing ok')))->toBe(
            "hello
how are you
every thing ok"
        );
    }

    function it_has_words()
    {
        expect(words("hello how are you"))->toBeLike(ImmList('hello', 'how', 'are', 'you'));
        expect(unwords(ImmList('hello', 'how', 'are', 'you')))->toBe("hello how are you");
    }
}