<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Utils;

use Phunkie\Cats\Lens;
use Phunkie\Types\ImmMap;
use Phunkie\Types\Pair;
use Phunkie\Types\Some;

/**
 * Generic lens implementation for object field access.
 * 
 * GenLens provides a way to create multiple lenses that focus on specific fields
 * of objects. Used primarily through the makeLenses() function to create field 
 * accessors that work with various types.
 *
 * Example:
 * ```php
 * // Create lenses for multiple fields
 * $lenses = makeLenses('name', 'age', 'address');
 * 
 * // Access fields through generated lenses
 * $name = $lenses->name->get($person); // Uses getName()
 * $age = $lenses->age->get($person);   // Uses getAge()
 * 
 * // Works with different types:
 * $map = ImmMap("key" => "value");
 * $value = $lenses->key->get($map);    // Uses ImmMap->get()
 * 
 * $pair = Pair("first", "second");
 * $first = $lenses->_1->get($pair);    // Direct field access
 * 
 * $wrapped = Some(ImmMap("x" => 1));
 * $x = $lenses->x->get($wrapped);      // Unwraps Some(ImmMap)
 * ```
 *
 * The generated lenses handle:
 * - Objects with getters (getName() for 'name')
 * - ImmMap values (using get())
 * - Some wrapped values (automatically unwrapped)
 * - Pair values (direct field access)
 * - Copiable objects (using copy())
 *
 * @see \Phunkie\Functions\lens\makeLenses() Function to create lenses
 * @see Lens The base lens interface
 * @see Copiable Interface for copyable objects
 */
final class GenLens
{
    /** @var array<string,Lens> The configured lenses, keyed by field name */
    private array $lenses = [];

    /**
     * Creates lenses for multiple fields.
     *
     * Used internally by makeLenses() to create field accessors.
     * Each field gets a corresponding lens that can get/set its value.
     *
     * @param string ...$fields Field names to create lenses for
     */
    public function __construct(...$fields)
    {
        foreach ($fields as $field) {
            $g = function ($data) use ($field) {
                if ($data instanceof ImmMap || ($data instanceof Some && $data->get() instanceof ImmMap)) {
                    if ($data instanceof Some) {
                        return $data->get()->get($field);
                    }
                    return $data->get($field);
                }
                if ($data instanceof Pair) {
                    return $data->$field;
                }
                $getter = "get{$field}";

                return $data->$getter();
            };
            $s = fn ($newValue, Copiable $data) => $data->copy([$field => $newValue]);
            $this->lenses[$field] = new Lens($g, $s);
        }
    }

    /**
     * Gets a configured lens by name.
     *
     * @param string $lens Name of the lens to get
     * @throws \Error If lens not configured
     */
    public function __get(string $lens): Lens
    {
        if (!isset($this->lenses[$lens])) {
            throw new \Error(sprintf('Lens %s has not been configured.', $lens));
        }

        return $this->lenses[$lens];
    }

    public function __isset(string $lens): bool
    {
        return isset($this->lenses[$lens]);
    }

    /**
     * Lenses are configured once, at construction.
     *
     * @param string $name Lens name
     * @param Lens $lens The lens to set
     * @throws \Error Always: lenses are immutable
     */
    public function __set(string $name, Lens $lens)
    {
        throw new \Error('Lenses are immutable.');
    }
}
