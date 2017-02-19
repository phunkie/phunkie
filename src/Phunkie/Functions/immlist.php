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
    use Phunkie\Types\ImmList;

    const head = "\\Phunkie\\Functions\\immlist\\head";
    function head(ImmList $list)
    {
        return $list->head();
    }

    const init = "\\Phunkie\\Functions\\immlist\\init";
    function init(ImmList $list): ImmList
    {
        return $list->init();
    }

    const tail = "\\Phunkie\\Functions\\immlist\\tail";
    function tail(ImmList $list): ImmList
    {
        return $list->tail();
    }

    const last = "\\Phunkie\\Functions\\immlist\\last";
    function last(ImmList $list)
    {
        return $list->last();
    }

    const reverse = "\\Phunkie\\Functions\\immlist\\reverse";
    function reverse(ImmList $list): ImmList
    {
        return $list->reverse();
    }

    const length = "\\Phunkie\\Functions\\immlist\\length";
    function length(ImmList $list): int
    {
        return $list->length;
    }

    const concat = "\\Phunkie\\Functions\\immlist\\concat";
    function concat(...$items): ImmList
    {
        $result = [];
        foreach ($items as $item) {
            $result = !$item instanceof ImmList ? array_merge($result, [$item]) : array_merge($result, $item->toArray());
        }
        return ImmList(...$result);
    }

    const take = "\\Phunkie\\Functions\\immlist\\take";
    function take(int $n)
    {
        return applyPartially([$n],func_get_args(),function(ImmList $list) use ($n) {
            return ImmList(...array_slice($list->toArray(), 0, $n < 0 ? 0 : $n));
        });
    }

    const takeWhile = "\\Phunkie\\Functions\\immlist\\takeWhile";
    function takeWhile(callable $f)
    {
        return applyPartially([$f],func_get_args(),function(ImmList $list) use ($f) {
            return $list->takeWhile($f);
        });
    }

    const drop = "\\Phunkie\\Functions\\immlist\\drop";
    function drop(int $n)
    {
        return applyPartially([$n],func_get_args(),function(ImmList $list) use ($n) {
            return ImmList(...array_slice($list->toArray(), $n < 0 ? 0 : $n));
        });
    }

    const dropWhile = "\\Phunkie\\Functions\\immlist\\dropWhile";
    function dropWhile(callable $f)
    {
        return applyPartially([$f],func_get_args(),function(ImmList $list) use ($f) {
            return $list->dropWhile($f);
        });
    }

    const nth = "\\Phunkie\\Functions\\immlist\\nth";
    function nth(int $nth)
    {
        return applyPartially([$nth],func_get_args(),function(ImmList $list) use ($nth) {
            return array_key_exists($nth, $list->toArray()) ? Some($list->toArray()[$nth]) : None();
        });
    }

    const filter = "\\Phunkie\\Functions\\immlist\\filter";
    function filter($f)
    {
        return applyPartially([$f],func_get_args(),function(ImmList $list) use ($f) {
            return $list->filter($f);
        });
    }

    const reject = "\\Phunkie\\Functions\\immlist\\reject";
    function reject($f)
    {
        return applyPartially([$f],func_get_args(),function(ImmList $list) use ($f) {
            return $list->reject($f);
        });
    }

    const reduce = "\\Phunkie\\Functions\\immlist\\reduce";
    function reduce($f)
    {
        return applyPartially([$f],func_get_args(),function(ImmList $list) use ($f) {
            return $list->reduce($f);
        });
    }
}