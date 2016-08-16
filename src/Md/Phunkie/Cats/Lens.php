<?php

namespace Md\Phunkie\Cats;

/**
 * Lens<A,B>
 */
class Lens
{
    private $g;
    private $s;

    /**
     * @param callable<A, B> $g
     * @param callable<(A,B), A> $s
     */
    public function __construct(callable $g, callable $s)
    {
        $this->g = $g;
        $this->s = $s;
    }

    /**
     * @param A $a
     * @return B
     */
    public function get($a)
    {
        return call_user_func($this->g, $a);
    }

    /**
     * @param B $b
     * @param A $a
     * @return A
     */
    public function set($b, $a)
    {
        return call_user_func_array($this->s, [$b, $a]);
    }

    /**
     * @param callable<B,B> $f
     * @param A $a
     * @return A
     */
    public function mod(callable $f, $a)
    {
        return $this->set($f($this->get($a)), $a);
    }

    public function andThen(Lens $l): Lens
    {
        return new Lens(
            function($a) use ($l) { return $l->get($this->get($a)); },
            function($c, $a) use ($l) { return $this->mod(function($b) use ($l, $c) { return $l->set($c, $b); }, $a); }
        );
    }

    public function compose(Lens $that): Lens
    {
        return $that->andThen($this);
    }
}