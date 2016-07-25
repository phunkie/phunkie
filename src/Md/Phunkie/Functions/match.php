<?php

namespace {

    use Md\Phunkie\PatternMatching\Match;

    function match($value)
    {
        return new Match($value);
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
}

namespace Md\Phunkie\PatternMatching\Wildcarded {
    function _Cons($head, $tail)
    {
        if ($head == Nil) $head = Nil();
        if ($tail == Nil) $tail = Nil();
        return new Cons($head, $tail);
    }
}