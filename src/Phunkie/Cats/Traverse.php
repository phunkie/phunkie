<?php

namespace Phunkie\Cats;

use Phunkie\Types\Kind;

/**
 * Traverse<F<_>>
 */
interface Traverse
{
    /**
     * (A -> G<B>) -> G<F<B>>
     */
    public function traverse(callable $f): Kind;

    /**
     * G<F<A>>
     */
    public function sequence(): Kind;
}
