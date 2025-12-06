<?php

namespace Phunkie\Cats;

use function Phunkie\Functions\type\normaliseType;
use Phunkie\Types\Kind;

/**
 * Represents a natural transformation between functors.
 * 
 * A natural transformation provides a way to convert from one functor to another
 * while preserving the structure. It must satisfy the naturality condition:
 * map(f) ∘ transform = transform ∘ map(f)
 *
 * Example:
 * ```php
 * // Natural transformation from Option to List
 * $optionToList = new class implements NaturalTransformation {
 *     public function transform(Kind $fa): Kind {
 *         return match(true) {
 *             $fa instanceof Some => ImmList($fa->get()),
 *             $fa instanceof None => ImmList()
 *         };
 *     }
 * };
 * 
 * $opt = Some(42);
 * $list = $optionToList->transform($opt); // List(42)
 * ```
 *
 * @template F
 * @template G
 */
class NaturalTransformation
{
    use Show;
    private $f;
    private $from;
    private $to;

    /**
     * Creates a natural transformation from a function.
     *
     * @param callable $f Function that transforms from one functor to another
     */
    public function __construct(callable $f)
    {
        $this->f = $f;
        $this->extractToAndFromTypes();
    }

    /**
     * Makes the transformation callable directly.
     *
     * @param Kind<F,A> $a Value to transform
     * @return Kind<G,A> Transformed value
     */
    public function __invoke($a)
    {
        return ($this->f)($a);
    }

    /**
     * Returns a string representation of this transformation.
     *
     * @return string A unique identifier for this transformation
     */
    public function toString(): string
    {
        return "NaturalTransformation" . "@" . substr(ltrim(spl_object_hash($this), "0"), 0, 8);
    }

    /**
     * Returns the type representation of this transformation.
     *
     * @return string Format: "~>[FromType, ToType]"
     */
    public function showType()
    {
        return "~>[$this->from, $this->to]";
    }

    /**
     * Property accessor for 'from' and 'to' types.
     *
     * @param string $member Property name ('from' or 'to')
     * @return string The source or target functor type
     * @throws \Error If accessing an invalid property
     */
    public function __get($member) { return match ($member) {
        "from" => $this->from,
        "to" => $this->to,
        default => throw new \Error("$member is not a member of Natural Transformation") };
    }

    /**
     * Prevents modification of the transformation.
     *
     * @param string $member Unused
     * @param mixed $value Unused
     * @throws \Error Always throws as NaturalTransformation is immutable
     */
    public function __set($member, $value)
    {
        throw new \Error("Natural Transformation is immutable");
    }

    /**
     * Extracts source and target types from the transformation function.
     * 
     * Uses reflection to determine the parameter and return types of the
     * transformation function, normalizing them to remove namespace prefixes.
     */
    private function extractToAndFromTypes()
    {
        $reflection = method_exists($this->f, "__invoke") ?
            new \ReflectionMethod($this->f, "__invoke") :
            new \ReflectionFunction($this->f);
        $this->from = ltrim(normaliseType($reflection->getParameters()[0]->getType()->getName()) ?: "?", "Phunkie\\Types\\");
        $this->to = ltrim(normaliseType($reflection->getReturnType()->getName()) ?: "?", "Phunkie\\Types\\");
    }

    /**
     * Transforms a value from one functor to another.
     *
     * @template A
     * @param Kind<F,A> $fa Value in the source functor
     * @return Kind<G,A> Value in the target functor
     */
    public function transform(Kind $fa): Kind
    {
        return ($this->f)($fa);
    }

    /**
     * Composes this transformation with another.
     * 
     * Creates a new transformation that applies this one followed by the given one.
     * The composition g ∘ f is implemented as f.andThen(g).
     *
     * @template H
     * @param NaturalTransformation<G,H> $g The transformation to compose with
     * @return NaturalTransformation<F,H> The composed transformation
     */
    public function andThen(NaturalTransformation $g): NaturalTransformation
    {
        return new class($this->f, $g) extends NaturalTransformation {
            private $f;
            private $g;

            public function __construct(callable $f, NaturalTransformation $g)
            {
                $this->f = $f;
                $this->g = $g;
            }

            public function transform(Kind $fa): Kind
            {
                return $this->g->transform(($this->f)($fa));
            }

            public function andThen(NaturalTransformation $h): NaturalTransformation
            {
                return $h->andThen($this);
            }
        };
    }
}
