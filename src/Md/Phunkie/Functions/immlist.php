<?php

namespace {
    use Md\Phunkie\Types\Cons;
    use Md\Phunkie\Types\ImmList;
    use Md\Phunkie\Types\Nil;
    use Md\Phunkie\Types\NonEmptyList;

    function ImmList(...$values): ImmList { switch(count($values)) {
        case 0: return Nil();
        case 1: return new Cons($values[0], Nil());
        default: return new Cons($values[0], ImmList(...array_slice($values, 1))); }
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

namespace Md\Phunkie\Functions\immlist {

    use function Md\Phunkie\Functions\currying\applyPartially;
    use Md\Phunkie\Types\ImmList;

    const head = "\\Md\\Phunkie\\Functions\\immlist\\head";
    function head(ImmList $list)
    {
        return $list->head();
    }

    const init = "\\Md\\Phunkie\\Functions\\immlist\\init";
    function init(ImmList $list): ImmList
    {
        return $list->init();
    }

    const tail = "\\Md\\Phunkie\\Functions\\immlist\\tail";
    function tail(ImmList $list): ImmList
    {
        return $list->tail();
    }

    const last = "\\Md\\Phunkie\\Functions\\immlist\\last";
    function last(ImmList $list)
    {
        return $list->last();
    }

    const reverse = "\\Md\\Phunkie\\Functions\\immlist\\reverse";
    function reverse(ImmList $list): ImmList
    {
        return $list->reverse();
    }

    const length = "\\Md\\Phunkie\\Functions\\immlist\\length";
    function length(ImmList $list): int
    {
        return $list->length;
    }

    const concat = "\\Md\\Phunkie\\Functions\\immlist\\concat";
    function concat(...$items): ImmList
    {
        $result = [];
        foreach ($items as $item) {
            $result = !$item instanceof ImmList ? array_merge($result, [$item]) : array_merge($result, $item->toArray());
        }
        return ImmList(...$result);
    }

    const take = "\\Md\\Phunkie\\Functions\\immlist\\take";
    function take(int $n)
    {
        return applyPartially([$n],func_get_args(),function(ImmList $list) use ($n) {
            return ImmList(...array_slice($list->toArray(), 0, $n < 0 ? 0 : $n));
        });
    }

    const drop = "\\Md\\Phunkie\\Functions\\immlist\\drop";
    function drop(int $n)
    {
        return applyPartially([$n],func_get_args(),function(ImmList $list) use ($n) {
            return ImmList(...array_slice($list->toArray(), $n < 0 ? 0 : $n));
        });
    }

    const nth = "\\Md\\Phunkie\\Functions\\immlist\\nth";
    function nth(int $nth)
    {
        return applyPartially([$nth],func_get_args(),function(ImmList $list) use ($nth) {
            return array_key_exists($nth, $list->toArray()) ? Some($list->toArray()[$nth]) : None();
        });
    }

    const filter = "\\Md\\Phunkie\\Functions\\immlist\\filter";
    function filter($f)
    {
        return applyPartially([$f],func_get_args(),function(ImmList $list) use ($f) {
            return $list->filter($f);
        });
    }

    const reject = "\\Md\\Phunkie\\Functions\\immlist\\reject";
    function reject($f)
    {
        return applyPartially([$f],func_get_args(),function(ImmList $list) use ($f) {
            return $list->reject($f);
        });
    }

    const reduce = "\\Md\\Phunkie\\Functions\\immlist\\reduce";
    function reduce($f)
    {
        return applyPartially([$f],func_get_args(),function(ImmList $list) use ($f) {
            return $list->reduce($f);
        });
    }
}