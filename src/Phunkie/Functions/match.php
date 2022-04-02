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

    use Phunkie\PatternMatching\PMatch;
    use Phunkie\PatternMatching\Underscore;

    function pmatch(...$values)
    {
        return new PMatch(...$values);
    }

    function underscore()
    {
        return new Underscore();
    }
}

namespace Phunkie\PatternMatching\Referenced {

    use Phunkie\Validation\Success as Valid;
    use Phunkie\Validation\Failure as Invalid;

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
        return new GenericReferenced(Valid::class, $value);
    }

    function Failure(&$value)
    {
        return new GenericReferenced(Invalid::class, $value);
    }
}

namespace Phunkie\PatternMatching\Wildcarded {
    function ImmList($head, $tail)
    {
        if ($head == Nil) {
            $head = Nil();
        }
        if ($tail == Nil) {
            $tail = Nil();
        }
        return new ImmList($head, $tail);
    }
}
