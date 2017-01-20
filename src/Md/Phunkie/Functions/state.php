<?php

namespace Md\Phunkie\Functions\state {

    use Md\Phunkie\Cats\State;

    const get = "\\Md\\Phunkie\\Functions\\state\\get";
    function get()
    {
        return new State(function($s) { return Pair($s, $s); });
    }

    const gets = "\\Md\\Phunkie\\Functions\\state\\gets";
    function gets(callable $f): State
    {
        return new State(function($s) use ($f) { return Pair($s, $f($s)); });
    }

    const put = "\\Md\\Phunkie\\Functions\\state\\put";
    function put($s): State
    {
        return new State(function($ignore) use ($s) { return Pair($s, Unit()); });
    }

    const modify = "\\Md\\Phunkie\\Functions\\state\\modify";
    function modify(callable $f)
    {
        return new State(function($s) use ($f) { return Pair($f($s), Unit()); });
    }
}

namespace {

    use Md\Phunkie\Cats\State;

    function State($a)
    {
        return new State(function ($s) use ($a) {
            return Pair($s, $a);
        });
    }

}
