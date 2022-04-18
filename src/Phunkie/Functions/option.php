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

    use Phunkie\Types\None;
    use Phunkie\Types\Option;
    use Phunkie\Types\Some;

    function Option($t)
    {
        return $t === null ? None() : Some($t);
    }

    function Some(...$t): Option
    {
        return Some::instance(...$t);
    }

    function None(): Option
    {
        return None::instance();
    }
}

namespace Phunkie\Functions\option {

    use Phunkie\Types\ImmList;
    use Phunkie\Types\Option;

    const isDefined = "\\Phunkie\\Functions\\option\\isDefined";
    const isJust = "\\Phunkie\\Functions\\option\\isDefined";
    const isSome = "\\Phunkie\\Functions\\option\\isDefined";
    function isDefined(Option $x): bool
    {
        return $x->isDefined();
    }

    const isNone = "\\Phunkie\\Functions\\option\\isNone";
    const isNothing = "\\Phunkie\\Functions\\option\\isNone";
    const isEmpty = "\\Phunkie\\Functions\\option\\isNone";
    function isNone(Option $x): bool
    {
        return $x->isEmpty();
    }

    const fromSome = "\\Phunkie\\Functions\\option\\fromSome";
    const fromJust = "\\Phunkie\\Functions\\option\\fromSome";
    function fromSome(Option $x)
    {
        if (isNone($x)) {
            throw new \Error("Can not get a value from None.");
        }
        return $x->get();
    }

    const listToOption = "\\Phunkie\\Functions\\option\\listToOption";
    function listToOption(ImmList $xs): Option
    {
        return $xs->isEmpty() ? None() : Some($xs->head);
    }

    const optionToList = "\\Phunkie\\Functions\\option\\optionToList";
    function optionToList(Option $x): ImmList
    {
        return $x->isEmpty() ? Nil() : ImmList($x->get());
    }
}
