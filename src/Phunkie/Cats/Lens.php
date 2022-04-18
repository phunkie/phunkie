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

class Lens
{
    private $g;
    private $s;

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

    public function mod(callable $f, $a)
    {
        return $this->set($f($this->get($a)), $a);
    }

    public function combine(Lens ...$other)
    {
        if (func_num_args() == 0) {
            return $this;
        }
        if (func_num_args() == 1) {
            return $this->andThen($other[0]);
        }
        return $this->andThen($other[0])->combine(array_slice($other, 1));
    }

    public function andThen(Lens $l): Lens
    {
        return new Lens(
            function ($a) use ($l) {
                return $l->get($this->get($a));
            },
            function ($c, $a) use ($l) {
                return $this->mod(function ($b) use ($l, $c) {
                    return $l->set($c, $b);
                }, $a);
            }
        );
    }

    public function compose(Lens $that): Lens
    {
        return $that->andThen($this);
    }
}
