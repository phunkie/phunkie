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

use function Phunkie\Functions\semigroup\combine;

/**
 * A list that is guaranteed to contain at least one element.
 * 
 * NonEmptyList (Nel) is a specialized ImmList that cannot be empty.
 * It provides stronger guarantees for operations that require non-empty lists.
 *
 * Example:
 * ```php
 * $nel = Nel(1, 2, 3);        // NonEmptyList(1, 2, 3)
 * $combined = $nel->combine(ImmList(4, 5)); // NonEmptyList(1, 2, 3, 4, 5)
 * ```
 *
 * @template A
 * @extends ImmList<A>
 */
final class NonEmptyList extends ImmList
{
    /**
     * Combines this NonEmptyList with another list.
     *
     * Example:
     * ```php
     * Nel(1, 2)->combine(ImmList(3, 4)); // Nel(1, 2, 3, 4)
     * ```
     *
     * @template B
     * @param ImmList<B> $another The list to combine with
     * @return NonEmptyList<A|B> The combined list
     */
    public function combine(ImmList $another)
    {
        return Nel(...combine($this->toArray(), $another->toArray()));
    }

    /**
     * Creates a Failure containing this list.
     *
     * @return Failure<A> A Failure containing this list
     */
    public function failure()
    {
        return Failure($this);
    }

    /**
     * Creates a Success containing this list.
     *
     * @return Success<A> A Success containing this list
     */
    public function success()
    {
        return Success($this);
    }
}
