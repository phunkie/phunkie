<?php

namespace Phunkie\PatternMatching\Referenced {

    use Phunkie\Cats\Free\Bind;
    use Phunkie\Cats\Free\Pure;
    use Phunkie\Cats\Free\Suspend;

    function Pure(&$value)
    {
        return new GenericReferenced(Pure::class, $value);
    }

    function Suspend(&$value)
    {
        return new GenericReferenced(Suspend::class, $value);
    }

    function Bind(&$target, &$f)
    {
        return new GenericReferenced(Bind::class, $target, $f);
    }
}
