<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Cats;

use function Phunkie\Functions\semigroup\combine;

/**
 * Id<A>
 */
class Id
{
    private $a;

    public function __construct($a)
    {
        $this->a = $a;
    }

    public function map($f)
    {
        return $f($this->a);
    }

    /**
     * @param callable<A, Id<B>> $f
     * @return Id<B>
     */
    public function flatMap(callable $f)
    {
        return $f($this->a);
    }

    public function andThen($b)
    {
        return combine($this->a, $b);
    }

    public function compose($b)
    {
        return combine($b, $this->a);
    }
}
