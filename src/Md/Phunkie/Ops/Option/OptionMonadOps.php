<?php

namespace Md\Phunkie\Ops\Option;

use Md\Phunkie\Types\Kind;

trait OptionMonadOps
{
    public function flatMap(callable $f): Kind
    {
        return $this->isEmpty() ? None() : ($f($this->get()) ?: None());
    }

    public function flatten(): Kind
    {
        return $this->flatMap(function($x) { return $x; });
    }
}