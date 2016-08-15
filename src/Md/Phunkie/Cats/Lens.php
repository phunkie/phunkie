<?php

namespace Md\Phunkie\Cats;

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

    public function get($a)
    {
        return call_user_func($this->g, $a);
    }

    public function set($b, $a)
    {
        return call_user_func_array($this->s, [$b, $a]);
    }
}