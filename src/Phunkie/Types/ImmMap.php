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

use ArrayAccess;
use SplObjectStorage;
use Phunkie\Cats\Applicative;
use Phunkie\Cats\Foldable;
use Phunkie\Cats\Functor;
use Phunkie\Cats\Monad;
use Phunkie\Cats\Show;
use Phunkie\Cats\Traverse;
use Phunkie\Ops\ImmMap\ImmMapApplicativeOps;
use Phunkie\Ops\ImmMap\ImmMapEqOps;
use Phunkie\Ops\ImmMap\ImmMapFoldableOps;
use Phunkie\Ops\ImmMap\ImmMapFunctorOps;
use Phunkie\Ops\ImmMap\ImmMapMonadOps;
use Phunkie\Ops\ImmMap\ImmMapMonoidOps;
use Phunkie\Ops\ImmMap\ImmMapOps;
use Phunkie\Ops\ImmMap\ImmMapTraverseOps;
use Phunkie\Utils\Copiable;
use Phunkie\Utils\Iterator;
use function Phunkie\Functions\show\showArrayType;
use function Phunkie\Functions\show\showValue;
use function Phunkie\Functions\type\promote;

/**
 * An immutable key-value map implementation.
 *
 * ImmMap provides a type-safe, immutable mapping between keys and values.
 * It implements several type class interfaces:
 * - Functor: Transform values while preserving structure
 * - Applicative: Lift values and apply functions in map context
 * - Monad: Chain computations that produce maps
 * - Foldable: Fold/reduce the map to a single value
 * - Traverse: Sequence effects through the map
 * - Monoid: Combine maps with the empty map as identity
 *
 * Example:
 * ```php
 * $map = ImmMap(1 => "one", 2 => "two");
 * $map->get(1);                    // Some("one")
 * $map->get(3);                    // None
 * $map->mapValues(fn($x) => strtoupper($x)); // ImmMap(1 => "ONE", 2 => "TWO")
 * $map->flatMap(fn($p) => ImmMap($p->_1 . "a" => $p->_2)); // Monad operations
 * $map->foldLeft(0)(fn($acc, $pair) => $acc + strlen($pair->_2)); // 6
 * ```
 *
 * @template K
 * @template V
 * @implements ArrayAccess<K,V>
 * @implements Copiable
 * @implements Applicative<ImmMap<K,V>>
 * @implements Monad<ImmMap<K,V>>
 * @implements Foldable<Pair<K,V>>
 * @implements Traverse
 * @implements Kind<ImmMap, K, V>
 */
final class ImmMap implements ArrayAccess, Copiable, Applicative, Monad, Foldable, Traverse, Kind
{
    public const kind = "ImmMap";
    use Show;
    use ImmMapEqOps;
    use ImmMapApplicativeOps;
    use ImmMapFoldableOps;
    use ImmMapTraverseOps;
    use ImmMapMonoidOps;
    use ImmMapOps;
    private $values;

    /**
     * Creates a new ImmMap.
     * 
     * Can be constructed in three ways:
     * 1. Empty map: ImmMap()
     * 2. From array: ImmMap(['key' => 'value'])
     * 3. From pairs: ImmMap('key', 'value', 'key2', 'value2')
     *
     * @throws \Error if odd number of arguments provided for pair construction
     */
    public function __construct(...$values) { $this->values = new SplObjectStorage(); match (true) {
        $this->noArguments($values) => _,
        $this->isArrayAndOneArgument($values) => $this->createFromArray($values),
        $this->oddNumberOfArguments($values) => throw new \Error("not enough arguments for constructor ImmMap"),
        default => $this->createFromVariadic($values) };
    }

