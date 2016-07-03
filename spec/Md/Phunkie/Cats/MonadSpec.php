<?php

namespace spec\Md\Phunkie\Cats;

use Eris\TestTrait;
use Md\Phunkie\Laws\MonadLaws;
use Md\Phunkie\Types\Kind;
use PhpSpec\ObjectBehavior;

class MonadSpec extends ObjectBehavior
{
    use TestTrait, MonadLaws;
    /**
     * Kind<TA> $fa
     * TA => Kind<TB> $f
     * TB => Kind<TC> $g
     */
    function it_obeys_the_law_of_flatmap_associativity()
    {
        // Option
        $fa = Some(42);
        $f = function(int $x):Kind { return Some(gettype($x)); };
        $g = function(string $x): Kind { return Some(strlen($x) % 2 == 0); };
        expect($this->flapMapAssociativity($fa, $f, $g))->toBe(true);

        // List
        $fa = ImmList(1,2,3);
        $f = function(int $x):Kind { return ImmList(gettype($x)); };
        $g = function(string $x): Kind { return ImmList(strlen($x) % 2 == 0); };
        expect($this->flapMapAssociativity($fa, $f, $g))->toBe(true);
    }

    /**
     * Kind<TA> $fa
     * TA $a
     * TA => Kind<TB> $f
     */
    function it_obeys_the_law_of_left_identity()
    {
        // Option
        $fa = Some(42);
        $a = 1;
        $f = function(int $x): Kind { return Some(($x + 2) % 2 == 0); };
        expect($this->leftIdentity($fa, $a, $f))->toBe(true);

        // List
        $fa = ImmList(1,2,3);
        $a = 1;
        $f = function(int $x): Kind { return ImmList(($x + 2) % 2 == 0); };
        expect($this->leftIdentity($fa, $a, $f))->toBe(true);
    }

    function it_obeys_the_law_of_right_identity()
    {
        // Option
        $fa = Some(42);
        expect($this->rightIdentity($fa))->toBe(true);

        // List
        $fa = ImmList(1,2,3);
        expect($this->rightIdentity($fa))->toBe(true);
    }
}