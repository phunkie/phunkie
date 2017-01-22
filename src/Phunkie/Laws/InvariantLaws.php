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

use const Phunkie\Functions\function1\identity;
use Phunkie\Types\Function1;
use Phunkie\Types\Kind;

trait InvariantLaws
{
    public function invariantIdentity(Kind $fa): bool
    {
        return $fa->eqv($fa->imap(identity, identity), Some(42));
    }

    /**
     * @param Kind $fa
     * @param Function1<Int,String> $f1
     * @param Function1<String,Int> $f2
     * @param Function1<String,Bool> $g1
     * @param Function1<Bool,String> $g2
     * @return bool
     */
    public function invariantComposition(Kind $fa, Function1 $f1, Function1 $f2, Function1 $g1, Function1 $g2): bool
    {
        return $fa->imap($f1, $f2)->imap($g1, $g2)->eqv(
            $fa->imap($g1->compose($f1), $f2->compose($g2)), Some(42));
    }
}