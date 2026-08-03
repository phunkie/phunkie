<?php

namespace spec\Phunkie\Functions;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
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
            'countOf(): Argument #1 ($counts) must be of type ImmMap<String, String>, ImmMap<String, Int> given'
        );

        assertTypeArguments(ImmMap(["a" => 1]), ['String', 'String'], 'countOf', 1, 'counts');
    }

    #[Test]
    public function it_refuses_a_value_carrying_the_wrong_number_of_arguments()
    {
        $this->expectException(TypeError::class);

        assertTypeArguments(ImmList(1, 2), ['String', 'Int'], 'countOf', 1, 'counts');
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
}
