<?php

namespace {

    use Md\Phunkie\PatternMatching\Match;

    function match(...$values)
    {
        return new Match(...$values);
    }
}

namespace Md\Phunkie\PatternMatching\Referenced {
    function _Cons(&$head, &$tail)
    {
        return new Cons($head, $tail);
    }

    function _ConsX(&$head, $tail)
    {
        return new ConsX($head, $tail);
    }

    function _ConsXs($head, &$tail)
    {
        return new ConsXs($head, $tail);
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
}

namespace Md\Phunkie\PatternMatching\Wildcarded {
    function _Cons($head, $tail)
    {
        if ($head == Nil) $head = Nil();
        if ($tail == Nil) $tail = Nil();
        return new Cons($head, $tail);
    }
}