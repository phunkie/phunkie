<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Phunkie\Types;

use Error;
use Md\Unit\TestCase;
use Md\PropertyTesting\TestTrait;
use Phunkie\Cats\Show;
use Phunkie\Types\Unit;
use PHPUnit\Framework\Attributes\Test;
use function Phunkie\Functions\show\showValue;
use function Phunkie\Functions\show\usesTrait;

class UnitSpec extends TestCase
{
    use TestTrait;

    #[Test]
    public function it_creates_a_unit_value()
    {
        $unit = Unit();

        $this->assertInstanceOf(Unit::class, $unit);
    }

    #[Test]
    public function it_is_showable()
    {
        $unit = Unit();

        $this->assertTrue(usesTrait($unit, Show::class));
        $this->assertEquals("()", $unit->toString());
        $this->assertEquals("()", (string)$unit);
        $this->assertEquals("()", showValue($unit));
    }

    #[Test]
    public function it_shows_its_type()
    {
        $unit = Unit();

        $this->assertEquals("Unit", $unit->showType());
    }

    #[Test]
    public function it_throws_error_when_accessing_members()
    {
        $unit = Unit();

        $this->expectException(Error::class);
        $this->expectExceptionMessage("_1 is not a member of Unit");

        $unit->_1;
    }

    #[Test]
    public function it_throws_error_when_setting_members()
    {
        $unit = Unit();

        $this->expectException(Error::class);
        $this->expectExceptionMessage("_1 is not a member of Unit");

        $unit->_1 = 42;
    }

    #[Test]
    public function it_throws_error_when_copying()
    {
        $unit = Unit();

        $this->expectException(Error::class);
        $this->expectExceptionMessage("copy is not a member of Unit");

        $unit->copy(['1' => 42]);
    }

    #[Test]
    public function it_supports_equality_comparison()
    {
        $unit1 = Unit();
        $unit2 = Unit();

        // All Units are equal to each other
        $this->assertTrue($unit1->eqv($unit2));
        $this->assertTrue($unit2->eqv($unit1));
    }

    #[Test]
    public function it_provides_monoid_zero()
    {
        $unit = Unit();
        $zero = $unit->zero();

        $this->assertInstanceOf(Unit::class, $zero);
        $this->assertTrue($unit->eqv($zero));
    }

    #[Test]
    public function it_combines_units()
    {
        $unit1 = Unit();
        $unit2 = Unit();
        $result = $unit1->combine($unit2);

        $this->assertInstanceOf(Unit::class, $result);
        $this->assertTrue($result->eqv(Unit()));
    }

    #[Test]
    public function it_obeys_monoid_left_identity_law()
    {
        // zero().combine(x) == x
        $unit = Unit();
        $zero = $unit->zero();

        $this->assertTrue(
            $zero->combine($unit)->eqv($unit)
        );
    }

    #[Test]
    public function it_obeys_monoid_right_identity_law()
    {
        // x.combine(zero()) == x
        $unit = Unit();
        $zero = $unit->zero();

        $this->assertTrue(
            $unit->combine($zero)->eqv($unit)
        );
    }

    #[Test]
    public function it_obeys_monoid_associativity_law()
    {
        // (a.combine(b)).combine(c) == a.combine(b.combine(c))
        $a = Unit();
        $b = Unit();
        $c = Unit();

        $this->assertTrue(
            $a->combine($b)->combine($c)->eqv(
                $a->combine($b->combine($c))
            )
        );
    }

    #[Test]
    public function it_obeys_eq_reflexivity_law()
    {
        // x.eqv(x) == true
        $unit = Unit();

        $this->assertTrue($unit->eqv($unit));
    }

    #[Test]
    public function it_obeys_eq_symmetry_law()
    {
        // x.eqv(y) == y.eqv(x)
        $x = Unit();
        $y = Unit();

        $this->assertEquals(
            $x->eqv($y),
            $y->eqv($x)
        );
    }

    #[Test]
    public function it_obeys_eq_transitivity_law()
    {
        // if x.eqv(y) and y.eqv(z) then x.eqv(z)
        $x = Unit();
        $y = Unit();
        $z = Unit();

        $this->assertTrue($x->eqv($y));
        $this->assertTrue($y->eqv($z));
        $this->assertTrue($x->eqv($z));
    }

    #[Test]
    public function it_demonstrates_singleton_behavior()
    {
        // All Unit values are equal
        $instances = array_map(fn() => Unit(), range(1, 10));

        foreach ($instances as $i => $unit1) {
            foreach ($instances as $j => $unit2) {
                $this->assertTrue(
                    $unit1->eqv($unit2),
                    "Unit at index $i should equal Unit at index $j"
                );
            }
        }
    }

    #[Test]
    public function it_combines_multiple_units()
    {
        // Combining multiple Units should always result in Unit
        $units = array_map(fn() => Unit(), range(1, 5));
        $result = array_reduce(
            array_slice($units, 1),
            fn($acc, $unit) => $acc->combine($unit),
            $units[0]
        );

        $this->assertInstanceOf(Unit::class, $result);
        $this->assertTrue($result->eqv(Unit()));
    }

    #[Test]
    public function it_can_be_used_as_return_value()
    {
        // Unit is useful as a return value for side-effecting functions
        $sideEffect = function() {
            // Perform some side effect
            return Unit();
        };

        $result = $sideEffect();
        $this->assertInstanceOf(Unit::class, $result);
        $this->assertEquals("()", $result->toString());
    }

    #[Test]
    public function it_maintains_monoid_properties_under_repeated_operations()
    {
        $unit = Unit();

        // Combining a unit with itself repeatedly should still be unit
        $result = $unit;
        for ($i = 0; $i < 100; $i++) {
            $result = $result->combine(Unit());
        }

        $this->assertTrue($result->eqv(Unit()));
    }

    #[Test]
    public function it_zero_is_idempotent()
    {
        // Calling zero() multiple times should always return equivalent Units
        $unit = Unit();
        $zero1 = $unit->zero();
        $zero2 = $zero1->zero();
        $zero3 = $zero2->zero();

        $this->assertTrue($zero1->eqv($zero2));
        $this->assertTrue($zero2->eqv($zero3));
        $this->assertTrue($zero1->eqv($zero3));
    }

    #[Test]
    public function it_has_consistent_string_representation()
    {
        // All Units should have the same string representation
        $units = array_map(fn() => Unit(), range(1, 10));

        foreach ($units as $unit) {
            $this->assertEquals("()", $unit->toString());
            $this->assertEquals("()", (string)$unit);
        }
    }
}
