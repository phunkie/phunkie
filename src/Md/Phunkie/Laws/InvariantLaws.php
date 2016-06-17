<?php

namespace Md\Phunkie\Laws;

use Md\Phunkie\Types\Function1;
use Md\Phunkie\Types\Kind;

trait InvariantLaws
{
    public function invariantIdentity(Kind $fa, $arg): bool
    {
        return $fa->eqv($fa->imap(Function1::identity(), Function1::identity()), $arg);
    }

    public function invariantComposition(Kind $fa, Function1 $f1, Function1 $f2, Function1 $g1, Function1 $g2): bool
    {
        return $fa->imap($f1, $f2)->imap($g1, $g2) == $fa->imap($g1->compose($f1), $f1->compose($g2));
    }
}