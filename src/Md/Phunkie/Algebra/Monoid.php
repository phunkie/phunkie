<?php

namespace Md\Phunkie\Algebra;

/**
 * Interface Monoid<T>
 */
interface Monoid extends Semigroup
{
    /**
     * @return T
     */
    public function zero();
}