<?php

namespace Md\Phunkie\Laws;

use Md\Phunkie\Cats\Lens;

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
        return $l->set($l->get($a),$a) == $a;
    }
}