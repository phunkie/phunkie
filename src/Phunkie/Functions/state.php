<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Functions\state {

    use Phunkie\Cats\State;

    const get = "\\Phunkie\\Functions\\state\\get";
    function get()
    {
        return new State(function ($s) {
            return Pair($s, $s);
        });
    }

    const gets = "\\Phunkie\\Functions\\state\\gets";
    function gets(callable $f): State
    {
        return new State(function ($s) use ($f) {
            return Pair($s, $f($s));
        });
    }

    const put = "\\Phunkie\\Functions\\state\\put";
    function put($s): State
    {
        return new State(function ($ignore) use ($s) {
            return Pair($s, Unit());
        });
    }

    const modify = "\\Phunkie\\Functions\\state\\modify";
    function modify(callable $f)
    {
        return new State(function ($s) use ($f) {
            return Pair($f($s), Unit());
        });
    }
}

namespace {

    use Phunkie\Cats\State;

    function State($a)
    {
        return new State(function ($s) use ($a) {
            return Pair($s, $a);
        });
    }

}
