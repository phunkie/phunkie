<?php

use function Phunkie\Functions\currying\curry;
use function Phunkie\Functions\currying\uncurry;
use const Phunkie\Functions\immlist\take;

function curry_examples()
{
    $implode = curry('implode');
    $implodeColon = $implode(":");
    printLn($implodeColon(["a", "b", "c"]));

    $take = uncurry(take);
    $list = $take(2, ImmList(1, 2, 3));
    printLn($list);
}
