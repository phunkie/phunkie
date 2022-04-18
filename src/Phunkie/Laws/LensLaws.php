<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Laws;

use Phunkie\Cats\Lens;

trait LensLaws
{
    public function identityLaw(Lens $l, $a, $b)
    {
        return $l->get($l->set($b, $a)) == $b;
    }

    public function retentionLaw(Lens $l, $a, $b, $c)
    {
        return $l->set($c, $l->set($b, $a)) == $l->set($c, $a);
    }

    public function doubleSetLaw(Lens $l, $a)
    {
        return $l->set($l->get($a), $a) == $a;
    }
}
