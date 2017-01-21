<?php

namespace Md\Phunkie\Functions\function1 {

    use function Md\Phunkie\Functions\semigroup\combine;
    const identity = "\\Md\\Phunkie\\Functions\\function1\\identity";
    function identity($x) {
        return $x;
    }

    const compose = "\\Md\\Phunkie\\Functions\\function1\\compose";
    function compose(callable ...$fs) {
        return combine(...array_map(function($f) {return Function1($f);}, array_reverse($fs)));
    }

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