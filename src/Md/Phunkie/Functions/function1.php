<?php

namespace Md\Phunkie\Functions\function1 {

    function identity($x) {
        return $x;
    }

    const identity = "\\Md\\Phunkie\\Functions\\function1\\identity";
}

namespace {

    use Md\Phunkie\PatternMatching\Wildcarded\Function1 as WildcardedFunction1;
    use Md\Phunkie\Types\Function1;

    function Function1($f)
    {
        if ($f == _) {
            return new WildcardedFunction1();
        }
        return new Function1($f);
    }

}