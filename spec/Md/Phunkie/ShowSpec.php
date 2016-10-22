<?php

namespace spec\Md\Phunkie;

use function Md\Phunkie\Functions\show\get_type_to_show;
use function Md\Phunkie\Functions\show\get_value_to_show;
use PhpSpec\ObjectBehavior;
use stdClass;

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
        expect(get_type_to_show(["foo"=>"bar"]))->toReturn("Array<String, String>");
        expect(get_type_to_show(function(){}))->toReturn("Callable");

        expect(get_type_to_show(Unit()))->toReturn("Unit");
        expect(get_type_to_show(None()))->toReturn("None");
        expect(get_type_to_show(Some(42)))->toReturn("Option<Int>");
        expect(get_type_to_show(ImmList(1,2,3)))->toReturn("List<Int>");
        expect(get_type_to_show(ImmList(Some(1),Some(2),Some(3))))->toReturn("List<Option<Int>>");
        expect(get_type_to_show(Nil()))->toReturn("List<Nothing>");
        expect(get_type_to_show(ImmList(1,2,"f")))->toReturn("List<Mixed>");
        expect(get_type_to_show(ImmList(Some(32), Some(56), None())))->toReturn("List<Option<Int>>");
        expect(get_type_to_show(Function1(function(int $x):bool { return $x == 42; })))->toReturn("Function1");

        expect(get_type_to_show(Pair(42, "42")))->toReturn("(Int, String)");

        expect(get_type_to_show(Success("yay")))->toReturn("Validation<E, String>");
        expect(get_type_to_show(Failure(new \Exception())))->toReturn("Validation<Exception, A>");

        expect(get_type_to_show(Tuple(1,true,"")))->toReturn("(Int, Boolean, String)");

        expect(get_type_to_show(new class{}))->toReturn("AnonymousClass");
        expect(get_type_to_show(new class extends SomeSuperClass {}))->toReturn(SomeSuperClass::class);
    }

    function it_prints_value()
    {
        expect(get_value_to_show(1))->shouldReturn(1);
        expect(get_value_to_show("1"))->shouldReturn('"1"');
        expect(get_value_to_show(true))->shouldReturn("true");
        expect(get_value_to_show(null))->shouldReturn("null");
        expect(get_value_to_show([1,2,3]))->shouldReturn("[1, 2, 3]");
        expect(get_value_to_show(function(int $x):bool { return $x == 42; }))->shouldReturn("<function>");

        expect(get_value_to_show(Some(42)))->shouldReturn(Some(42)->show());
        expect(get_value_to_show(ImmList(42)))->shouldReturn(ImmList(42)->show());
        expect(get_value_to_show(Function1(function(int $x):bool { return $x == 42; })))
            ->shouldReturn(Function1(function(int $x):bool { return $x == 42; })->show());
        expect(get_value_to_show(Success(42)))->shouldReturn(Success(42)->show());
        expect(get_value_to_show(Failure(42)))->shouldReturn(Failure(42)->show());
        expect(get_value_to_show(Tuple(42)))->shouldReturn(Tuple(42)->show());

        $object = new stdClass();
        expect(get_value_to_show($object))->toReturn(get_class($object) . "@" . $this->hash($object));

        $object = new class{};
        expect(get_value_to_show($object))->toReturn("anonymous" . "@" . $this->hash($object));

        $object = new class extends SomeSuperClass {};
        expect(get_value_to_show($object))->toReturn(SomeSuperClass::class . "@" . $this->hash($object));
    }

    private function hash($object)
    {
        return substr(ltrim(spl_object_hash($object), "0"), 0, 8);
    }
}

class SomeSuperClass {

}