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
    use Phunkie\Types\Cons;
    use Phunkie\Types\ImmList;
    use Phunkie\Types\Nil;
    use Phunkie\Types\NonEmptyList;

    function ImmList(...$values): ImmList { switch(count($values)) {
        case 0: return Nil();
        default: return new ImmList(...$values); }
    }

    function Nil(): ImmList
    {
        return new Nil();
    }

    function Cons($head, $tail)
    {
        if ($head == Nil) $head = Nil();
        if ($tail == Nil) $tail = Nil();
        return new Cons($head, $tail);
    }

    function Nel(...$values): NonEmptyList
    {
        return new NonEmptyList(...$values);
    }
}

namespace Phunkie\Functions\immlist {

    use function Phunkie\Functions\currying\applyPartially;
    use const Phunkie\Functions\function1\identity;
    use function Phunkie\Functions\immlist\local\assertListOrString;
    use const Phunkie\Functions\immlist\local\FIRST_ARGUMENT;
    use function Phunkie\Functions\immlist\local\formatError;
    use const Phunkie\Functions\immlist\local\SECOND_ARGUMENT;
    use Phunkie\Types\ImmList;
    use Phunkie\Types\ImmList\NoSuchElementException;
    use Phunkie\Validation\Failure;

    const head = "\\Phunkie\\Functions\\immlist\\head";
    function head($listOrString)
    {
        assertListOrString($listOrString, FIRST_ARGUMENT, head);

        if ($listOrString instanceof ImmList)
            return $listOrString->head();

        if (strlen($listOrString) != 0)
            return $listOrString[0];

        throw new NoSuchElementException("head of empty list");
    }

    const init = "\\Phunkie\\Functions\\immlist\\init";
    function init($listOrString)
    {
        assertListOrString($listOrString, FIRST_ARGUMENT, init);
        if ($listOrString instanceof ImmList)
            return $listOrString->init();

        if (strlen($listOrString))
            return substr($listOrString, 0, strlen($listOrString) - 1);

        throw new \BadMethodCallException("empty init");
    }

    const tail = "\\Phunkie\\Functions\\immlist\\tail";
    function tail($listOrString)
    {
        assertListOrString($listOrString, FIRST_ARGUMENT, tail);
        if ($listOrString instanceof ImmList)
            return $listOrString->tail();

        if (strlen($listOrString) > 1)
            return substr($listOrString, 1);
        if (strlen($listOrString) == 0)
            throw new \BadMethodCallException("tail of empty list");

        return "";
    }

    const last = "\\Phunkie\\Functions\\immlist\\last";
    function last($listOrString)
    {
        assertListOrString($listOrString, FIRST_ARGUMENT, last);
        if ($listOrString instanceof ImmList)
            return $listOrString->last();

        if (strlen($listOrString) == 0)
            throw new NoSuchElementException("last of empty list");

        return $listOrString[strlen($listOrString) - 1];
    }

    const reverse = "\\Phunkie\\Functions\\immlist\\reverse";
    function reverse($listOrString)
    {
        assertListOrString($listOrString, FIRST_ARGUMENT, reverse);
        return $listOrString instanceof ImmList ? $listOrString->reverse() : strrev($listOrString);
    }

    const length = "\\Phunkie\\Functions\\immlist\\length";
    function length($listOrString): int
    {
        assertListOrString($listOrString, FIRST_ARGUMENT, length);
        return $listOrString instanceof ImmList ? $listOrString->length : strlen($listOrString);
    }

    const concat = "\\Phunkie\\Functions\\immlist\\concat";
    function concat(...$items)
    {
        $concatLists = function(...$lists) {
            $result = [];
            foreach ($lists as $item) {
                $result = !$item instanceof ImmList ? array_merge($result, [$item]) : array_merge($result, $item->toArray());
            }
            return ImmList(...$result);
        };
        $concatStrings = function(...$s) { return array_reduce($s, function($a, $b) { return $a . $b; }, ""); };
        if ($items[0] instanceof ImmList || is_array($items[0]))
            return $concatLists(...$items);
        return $concatStrings(...$items);
    }

    const take = "\\Phunkie\\Functions\\immlist\\take";
    function take(int $n)
    {
        return applyPartially([$n],func_get_args(),function($listOrString) use ($n) {
            assertListOrString($listOrString, SECOND_ARGUMENT, take);
            if (is_string($listOrString)) {
                return substr($listOrString, 0, $n < 0 ? 0 : $n);
            }
            return ImmList(...array_slice($listOrString->toArray(), 0, $n < 0 ? 0 : $n));
        });
    }

