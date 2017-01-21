<?php

namespace spec\Md\Phunkie\Types;

use function Md\Phunkie\Functions\function1\compose;
use function Md\Phunkie\Functions\tuple\assign;
use PhpSpec\ObjectBehavior;

class TupleSpec extends ObjectBehavior
{
    function it_lets_you_assign_the_return_values()
    {
        $name = $gender = $age = null;
        (compose(assign($name, $gender, $age)))(Tuple("Luigi", "male", 23));
        expect($name)->toBe("Luigi");
        expect($gender)->toBe("male");
        expect($age)->toBe(23);

        $name = $age = null;
        (compose(assign($name, $age)))(Pair("Luigi", 23));
        expect($name)->toBe("Luigi");
        expect($age)->toBe(23);
    }
}