<?php

namespace spec\Md\Phunkie;

use function Md\Phunkie\Functions\show\get_type_to_show;
use PhpSpec\ObjectBehavior;

class ShowSpec extends ObjectBehavior
{
    function it_prints_types()
    {
        expect(get_type_to_show(1))->toReturn("Int");
        expect(get_type_to_show("1"))->toReturn("String");
        expect(get_type_to_show(27.23))->toReturn("Double");
        expect(get_type_to_show(null))->toReturn("Null");
        expect(get_type_to_show(true))->toReturn("Boolean");
        expect(get_type_to_show(STDIN))->toReturn("Resource");
        expect(get_type_to_show([1,2,3]))->toReturn("Array<Int>");
        expect(get_type_to_show([]))->toReturn("Array<Nothing>");
        expect(get_type_to_show(function(){}))->toReturn("Callable");

        expect(get_type_to_show(Unit()))->toReturn("Unit");
        expect(get_type_to_show(None()))->toReturn("None");
        expect(get_type_to_show(Some(42)))->toReturn("Option<Int>");
        expect(get_type_to_show(ImmList(1,2,3)))->toReturn("List<Int>");
        expect(get_type_to_show(ImmList(Some(1),Some(2),Some(3))))->toReturn("List<Option<Int>>");
        expect(get_type_to_show(Nil()))->toReturn("List<Nothing>");
        expect(get_type_to_show(ImmList(1,2,"f")))->toReturn("List<Mixed>");
        expect(get_type_to_show(ImmList(Some(32), Some(56), None())))->toReturn("List<Option<Int>>");

        expect(get_type_to_show(Pair(42, "42")))->toReturn("(Int, String)");

        expect(get_type_to_show(Success("yay")))->toReturn("Validation<E, String>");
        expect(get_type_to_show(Failure(new \Exception())))->toReturn("Validation<Exception, A>");

        expect(get_type_to_show(Tuple(1,true,"")))->toReturn("(Int, Boolean, String)");
    }
}