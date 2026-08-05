<?php

namespace spec\Phunkie\Cats;

use PHPUnit\Framework\TestCase;
use Phunkie\Types\ImmList;
use Phunkie\Types\ImmMap;
use Phunkie\Types\ImmSet;
use Phunkie\Types\Kind;
use stdClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use function Phunkie\Functions\show\showKind;
use function Phunkie\Functions\show\showType;
use function Phunkie\Functions\show\showValue;
use function Phunkie\Functions\type\normaliseType;

class ShowSpec extends TestCase
{
    // A collection is called one thing, and `kind` is where that one thing is
    // written down. `ImmMap` and `ImmSet` used to hold their class name there,
    // so they announced themselves as ImmMap<String, Int> while rendering as
    // Map("a" -> 1). `ImmList` escaped it only by overriding showType, which is
    // why this is stated once for all three rather than three times.
    #[Test]
    #[DataProvider('collections')]
    public function it_calls_a_collection_the_same_in_its_kind_its_type_and_its_value(
        string $name,
        string $type,
        object $collection
    ) {
        $this->assertEquals($name, $collection::kind);
        $this->assertEquals($type, showType($collection));
        $this->assertStringStartsWith($name . "(", showValue($collection));
    }

    /**
     * @return array<string, array{string, string, object}>
     */
    public static function collections(): array
    {
        return [
            'list' => ['List', 'List<Int>', ImmList(1, 2)],
            'set' => ['Set', 'Set<Int>', ImmSet(1, 2)],
            'map' => ['Map', 'Map<String, Int>', ImmMap(["a" => 1])],
        ];
    }

    #[Test]
    public function it_prints_types()
    {
        $this->assertEquals(showType(1), "Int");
        $this->assertEquals(showType("1"), "String");
        $this->assertEquals(showType(27.23), "Float");
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

        $this->assertEquals("Validation<E, String>", showType(Success("yay")));
        $this->assertEquals("Validation<Exception, A>", showType(Failure(new \Exception())));

        $this->assertEquals(showType(Tuple(1, true, "")), "(Int, Boolean, String)");

        $this->assertEquals(showType(new class () {}), "AnonymousClass");
        $this->assertEquals(showType(new class () extends SomeSuperClass {}), "AnonymousClass < " . SomeSuperClass::class);
    }

    #[Test]
    public function it_calls_the_php_float_type_float_everywhere()
    {
        $this->assertEquals("Float", showType(1.5));
        $this->assertEquals("List<Float>", showType(ImmList(1.5)));
        $this->assertEquals("Array<Float>", showType([1.5]));

        // gettype() spells it "double", reflection spells it "float".
        $this->assertEquals("Float", normaliseType("double"));
        $this->assertEquals("Float", normaliseType("float"));

        // The retired spelling still resolves, so ":kind Double" keeps working.
        $this->assertEquals("Float", normaliseType("Double"));
        $this->assertEquals("*", showKind("Float")->getOrElse("?"));
        $this->assertEquals("*", showKind("Double")->getOrElse("?"));
    }

    // A generic class of your own is a Kind, because the compiler makes it one,
    // but nothing makes it use the Show trait. Falling back to its class name
    // there would have it announce itself as Stack while every phunkie type
    // beside it says what it is holding.
    #[Test]
    public function it_calls_a_kind_by_its_type_without_the_show_trait()
    {
        $this->assertEquals("Stack<Int>", showType(new Stack(1, 2)));
    }

    #[Test]
    public function it_names_a_kind_holding_nothing_without_its_arguments()
    {
        $this->assertEquals("Stack", showType(new Stack()));
    }

    // `kind` is where a type's name is written down, so a class that says it is
    // called something else is called that, exactly as ImmMap and ImmSet are.
    #[Test]
    public function it_prefers_the_name_a_kind_writes_down_to_its_class_name()
    {
        $this->assertEquals("Heap<String>", showType(new PriorityQueue("a")));
    }

    #[Test]
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

/**
 * What `final class Stack<T>` compiles to: a Kind, with no Show trait and no
 * name of its own written down.
 */
class Stack implements Kind
{
    private array $items;

    public function __construct(...$items)
    {
        $this->items = $items;
    }

    public function getTypeArity(): int
    {
        return 1;
    }

    public function getTypeVariables(): array
    {
        return $this->items === [] ? [] : [showType($this->items[0])];
    }
}

class PriorityQueue extends Stack
{
    public const kind = "Heap";
}
