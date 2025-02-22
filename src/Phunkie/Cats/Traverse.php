<?php

namespace Phunkie\Cats;

use Phunkie\Types\Kind;

/**
 * Provides traversal capabilities over data structures.
 * 
 * Traverse allows you to transform elements of a structure while preserving its shape,
 * where the transformation itself returns values in some applicative functor F.
 * This is useful for:
 * - Performing effects while traversing a structure
 * - Collecting results in a different context
 * - Converting between different applicative structures
 *
 * Example:
 * ```php
 * class UserList implements Traverse {
 *     private $ids;
 * 
 *     // Find users by their IDs, might return None for not found
 *     public function findUsers(): Kind {
 *         return $this->traverse(fn($id) => findUserById($id));
 *         // Returns Option<ImmList<User>>
 *     }
 * 
 *     public function traverse(callable $f): Kind {
 *         // Implementation...
 *     }
 * 
 *     public function sequence(): Kind {
 *         return $this->traverse(fn($x) => $x);
 *     }
 * }
 * ```
 *
 * The key difference from map is that traverse handles nested effects:
 * - map:      (A => B) => F<A> => F<B>
 * - traverse: (A => G<B>) => F<A> => G<F<B>>
 */
interface Traverse
{
    /**
     * Traverses this structure with an effectful function.
     * 
     * Applies a function that returns values in applicative functor G
     * to each element, collecting results in G<F<B>>.
     *
     * @template G The effect type
     * @template B The result element type
     * @param callable(A):Kind<G,B> $f The effectful function to apply
     * @return Kind<G,F<B>> Results collected in the effect type
     */
    public function traverse(callable $f): Kind;

    /**
     * Sequences nested effects in this structure.
     * 
     * Useful when you have a structure of effects and want to get
     * an effect of structure instead.
     *
     * Example:
     * ```php
     * $list = ImmList(Some(1), Some(2));
     * $result = $list->sequence();
     * // Some(ImmList(1, 2))
     * ```
     *
     * @return Kind<G,F<A>> Effects containing structure
     */
    public function sequence(): Kind;
}