    /**
     * Checks if a key exists in the map.
     *
     * @param K $offset The key to check
     * @return bool True if the key exists
     */
    public function offsetExists($offset): bool
    {
        foreach ($this->values as $k) {
            if ($k == promote($offset)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Gets the value associated with a key.
     *
     * @param K $offset The key to look up
     * @return Option<V> Some(value) if key exists, None otherwise
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        foreach ($this->values as $k) {
            if ($k == promote($offset)) {
                return Some($this->values[$k]);
            }
        }
        return None();
    }

    /**
     * Attempting to modify the map throws an error.
     *
     * @param K $offset Unused
     * @param V $value Unused
     * @throws \TypeError Always throws as ImmMap is immutable
     */
    public function offsetSet($offset, $value): void
    {
        throw new \TypeError("ImmMaps are immutable");
    }

    /**
     * Attempting to unset a key throws an error.
     *
     * @param K $offset Unused
     * @throws \TypeError Always throws as ImmMap is immutable
     */
    public function offsetUnset($offset): void
    {
        throw new \TypeError("ImmMaps are immutable");
    }

    /**
     * Creates a copy of the map with optional field updates.
     *
     * Example:
     * ```php
     * $map = ImmMap("a" => 1, "b" => 2);
     * $copy = $map->copy(["b" => 3]); // ImmMap("a" => 1, "b" => 3)
     * ```
     *
     * @param array<K,V> $fields Key-value pairs to update in the copy
     * @return ImmMap<K,V> A new map with the specified updates
     */
    public function copy(array $fields = [])
    {
        $copy = ImmMap();
        $copy->values = clone $this->values;
        foreach ($fields as $field => $value) {
            $copy = $copy->minus($field);
            $copy->values->attach(promote($field), $value);
        }
        return $copy;
    }

    /**
     * Gets the value associated with a key.
     * Alias for offsetGet().
     *
     * @param K $offset The key to look up
     * @return Option<V> Some(value) if key exists, None otherwise
     */
    public function get($offset)
    {
        return $this->offsetGet($offset);
    }

    /**
     * Checks if a key exists in the map.
     * Alias for offsetExists().
     *
     * @param K $offset The key to check
     * @return bool True if the key exists
     */
    public function contains($offset)
    {
        return $this->offsetExists(promote($offset));
    }

    /**
     * Gets the value for a key or returns a default value.
     *
     * Example:
     * ```php
     * $map = ImmMap("a" => 1);
     * $map->getOrElse("a", 0); // 1
     * $map->getOrElse("b", 0); // 0
     * ```
     *
     * @param K $offset The key to look up
     * @param V $default The default value if key doesn't exist
     * @return V The value or default
     */
    public function getOrElse($offset, $default)
    {
        return $this->get($offset)->getOrElse($default);
    }

    /**
     * Returns all keys in the map.
     *
     * @return array<K> Array of keys
     */
    public function keys()
    {
        $keys = [];
        foreach ($this->values as $k) {
            $keys[] = $k instanceof ImmString || $k instanceof ImmInteger ? $k->get() : $k;
        }
        return $keys;
    }

    /**
     * Returns all values in the map.
     *
     * @return array<V> Array of values
     */
    public function values()
    {
        $values = [];
        foreach ($this->values as $k) {
            $values[] = $this->values[$k];
        }
        return $values;
    }

    /**
     * Returns a string representation of the map.
     *
     * Example:
     * ```php
     * $map = ImmMap(1 => "one", 2 => "two");
     * echo $map->toString(); // "Map(1 -> one, 2 -> two)"
     * ```
     *
     * @return string The string representation
     */
    public function toString(): string
    {
        $mappings = [];
        foreach ($this->values as $k) {
            $mappings[] = showValue($k instanceof ImmString || $k instanceof ImmInteger ? $k->get() : $k) . " -> " . showValue($this->values[$k]);
        }
        return "Map(" . implode(", ", $mappings) . ")";
    }

    /**
     * Returns the number of type parameters this type takes.
     *
     * @return int Always returns 2 (key and value types)
     */
    public function getTypeArity(): int
    {
        return 2;
    }

    /**
     * Returns an array of type variables for this map.
     *
     * @return array<string> [key_type, value_type]
     */
    public function getTypeVariables(): array
    {
        return [showArrayType($this->keys()), showArrayType($this->values())];
    }

    /**
     * Returns a new map with an additional key-value pair.
     * If the key already exists, its value is updated.
     *
     * @param K $k The key to add
     * @param V $v The value to associate with the key
     * @return ImmMap<K,V> A new map with the added/updated pair
     */
    public function plus($k, $v)
    {
        if ($this->contains($k)) {
            return $this->minus($k)->plus($k, $v);
        }

        $mappings = clone $this->values;
        $mappings->attach(promote($k), $v);
        $map = new self();
        $map->values = $mappings;
        return $map;
    }

    /**
     * Returns a new map with a key removed.
     *
     * @param K $k The key to remove
     * @return ImmMap<K,V> A new map without the specified key
     */
    public function minus($k)
    {
        $mappings = new SplObjectStorage();
        if ($this->contains($k)) {
            foreach ($this->values as $offset) {
                if (promote($k) != $offset) {
                    $mappings[$offset] = $this->values[$offset];
                }
            }
            $map = new self();
            $map->values = $mappings;
            return $map;
        }
        return clone ($this);
    }

    /**
     * Returns an iterator over the map's entries.
     *
     * @return Iterator<K,V> Iterator over key-value pairs
     */
    public function iterator()
    {
        return new Iterator($this->values);
    }

    /**
     * Creates a map from an array.
     *
     * @param array<array<K,V>> $values Array containing a single associative array
     */
    private function createFromArray($values)
    {
        foreach ($values[0] as $k => $v) {
            $k = promote($k);
            $this->values[$k] = $v;
        }
    }

    /**
     * Creates a map from variadic arguments.
     *
     * @param array<K|V> $values Alternating key-value pairs
     */
    private function createFromVariadic($values)
    {
        for ($i = 0; $i <= count($values) - 1; $i += 2) {
            $values[$i] = promote($values[$i]);
            $this->values[$values[$i]] = $values[$i + 1];
        }
    }

    /**
     * Checks if no arguments were provided.
     *
     * @param array<mixed> $values Constructor arguments
     * @return bool True if no arguments provided
     */
    private function noArguments($values)
    {
        return count($values) == 0;
    }

    /**
     * Checks if a single array argument was provided.
     *
     * @param array<mixed> $values Constructor arguments
     * @return bool True if single array argument
     */
    private function isArrayAndOneArgument($values)
    {
        return count($values) == 1 && is_array($values[0]);
    }

    /**
     * Checks if an odd number of arguments was provided.
     *
     * @param array<mixed> $values Constructor arguments
     * @return bool True if odd number of arguments
     */
    private function oddNumberOfArguments($values)
    {
        return count($values) % 2 != 0;
    }
}
