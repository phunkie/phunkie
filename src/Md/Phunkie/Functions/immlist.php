<?php

namespace {

    use Md\Phunkie\Types\ImmList;

    function ImmList(...$values): ImmList
    {
        return new ImmList(...$values);
    }
}

namespace Md\Phunkie\Functions\immlist {

    use Md\Phunkie\Types\ImmList;

    function head(ImmList $list)
    {
        return $list->head();
    }

    function init(ImmList $list): ImmList
    {
        return $list->init();
    }

    function tail(ImmList $list): ImmList
    {
        return $list->tail();
    }

    function last(ImmList $list)
    {
        return $list->last();
    }

    function reverse(ImmList $list): ImmList
    {
        return $list->reverse();
    }

    function length(ImmList $list): int
    {
        return $list->length;
    }

    function concat(...$items): ImmList
    {
        $result = [];
        foreach ($items as $item) {
            if (!$item instanceof ImmList) {
                $result[] = $item;
            } else {
                $result = array_merge($result, $item->toArray());
            }
        }
        return ImmList(...$result);
    }
}