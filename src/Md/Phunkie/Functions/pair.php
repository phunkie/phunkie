<?php
namespace {

    use Md\Phunkie\Types\Pair;

    function Pair(...$args)
    {
        return new Pair(...$args);
    }
}

namespace Md\Phunkie\Functions\pair {

    use Md\Phunkie\Types\Pair;

    const _1 = "\\Md\\Phunkie\\Functions\\pair\\_1";
    function _1(Pair $pair)
    {
        return $pair->_1;
    }

    const _2 = "\\Md\\Phunkie\\Functions\\pair\\_2";
    function _2(Pair $pair)
    {
        return $pair->_2;
    }
}