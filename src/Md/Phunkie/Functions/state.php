<?php

namespace Md\Phunkie\Functions\state {

    use Md\Phunkie\Cats\State;

    /**
     * @return State<S,S>
     */
    function get()
    {
        return new State(function($s) { return Pair($s, $s); });
    }

    /**
     * @param callable<S,A> $f
     * @return State<S,A>
     */
    function gets(callable $f): State
    {
        return new State(function($s) use ($f) { return Pair($s, $f($s)); });
    }

    /**
     * @param S $s
     * @return State<S,Unit>
     */
    function put($s): State
    {
        return new State(function($ignore) use ($s) { return Pair($s, Unit()); });
    }

    /**
     * @param callable<S,S> $f
     * @return State<S,Unit>
     */
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
