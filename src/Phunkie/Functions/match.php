<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace {

    use Phunkie\PatternMatching\Match;
    use Phunkie\PatternMatching\Underscore;

    function match(...$values)
    {
        return new Match(...$values);
    }

    function underscore() { return new Underscore(); }
}

namespace Phunkie\PatternMatching\Referenced {

    use Phunkie\Cats\Free\Pure;

    function ListWithTail(&$head, &$tail)
    {
        return new ListWithTail($head, $tail);
    }

    function ListNoTail(&$head, $tail)
    {
        return new ListNoTail($head, $tail);
    }

    function Some(&$value)
    {
        return new Some($value);
    }

    function Success(&$value)
    {
        return new Success($value);
    }

    function Failure(&$value)
    {
        return new Failure($value);
    }

    function Pure(&$value)
    {
        return new GenericReferenced(Pure::class, $value);
    }
}

namespace Phunkie\PatternMatching\Wildcarded {
    function ImmList($head, $tail)
    {
        if ($head == Nil) $head = Nil();
        if ($tail == Nil) $tail = Nil();
        return new ImmList($head, $tail);
    }
}