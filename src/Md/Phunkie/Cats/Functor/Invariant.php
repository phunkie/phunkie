<?php

namespace Md\Phunkie\Cats\Functor;

use Md\Phunkie\Types\Kind;

trait Invariant
{
    abstract public function imap(callable $f,callable $g): Kind;
}