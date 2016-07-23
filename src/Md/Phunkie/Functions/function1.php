<?php

namespace {

    use Md\Phunkie\Functions\function1\WildcardedFunction1;
    use Md\Phunkie\Types\Function1;

    function Function1($f)
    {
        if ($f == _) {
            return new WildcardedFunction1();
        }
        return new Function1($f);
    }

}

namespace Md\Phunkie\Functions\function1 {
    class WildcardedFunction1 {}
}