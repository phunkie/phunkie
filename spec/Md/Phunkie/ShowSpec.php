<?php

namespace spec\Md\Phunkie;

use function Md\Phunkie\Functions\show\showType;
use function Md\Phunkie\Functions\show\showValue;
use PhpSpec\ObjectBehavior;
use stdClass;

class ShowSpec extends ObjectBehavior
{
    function it_prints_types()
    {
        expect(showType(1))->toReturn("Int");
        expect(showType("1"))->toReturn("String");
        expect(showType(27.23))->toReturn("Double");
        expect(showType(null))->toReturn("Null");
        expect(showType(true))->toReturn("Boolean");
        expect(showType(STDIN))->toReturn("Resource");
        expect(showType([1,2,3]))->toReturn("Array<Int>");
        expect(showType([]))->toReturn("Array<Nothing>");
        expect(showType(["foo"=>"bar"]))->toReturn("Array<String, String>");
        expect(showType(function(){}))->toReturn("Callable");

        expect(showType(Unit()))->toReturn("Unit");
        expect(showType(None()))->toReturn("None");
        expect(showType(Some(42)))->toReturn("Option<Int>");
        expect(showType(ImmList(1,2,3)))->toReturn("List<Int>");
        expect(showType(ImmList(Some(1),Some(2),Some(3))))->toReturn("List<Option<Int>>");
        expect(showType(Nil()))->toReturn("List<Nothing>");
        expect(showType(ImmList(1,2,"f")))->toReturn("List<Mixed>");
        expect(showType(ImmList(Some(32), Some(56), None())))->toReturn("List<Option<Int>>");
        expect(showType(Function1(function(int $x):bool { return $x == 42; })))->toReturn("Function1");

        expect(showType(Pair(42, "42")))->toReturn("(Int, String)");

        expect(showType(Success("yay")))->toReturn("Validation<E, String>");
        expect(showType(Failure(new \Exception())))->toReturn("Validation<Exception, A>");

        expect(showType(Tuple(1,true,"")))->toReturn("(Int, Boolean, String)");

        expect(showType(new class{}))->toReturn("AnonymousClass");
        expect(showType(new class extends SomeSuperClass {}))->toReturn("AnonymousClass<" . SomeSuperClass::class . ">");
    }

    function it_prints_value()
    {
        expect(showValue(1))->shouldReturn("1");
        expect(showValue("1"))->shouldReturn('"1"');
        expect(showValue(true))->shouldReturn("true");
        expect(showValue(null))->shouldReturn("null");
        expect(showValue([1,2,3]))->shouldReturn("[1, 2, 3]");
        expect(showValue(function(int $x):bool { return $x == 42; }))->shouldReturn("<function>");

        expect(showValue(Some(42)))->shouldReturn(Some(42)->show());
        expect(showValue(ImmList(42)))->shouldReturn(ImmList(42)->show());
        expect(showValue(Function1(function(int $x):bool { return $x == 42; })))
            ->shouldReturn(Function1(function(int $x):bool { return $x == 42; })->show());
        expect(showValue(Success(42)))->shouldReturn(Success(42)->show());
        expect(showValue(Failure(42)))->shouldReturn(Failure(42)->show());
        expect(showValue(Tuple(42)))->shouldReturn(Tuple(42)->show());

        $object = new stdClass();
        expect(showValue($object))->toReturn(get_class($object) . "@" . $this->hash($object));

        $object = new class{};
        expect(showValue($object))->toReturn("anonymous" . "@" . $this->hash($object));

        $object = new class extends SomeSuperClass {};
        expect(showValue($object))->toReturn(SomeSuperClass::class . "@" . $this->hash($object));
    }

    private function hash($object)
    {
        return substr(ltrim(spl_object_hash($object), "0"), 0, 8);
    }
}

class SomeSuperClass {

}