    const takeWhile = "\\Phunkie\\Functions\\immlist\\takeWhile";
    function takeWhile(callable $f)
    {
        return applyPartially([$f],func_get_args(),function($listOrString) use ($f) {
            assertListOrString($listOrString, SECOND_ARGUMENT, takeWhile);
            if (is_string($listOrString)) {
                return ImmList(...str_split($listOrString))->takeWhile($f)->mkString("");
            }
            return $listOrString->takeWhile($f);
        });
    }

    const drop = "\\Phunkie\\Functions\\immlist\\drop";
    function drop(int $n)
    {
        return applyPartially([$n],func_get_args(),function($listOrString) use ($n) {
            assertListOrString($listOrString, SECOND_ARGUMENT, drop);
            if (is_string($listOrString)) {
                return substr($listOrString, $n < 0 ? 0 : $n);
            }
            return ImmList(...array_slice($listOrString->toArray(), $n < 0 ? 0 : $n));
        });
    }

    const dropWhile = "\\Phunkie\\Functions\\immlist\\dropWhile";
    function dropWhile(callable $f)
    {
        return applyPartially([$f],func_get_args(),function($listOrString) use ($f) {
            assertListOrString($listOrString, SECOND_ARGUMENT, dropWhile);
            if (is_string($listOrString)) {
                return ImmList(...str_split($listOrString))->dropWhile($f)->mkString("");
            }
            return $listOrString->dropWhile($f);
        });
    }

    const nth = "\\Phunkie\\Functions\\immlist\\nth";
    function nth(int $nth)
    {
        return applyPartially([$nth],func_get_args(),function($listOrString) use ($nth) {
            assertListOrString($listOrString, SECOND_ARGUMENT, nth);
            if (is_string($listOrString)) {
                return $nth > strlen($listOrString) - 1 ? None() : Some($listOrString[$nth]);
            }
            return array_key_exists($nth, $listOrString->toArray()) ? Some($listOrString->toArray()[$nth]) : None();
        });
    }

    const filter = "\\Phunkie\\Functions\\immlist\\filter";
    function filter($f)
    {
        return applyPartially([$f],func_get_args(),function($listOrString) use ($f) {
            assertListOrString($listOrString, SECOND_ARGUMENT, filter);
            if (is_string($listOrString)) {
                return ImmList(...str_split($listOrString))->filter($f)->mkString("");
            }
            return $listOrString->filter($f);
        });
    }

    const reject = "\\Phunkie\\Functions\\immlist\\reject";
    function reject($f)
    {
        return applyPartially([$f],func_get_args(),function($listOrString) use ($f) {
            assertListOrString($listOrString, SECOND_ARGUMENT, reject);
            if (is_string($listOrString)) {
                return ImmList(...str_split($listOrString))->reject($f)->mkString("");
            }
            return $listOrString->reject($f);
        });
    }

    const reduce = "\\Phunkie\\Functions\\immlist\\reduce";
    function reduce($f)
    {
        return applyPartially([$f],func_get_args(),function($listOrString) use ($f) {
            assertListOrString($listOrString, SECOND_ARGUMENT, reduce);
            if (is_string($listOrString)) {
                return ImmList(...str_split($listOrString))->reduce($f);
            }
            return $listOrString->reduce($f);
        });
    }

    const transpose = "\\Phunkie\\Functions\\immlist\\transpose";
    function transpose(ImmList $list)
    {
        return $list->transpose();
    }
}

namespace Phunkie\Functions\immlist\local {

    use function Phunkie\Functions\type\normaliseType;
    use Phunkie\Types\ImmList;
    const FIRST_ARGUMENT = 1;
    const SECOND_ARGUMENT = 2;
    const THIRD_ARGUMENT = 2;

    function assertListOrString($list, $argument, $functionCalled)
    {
        if ($list instanceof ImmList || is_string($list)) {
            return;
        }
        formatError("Argument %s passed to %s must be an instance of ImmList or a String, " .
            (gettype($list) == 'object' ? get_class($list) : normaliseType(gettype($list))) . " given",
            $argument, $functionCalled);
    }

    function formatError($error, $argumentNumber, $functionCalled)
    {
        throw new \TypeError(sprintf($error, $argumentNumber, $functionCalled));
    }
}