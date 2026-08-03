<?php

namespace spec\Phunkie\Functions;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use function Phunkie\Functions\type\normaliseType;

class TypeSpec extends TestCase
{
    #[Test]
    #[DataProvider('functionConstants')]
    public function it_names_a_function_that_exists(string $constant, string $value)
    {
        // These constants are the callable form of the function beside them, so
        // one that names a function which does not exist is useless: every one
        // of them carried a stray Md\ prefix and resolved to nothing.
        $this->assertTrue(
            is_callable($value),
            sprintf('The constant %s names "%s", which is not callable.', $constant, $value)
        );
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function functionConstants(): array
    {
        $constants = [
            'Phunkie\Functions\type\normaliseType',
            'Phunkie\Functions\type\promote',
            'Phunkie\Functions\tuple\assign',
            'Phunkie\Functions\reader\ask',
        ];

        $cases = [];

        foreach ($constants as $constant) {
            $cases[$constant] = [$constant, constant($constant)];
        }

        return $cases;
    }

    #[Test]
    #[DataProvider('typeNames')]
    public function it_normalises_a_type_name(string $given, string $expected)
    {
        $this->assertEquals($expected, normaliseType($given));
    }

    #[Test]
    #[DataProvider('typeNames')]
    public function it_normalises_an_already_normalised_name_to_itself(string $given, string $expected)
    {
        // Normalising is idempotent, so a name that has been through it once can
        // go through again without changing. Before, an already normalised name
        // fell through the lookup untouched, which meant the alias "Bool" stayed
        // "Bool" while "bool" became "Boolean".
        $this->assertEquals($expected, normaliseType($expected));
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function typeNames(): array
    {
        return [
            'gettype spells floats double' => ['double', 'Float'],
            'reflection spells them float' => ['float', 'Float'],
            'gettype spells ints integer' => ['integer', 'Int'],
            'reflection spells them int' => ['int', 'Int'],
            'gettype spells bools boolean' => ['boolean', 'Boolean'],
            'reflection spells them bool' => ['bool', 'Boolean'],
            'string' => ['string', 'String'],
            'null' => ['null', 'Null'],
            'resource' => ['resource', 'Resource'],
            'array' => ['array', 'Array'],
            'callable' => ['callable', 'Callable'],
            'mixed' => ['mixed', 'Mixed'],
            'void' => ['void', 'Void'],
            'object' => ['object', 'Object'],
        ];
    }

    #[Test]
    public function it_leaves_a_name_it_does_not_know_alone()
    {
        $this->assertEquals('Option', normaliseType('Option'));
        $this->assertEquals('Phunkie\Types\ImmList', normaliseType('Phunkie\Types\ImmList'));
    }
}
