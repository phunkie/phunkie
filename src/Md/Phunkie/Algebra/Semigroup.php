<?php

namespace Md\Phunkie\Algebra;

interface Semigroup
{
    /**
     * @param T $one
     * @param T $another
     * @return T
     */
    public function combine($one, $another);
}