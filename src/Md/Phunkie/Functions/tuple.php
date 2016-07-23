<?php

use function Md\Phunkie\Functions\pattern_matching\matching;
use function Md\Phunkie\Functions\pattern_matching\on;
use Md\Phunkie\Types\Pair;
use Md\Phunkie\Types\Tuple;
use Md\Phunkie\Types\Unit;

function Tuple(...$values)
{
    return matching(count($values),
        on(0)->returns(new Unit()),
        on(2)->returns(new Pair($values[0], $values[1])),
        on(_)->returns(new Tuple(...$values))
    );
}