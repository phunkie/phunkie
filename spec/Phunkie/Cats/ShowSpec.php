<?php

namespace spec\Phunkie\Cats;

use PHPUnit\Framework\TestCase;
use stdClass;
use function Phunkie\Functions\show\showType;
use function Phunkie\Functions\show\showValue;

class ShowSpec extends TestCase
{
    /**
     * @test
     */
    public function it_prints_types()
    {
        $this->assertEquals(showType(1), "Int");
        $this->assertEquals(showType("1"), "String");
        $this->assertEquals(showType(27.23), "Double");
        $this->assertEquals(showType(null), "Null");
        $this->assertEquals(showType(true), "Boolean");
        $this->assertEquals(showType(STDIN), "Resource");
        $this->assertEquals(showType([1,2,3]), "Array<Int>");
        $this->assertEquals(showType([]), "Array<Nothing>");
        $this->assertEquals(showType(["foo"=>"bar"]), "Array<String, String>");
        $this->assertEquals(showType(function () {
        }), "Callable");

        $this->assertEquals(showType(Unit()), "Unit");
        $this->assertEquals(showType(None()), "None");
        $this->assertEquals(showType(Some(42)), "Option<Int>");
        $this->assertEquals(showType(ImmList(1, 2, 3)), "List<Int>");
        $this->assertEquals(showType(ImmList(Some(1), Some(2), Some(3))), "List<Option<Int>>");
        $this->assertEquals(showType(Nil()), "List<Nothing>");
        $this->assertEquals(showType(ImmList(1, 2, "f")), "List<Mixed>");
        $this->assertEquals(showType(ImmList(Some(32), Some(56), None())), "List<Option<Int>>");
        $this->assertEquals(showType(Function1(fn (int $x): bool => $x == 42)), "Function1");

        $this->assertEquals(showType(Pair(42, "42")), "(Int, String)");

        $this->assertEquals(showType(Success("yay")), "Validation<E, String>");
        $this->assertEquals(showType(Failure(new \Exception())), "Validation<Exception, A>");

        $this->assertEquals(showType(Tuple(1, true, "")), "(Int, Boolean, String)");

        $this->assertEquals(showType(new class () {}), "AnonymousClass");
        $this->assertEquals(showType(new class () extends SomeSuperClass {}), "AnonymousClass < " . SomeSuperClass::class);
    }

    /**
     * @test
     */
    public function it_prints_value()
    {
        $this->assertEquals(showValue(1), "1");
        $this->assertEquals(showValue("1"), '"1"');
        $this->assertEquals(showValue(true), "true");
        $this->assertEquals(showValue(null), "null");
        $this->assertEquals(showValue([1,2,3]), "[1, 2, 3]");
        $this->assertEquals(showValue(fn (int $x): bool => $x == 42), "<function>");

        $this->assertEquals(showValue(Some(42)), Some(42)->show());
        $this->assertEquals(showValue(ImmList(42)), ImmList(42)->show());
        $this->assertEquals(
            showValue(Function1(fn (int $x): bool => $x == 42)),
            Function1(fn (int $x): bool => $x == 42)->show()
        );
        $this->assertEquals(showValue(Success(42)), Success(42)->show());
        $this->assertEquals(showValue(Failure(42)), Failure(42)->show());
        $this->assertEquals(showValue(Tuple(42)), Tuple(42)->show());

        $object = new stdClass();
        $this->assertEquals(showValue($object), get_class($object) . "@" . $this->hash($object));

        $object = new class () {};
        $this->assertEquals(showValue($object), "Anonymous" . "@" . $this->hash($object));

        $object = new class () extends SomeSuperClass {};
        $this->assertEquals(showValue($object), "Anonymous < " . SomeSuperClass::class . "@" . $this->hash($object));
    }

    private function hash($object)
    {
        return substr(ltrim(spl_object_hash($object), "0"), 0, 8);
    }
}

class SomeSuperClass
{
}
