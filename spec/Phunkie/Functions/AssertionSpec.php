<?php

namespace spec\Phunkie\Functions;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ArrayIterator;
use ArrayObject;
use Phunkie\Types\Kind;
use TypeError;

/**
 * The guards a generic signature compiles to. They are global, like pmatch,
 * because the compiled PHP calls them unqualified from whatever namespace the
 * source was written in.
 */
class AssertionSpec extends TestCase
{
    #[Test]
    public function it_accepts_a_value_whose_type_argument_is_the_one_promised()
    {
        $this->expectNotToPerformAssertions();

        assertTypeArguments(ImmList(1, 2, 3), ['Int'], 'doubleAll', 1, 'numbers');
    }

    #[Test]
    public function it_refuses_a_value_whose_type_argument_is_another()
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage(
            'doubleAll(): Argument #1 ($numbers) must be of type List<Int>, List<String> given'
        );

        assertTypeArguments(ImmList("a", "b"), ['Int'], 'doubleAll', 1, 'numbers');
    }

    // Nothing is the bottom type, so an empty container has committed to nothing
    // and satisfies every argument. This is what makes the empty case in a
    // guarded return need no handling of its own.
    #[Test]
    public function it_accepts_an_empty_container_whatever_was_promised()
    {
        $this->expectNotToPerformAssertions();

        assertTypeArguments(ImmList(), ['Int'], 'doubleAll', 1, 'numbers');
    }

    // None reports no arguments at all rather than Nothing, because Option's
    // arity follows whether it holds anything. It means the same thing here:
    // one shared object for every absence, committed to nothing.
    #[Test]
    public function it_accepts_an_absent_value_whatever_was_promised()
    {
        $this->expectNotToPerformAssertions();

        assertTypeArguments(None(), ['User'], 'getAddress', 1, 'user');
    }

    #[Test]
    public function it_accepts_a_present_value_holding_what_was_promised()
    {
        $this->expectNotToPerformAssertions();

        assertTypeArguments(Some(42), ['Int'], 'double', 1, 'number');
    }

    #[Test]
    public function it_refuses_a_present_value_holding_something_else()
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage(
            'double(): Argument #1 ($number) must be of type Option<Int>, Option<String> given'
        );

        assertTypeArguments(Some("42"), ['Int'], 'double', 1, 'number');
    }

    // Mixed is the top type. It needs no rule of its own: a heterogeneous list
    // reports Mixed, which is not the argument that was promised, so it fails
    // the same comparison every other wrong argument fails.
    #[Test]
    public function it_refuses_a_heterogeneous_value()
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage(
            'doubleAll(): Argument #1 ($numbers) must be of type List<Int>, List<Mixed> given'
        );

        assertTypeArguments(ImmList(1, "a"), ['Int'], 'doubleAll', 1, 'numbers');
    }

    // A subtype reports its parent's arguments, so the guard agrees with it
    // while PHP's own declaration keeps hold of the constructor. Comparing
    // rendered names instead would look for NonEmptyList<Int> against a value
    // that calls itself List<Int>, and never match.
    #[Test]
    public function it_accepts_a_subtype_reporting_its_parents_argument()
    {
        $this->expectNotToPerformAssertions();

        assertTypeArguments(Nel(1, 2, 3), ['Int'], 'head', 1, 'xs');
    }

    #[Test]
    public function it_accepts_a_nested_type_argument()
    {
        $this->expectNotToPerformAssertions();

        assertTypeArguments(ImmList(Some(1), Some(2)), ['Option<Int>'], 'firstDefined', 1, 'options');
    }

    #[Test]
    public function it_refuses_a_nested_type_argument_that_differs()
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage(
            'firstDefined(): Argument #1 ($options) must be of type List<Option<Int>>, List<Int> given'
        );

        assertTypeArguments(ImmList(1, 2), ['Option<Int>'], 'firstDefined', 1, 'options');
    }

    // A signature names the class, because the class is what PHP enforces. A
    // value reports its type. ImmMap and Map are the same type written two
    // ways, and at the top level it never showed, the guard reading the
    // constructor off the value and comparing only the arguments. One level
    // down the written text is what gets compared, so it has to be read as a
    // type name before anything is compared to it.
    #[Test]
    public function it_accepts_a_nested_argument_written_with_its_class_name()
    {
        $this->expectNotToPerformAssertions();

        assertTypeArguments(ImmList(ImmMap(["a" => 1])), ['ImmMap<String, Int>'], 'rowsOf', 1, 'rows');
    }

    #[Test]
    public function it_accepts_a_deeply_nested_argument_written_with_class_names()
    {
        $this->expectNotToPerformAssertions();

        assertTypeArguments(ImmList(Some(ImmList(1, 2))), ['Option<ImmList<Int>>'], 'deep', 1, 'xs');
    }

    #[Test]
    public function it_names_the_type_rather_than_the_class_when_it_refuses()
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage(
            'rowsOf(): Argument #1 ($rows) must be of type List<Map<String, Int>>, List<Option<Int>> given'
        );

        assertTypeArguments(ImmList(Some(1)), ['ImmMap<String, Int>'], 'rowsOf', 1, 'rows');
    }

    #[Test]
    public function it_reads_a_returned_nested_argument_as_a_type_name_too()
    {
        $rows = ImmList(ImmMap(["a" => 1]));

        $this->assertSame($rows, assertReturnTypeArguments($rows, ['ImmMap<String, Int>'], 'rowsOf'));
    }

    #[Test]
    public function it_accepts_every_argument_of_a_type_that_takes_more_than_one()
    {
        $this->expectNotToPerformAssertions();

        assertTypeArguments(ImmMap(["a" => 1]), ['String', 'Int'], 'countOf', 1, 'counts');
    }

    #[Test]
    public function it_refuses_when_one_argument_of_several_differs()
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage(
            'countOf(): Argument #1 ($counts) must be of type Map<String, String>, Map<String, Int> given'
        );

        assertTypeArguments(ImmMap(["a" => 1]), ['String', 'String'], 'countOf', 1, 'counts');
    }

    #[Test]
    public function it_refuses_a_value_carrying_the_wrong_number_of_arguments()
    {
        $this->expectException(TypeError::class);

        assertTypeArguments(ImmList(1, 2), ['String', 'Int'], 'countOf', 1, 'counts');
    }

    // A class from someone else's package cannot be made to implement Kind, so
    // what it holds has to be worked out by looking. Otherwise a type argument
    // on it would be accepted without ever being checked, which is worse than
    // refusing to check it.
    #[Test]
    public function it_works_out_what_a_foreign_collection_holds()
    {
        $this->expectNotToPerformAssertions();

        assertTypeArguments(new ArrayObject([1, 2, 3]), ['Int'], 'countOf', 1, 'xs');
    }

    #[Test]
    public function it_refuses_a_foreign_collection_holding_something_else()
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage(
            'countOf(): Argument #1 ($xs) must be of type ArrayObject<Int>, ArrayObject<String> given'
        );

        assertTypeArguments(new ArrayObject(["a", "b"]), ['Int'], 'countOf', 1, 'xs');
    }

    #[Test]
    public function it_accepts_an_empty_foreign_collection()
    {
        $this->expectNotToPerformAssertions();

        assertTypeArguments(new ArrayObject([]), ['Int'], 'countOf', 1, 'xs');
    }

    #[Test]
    public function it_reads_a_keyed_foreign_collection_as_two_arguments()
    {
        $this->expectNotToPerformAssertions();

        assertTypeArguments(new ArrayObject(["ada" => 1815]), ['String', 'Int'], 'bornIn', 1, 'born');
    }

    #[Test]
    public function it_refuses_a_keyed_foreign_collection_whose_keys_differ()
    {
        $this->expectException(TypeError::class);

        assertTypeArguments(new ArrayObject(["ada" => 1815]), ['Int', 'Int'], 'bornIn', 1, 'born');
    }

    // Looking costs nothing on a collection that can be walked twice. A one shot
    // iterator cannot, and consuming it to check it would leave the function
    // nothing to work with.
    #[Test]
    public function it_leaves_a_one_shot_iterator_unread()
    {
        $this->expectNotToPerformAssertions();

        $generator = (function () { yield "a"; yield "b"; })();

        assertTypeArguments($generator, ['Int'], 'countOf', 1, 'xs');
    }

    // Implementing Iterator is a claim to rewind, so anything that makes it is
    // taken at its word and read. Only Generator is documented as unable to,
    // which is what makes it the exception rather than iterators in general.
    #[Test]
    public function it_reads_an_iterator_that_can_be_walked_again()
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage(
            'countOf(): Argument #1 ($xs) must be of type ArrayIterator<Int>, ArrayIterator<String> given'
        );

        assertTypeArguments(new ArrayIterator(["a", "b"]), ['Int'], 'countOf', 1, 'xs');
    }

    #[Test]
    public function it_leaves_an_iterator_it_read_where_it_found_it()
    {
        $numbers = new ArrayIterator([1, 2, 3]);

        assertTypeArguments($numbers, ['Int'], 'countOf', 1, 'xs');

        $this->assertSame([1, 2, 3], iterator_to_array($numbers));
    }

    // An array carries no type of its own to ask, so it is read the same way a
    // foreign collection is. This is the widest case of all: before a signature
    // could promise an argument on one, every array was accepted unread.
    #[Test]
    public function it_works_out_what_an_array_holds()
    {
        $this->expectNotToPerformAssertions();

        assertTypeArguments([1, 2, 3], ['Int'], 'countOf', 1, 'xs');
    }

    /**
     * PHP writes its own types in lower case and phunkie renders them
     * capitalised, so a reader who writes `array<string>`, which is the
     * spelling PHP taught them, was told their array of strings was an
     * `Array<String>` and refused. PHP resolves a type name without regard to
     * case and so does this.
     */
    #[Test]
    public function it_reads_a_type_name_the_way_php_does_whatever_the_case()
    {
        $this->expectNotToPerformAssertions();

        assertTypeArguments(["a", "b"], ['string'], 'namesOf', 1, 'names');
        assertTypeArguments([1, 2], ['INT'], 'countOf', 1, 'counts');
        assertTypeArguments([1.5], ['float'], 'sizesOf', 1, 'sizes');
        assertTypeArguments([new Stack()], ['stack'], 'stacksOf', 1, 'stacks');
    }

    /**
     * `bool` is not `Boolean` in any case at all. It is the one type PHP names
     * differently from the way phunkie renders it, so it is the one that needs
     * saying rather than folding.
     */
    #[Test]
    public function it_knows_bool_and_boolean_are_the_same_type()
    {
        $this->expectNotToPerformAssertions();

        assertTypeArguments([true, false], ['bool'], 'flagsOf', 1, 'flags');
    }

    #[Test]
    public function it_refuses_an_array_holding_something_else()
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage(
            'countOf(): Argument #1 ($xs) must be of type Array<Int>, Array<String> given'
        );

        assertTypeArguments(["a", "b"], ['Int'], 'countOf', 1, 'xs');
    }

    #[Test]
    public function it_accepts_an_empty_array_whatever_was_promised()
    {
        $this->expectNotToPerformAssertions();

        assertTypeArguments([], ['Int'], 'countOf', 1, 'xs');
    }

    #[Test]
    public function it_reads_a_keyed_array_as_two_arguments()
    {
        $this->expectNotToPerformAssertions();

        assertTypeArguments(["ada" => 1815], ['String', 'Int'], 'bornIn', 1, 'born');
    }

    #[Test]
    public function it_refuses_a_keyed_array_whose_keys_differ()
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage(
            'bornIn(): Argument #1 ($born) must be of type Array<Int, Int>, Array<String, Int> given'
        );

        assertTypeArguments(["ada" => 1815], ['Int', 'Int'], 'bornIn', 1, 'born');
    }

    // The constructor is PHP's business. A value that reports no type arguments
    // is not something the guard has anything to say about, and the native
    // declaration beside it has already had its say.
    #[Test]
    public function it_leaves_a_value_that_reports_no_type_arguments_alone()
    {
        $this->expectNotToPerformAssertions();

        assertTypeArguments(42, ['Int'], 'double', 1, 'number');
    }

    #[Test]
    public function it_gives_back_the_value_it_guarded_on_the_way_out()
    {
        $numbers = ImmList(1, 2, 3);

        $this->assertSame($numbers, assertReturnTypeArguments($numbers, ['Int'], 'doubleAll'));
    }

    // PHP words this position differently from an argument, and so does this.
    #[Test]
    public function it_refuses_a_returned_value_of_another_type()
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage(
            'doubleAll(): Return value must be of type List<Int>, List<String> returned'
        );

        assertReturnTypeArguments(ImmList("a", "b"), ['Int'], 'doubleAll');
    }

    #[Test]
    public function it_accepts_an_empty_return_whatever_was_promised()
    {
        $empty = ImmList();

        $this->assertSame($empty, assertReturnTypeArguments($empty, ['Int'], 'firstTwo'));
    }

    // A return is worked out the same way an argument is, so a value with no
    // type of its own to ask is read on the way out as well as on the way in.
    #[Test]
    public function it_works_out_what_a_returned_collection_holds()
    {
        $names = new ArrayObject(["ada", "alan"]);

        $this->assertSame($names, assertReturnTypeArguments($names, ['String'], 'namesOf'));
    }

    #[Test]
    public function it_refuses_a_returned_collection_holding_something_else()
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage(
            'namesOf(): Return value must be of type ArrayObject<String>, ArrayObject<Int> returned'
        );

        assertReturnTypeArguments(new ArrayObject([1, 2]), ['String'], 'namesOf');
    }

    #[Test]
    public function it_refuses_a_returned_array_holding_something_else()
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage(
            'namesOf(): Return value must be of type Array<String>, Array<Int> returned'
        );

        assertReturnTypeArguments([1, 2], ['String'], 'namesOf');
    }

    // What T stands for depends on the object the method was called on, so the
    // object comes along: a stack of integers wants an integer pushed onto it.
    #[Test]
    public function it_accepts_a_value_that_is_what_the_type_variable_stands_for()
    {
        $this->expectNotToPerformAssertions();

        assertTypeVariable(4, 'T', new Stack(1, 2), 'Stack::push', 1, 'item');
    }

    #[Test]
    public function it_refuses_a_value_that_is_not_what_the_type_variable_stands_for()
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage(
            'Stack::push(): Argument #1 ($item) must be of type Int, String given'
        );

        assertTypeVariable("four", 'T', new Stack(1, 2), 'Stack::push', 1, 'item');
    }

    // A container that has committed to nothing stands for nothing yet, so the
    // first thing put into it is what it comes to stand for and cannot be wrong.
    #[Test]
    public function it_accepts_anything_put_into_a_container_holding_nothing()
    {
        $this->expectNotToPerformAssertions();

        assertTypeVariable("four", 'T', new Stack(), 'Stack::push', 1, 'item');
    }

    #[Test]
    public function it_reads_what_a_class_that_declared_its_parameters_is_holding()
    {
        $this->assertSame(['Int'], typeArgumentsHeldBy(new Stack(1, 2)));
    }

    #[Test]
    public function it_reads_nothing_from_a_class_holding_nothing_that_can_be_walked()
    {
        $this->assertSame([], typeArgumentsHeldBy(new Stack()));
    }

    // ImmList<T> inside a class that declared <T> promised a list of whatever
    // that object holds, so the promise cannot be read until there is an object
    // to read it against.
    #[Test]
    public function it_accepts_an_argument_resolved_to_what_its_owner_holds()
    {
        $this->expectNotToPerformAssertions();

        assertTypeArguments(ImmList(3, 4), ['T'], 'Stack::pushAll', 1, 'items', new Stack(1, 2));
    }

    #[Test]
    public function it_refuses_an_argument_that_is_not_what_its_owner_holds()
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage(
            'Stack::pushAll(): Argument #1 ($items) must be of type List<Int>, List<String> given'
        );

        assertTypeArguments(ImmList("a"), ['T'], 'Stack::pushAll', 1, 'items', new Stack(1, 2));
    }

    // An argument naming none of the parameters the class declared has nothing
    // to do with the object, and is left as it was written.
    #[Test]
    public function it_leaves_an_argument_naming_no_type_parameter_as_it_was_written()
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage(
            'Stack::labels(): Argument #1 ($labels) must be of type List<String>, List<Int> given'
        );

        assertTypeArguments(ImmList(1, 2), ['String'], 'Stack::labels', 1, 'labels', new Stack(1, 2));
    }

    #[Test]
    public function it_resolves_a_returned_type_variable_against_its_owner()
    {
        $numbers = ImmList(3, 4);

        $this->assertSame($numbers, assertReturnTypeArguments($numbers, ['T'], 'Stack::take', new Stack(1, 2)));
    }

    #[Test]
    public function it_refuses_a_return_that_is_not_what_its_owner_holds()
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage(
            'Stack::take(): Return value must be of type List<Int>, List<String> returned'
        );

        assertReturnTypeArguments(ImmList("a"), ['T'], 'Stack::take', new Stack(1, 2));
    }

    #[Test]
    public function it_accepts_a_return_from_a_container_holding_nothing()
    {
        $anything = ImmList("a");

        $this->assertSame($anything, assertReturnTypeArguments($anything, ['T'], 'Stack::take', new Stack()));
    }

    // A guard on an ordinary function has no owner to resolve against, and one
    // naming no type variable never needs it.
    #[Test]
    public function it_guards_without_an_owner_as_it_always_did()
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage(
            'doubleAll(): Argument #1 ($numbers) must be of type List<Int>, List<String> given'
        );

        assertTypeArguments(ImmList("a"), ['Int'], 'doubleAll', 1, 'numbers');
    }
}

/**
 * What `final class Stack<T>` compiles to: the names it gave its parameters,
 * how many it takes, and an answer to what they are read from the value.
 */
class Stack implements Kind
{
    public const typeParameters = ['T'];

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
        return typeArgumentsHeldBy($this);
    }
}
