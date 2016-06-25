<?php

namespace Md\Phunkie\Cats\Functor;

use Md\Phunkie\Types\Kind;

interface Invariant
{
    public function imap(callable $f,callable $g): Kind;
}