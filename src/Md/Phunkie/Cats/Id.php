<?php

namespace Md\Phunkie\Cats;

use function Md\Phunkie\Functions\semigroup\combine;

/**
 * Id<A>
 */
class Id
{
    private $a;

    public function __construct( $a)
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