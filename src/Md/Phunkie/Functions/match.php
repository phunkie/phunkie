<?php

namespace {

    use Md\Phunkie\PatternMatching\Match;

    function match(...$values)
    {
        return new Match(...$values);
    }
}

namespace Md\Phunkie\PatternMatching\Referenced {
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
}

namespace Md\Phunkie\PatternMatching\Wildcarded {
    function ImmList($head, $tail)
    {
        if ($head == Nil) $head = Nil();
        if ($tail == Nil) $tail = Nil();
        return new ImmList($head, $tail);
    }
}