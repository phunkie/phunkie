<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Types;

use Error;
use InvalidArgumentException;
use Phunkie\Cats\Functor;
use Phunkie\Cats\Show;
use Phunkie\Ops\Tuple\TupleFunctorOps;
use Phunkie\Utils\Copiable;
use TypeError;
use function Phunkie\Functions\functor\fmap;
use const Phunkie\Functions\show\showType;
use const Phunkie\Functions\show\showValue;

/**
 * Tuples in Phunkie are immutable ordered collections of elements where each element
 * can have a different type.
 *
 * @template T1
 * @template T2
 * @template T3
 * ...
 * @template TN
 */
class Tuple implements Copiable, Functor, Kind
{
    use Show;
    use TupleFunctorOps;

    /**
     * @var array<int,...TN>
     */
    private array $values;

    final public function __construct(...$values)
    {
        $this->tupleIsSealed();
        $this->guardNumArgs(func_num_args());
        $this->values = $values;
    }

    /**
     * @param string $member
     * @return mixed (T1 | T2 | T3 | ... | TN)
     */
    public function __get(string $member)
    {
        $this->startsWithUnderscore($member);
        $this->followedByANumber($member);
        $this->includedInMembers($member);

        return $this->values[$this->keyFromMember($member)];
    }

    /**
     * Tuples and Pairs are immutable
     *
     * $tuple = Tuple("hello", 42, true);
     *
     * These will throw TypeError:
     * $tuple->_1 = "world";     // Error: Tuples are immutable
     * $tuple->_4 = false;       // Error: _4 is not a member of tuple
     *
     * @param $member
     * @param $value
     * @return void
     * @throws TypeError always. Tuples are immutable
     */
    public function __set($member, $value): void
    {
        throw new \TypeError("Tuples are immutable");
    }

    /**
     * @param array $fields array<int, T1|T2|T3|...|TN>
     * @return Tuple<T1,...,TN>
     */
    public function copy(array $fields): Tuple
    {
        $values = $this->values;

        foreach ($fields as $parameter => $value) {
            $key = $this->keyFromMember($parameter);
            $this->validateKey($key, $parameter);
            $values[$key] = $value;
        }
        return Tuple(...$values);
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return "(" . implode(", ", fmap(showValue, ImmList(...$this->values))->toArray()) . ")";
    }

    /**
     * @return string
     */
    public function showType(): string
    {
        return sprintf("(%s)", implode(", ", $this->getTypeVariables()));
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->values;
    }

    /**
     * @return int
     */
    public function getArity(): int
    {
        return count($this->values);
    }

    /**
     * @return int
     */
    public function getTypeArity(): int
    {
        return $this->getArity();
    }

    /**
     * @return array
     */
    public function getTypeVariables(): array
    {
        return array_map(showType, $this->values);
    }

    /**
     * @param int $numArgs
     * @return void
     * @throws TypeError when trying to create a unit with arguments
     * @throws TypeError when trying to create a pair with the wrong number of arguments
     */
    private function guardNumArgs(int $numArgs): void
    {
        if (get_class($this) === Unit::class && $numArgs > 0) {
            throw new \TypeError(sprintf("Unit does not take arguments %d given", $numArgs));
        }

        if (get_class($this) === Pair::class && $numArgs !== 2) {
            throw new \TypeError(sprintf("Pair must take exactly 2 arguments %d given", $numArgs));
        }
    }

    /**
     * @return void
     * @throws TypeError when trying to extend a sealed class outside its package
     */
    private function tupleIsSealed(): void
    {
        if (!in_array(get_class($this), [Tuple::class, Pair::class, Unit::class])) {
            throw new TypeError("Tuple is sealed. It cannot be extended outside Phunkie");
        }
    }

    /**
     * @param $key
     * @param $member
     * @return void
     * @throws InvalidArgumentException when the key is not a member of the tuple
     */
    private function validateKey($key, $member): void
    {
        if (!array_key_exists($key, $this->values)) {
            throw new InvalidArgumentException("$member is not a member of " . get_class($this) . ".");
        }
    }

    /**
     * @param $member
     * @return void
     * @throws Error when the argument does not start with an underscore
     */
    private function startsWithUnderscore($member): void
    {
        if (!str_starts_with($member, "_")) {
            throw new Error("$member is not a member of " . get_class($this) . ".");
        }
    }

    /**
     * @param $member
     * @return void
     * @throws Error when the argument does not contain a number after the underscore
     */
    private function followedByANumber($member): void
    {
        if (!is_numeric(substr($member, 1))) {
            throw new Error("$member is not a member of " . get_class($this) . ".");
        }
    }

    /**
     * @param string $member
     * @return void
     * @throws Error when the argument is not a member of the tuple
     */
    private function includedInMembers(string $member): void
    {
        if (!array_key_exists($this->keyFromMember($member), $this->values)) {
            throw new Error("$member is not a member of Tuple");
        }
    }

    /**
     * @param string $member
     * @return int
     */
    private function keyFromMember(string $member): int
    {
        return ((int)substr($member, 1)) - 1;
    }
}
