<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Cats\Functor;

use Phunkie\Cats\Show;
use Phunkie\Ops\FunctorOps;
use Phunkie\Types\Function1;
use Phunkie\Types\ImmList;
use Phunkie\Types\Kind;
use Phunkie\Types\Option;

/**
 * Implements composition of multiple functors.
 * 
 * FunctorComposite allows combining multiple functors (like Option, ImmList, Function1)
 * into a single composite functor. This enables working with nested functor structures
 * in a more convenient way.
 *
 * Example:
 * ```php
 * $f = new FunctorComposite(Option::kind);
 * $g = $f->compose(ImmList::kind);
 * 
 * // Now we can map over Option<List<A>>
 * $composite = Some(ImmList(1, 2, 3));
 * $g->map($composite, fn($x) => $x * 2); // Some(List(2, 4, 6))
 * ```
 *
 * @template F
 * @template G
 * @template A
 */
class FunctorComposite
{
    use Show;
    use FunctorOps;
    
    /** @var array<string> List of functor kinds in this composition */
    protected array $kinds = [];

    /**
     * Creates a new functor composition starting with the given kind.
     *
     * @param string $kind The initial functor kind (ImmList, Option, or Function1)
     * @throws \RuntimeException If the kind is not supported
     */
    public function __construct(string $kind)
    {
        $this->kinds[] = match ($kind) {
            ImmList::kind, Option::kind, Function1::kind => $kind,
            default => throw new \RuntimeException("Composing functor of kind $kind is not supported")
        };
    }

    /**
     * Maps a function over the innermost type of the composite functor.
     *
     * @template B
     * @param Kind<F<G<A>>> $fa The nested functor structure
     * @param callable(A):B $f The function to apply
     * @return Kind<F<G<B>>> The transformed composite functor
     * @throws \TypeError If the functor kind doesn't match the composition
     */
    public function map(Kind $fa, callable $f)
    {
        $this->guardKindType($fa, $this->kinds[0]);
        return $fa->map($f);
    }

    /**
     * Invariant functor mapping for the composite.
     * Currently just delegates to map().
     */
    public function imap(Kind $fa, callable $f, callable $g)
    {
        return $this->map($fa, $f);
    }

    /**
     * Composes this functor with another functor kind.
     *
     * @param string $g The functor kind to compose with
     * @return FunctorComposite A new composite functor
     */
    public function compose(string $g): FunctorComposite
    {
        $functor = new class ($g, $this) extends FunctorComposite {
            use Show;
            private $fa;
            public function __construct(string $g, FunctorComposite $fa)
            {
                parent::__construct($g);
                $this->fa = $fa;
            }
            public function map(Kind $fga, callable $f)
            {
                return $this->fa->map($fga, fn ($ga) => $ga->map($f));
            }
        };
        $functor->kinds = array_merge($this->kinds, $functor->kinds);
        return $functor;
    }

    /**
     * Returns a string representation of the composite functor.
     *
     * @return string e.g., "Functor(Option(List()))" for Option<List<A>>
     */
    public function toString(): string
    {
        $covertImmListToList = fn ($kind) => $kind == ImmList::kind ? 'List' : $kind;
        $kinds = array_map($covertImmListToList, $this->kinds);
        return "Functor(" . implode("(", $kinds) . str_repeat(")", count($kinds));
    }

    /**
     * Ensures the given functor matches the expected kind.
     *
     * @param Kind $fa The functor to check
     * @param string $expectedType The kind that the functor should be
     * @throws \TypeError If the functor kind doesn't match
     */
    private function guardKindType(Kind $fa, string $expectedType)
    {
        if ($fa::kind !== $expectedType) {
            throw new \TypeError("Argument 1 passed to map() must be of the type " . $expectedType . ", " . $fa::kind . " given");
        }
    }
}
