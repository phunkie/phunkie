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

use Phunkie\Cats\Applicative;
use Phunkie\Algebra\Eq;
use const Phunkie\Functions\function1\identity;

trait ApplicativeLaws
{
    public function applicativeIdentity(Eq|Applicative  $fa): bool
    {
        return $fa->apply($fa->pure(identity))->eqv($fa, Some(42));
    }

    public function applicativeHomomorphism(Eq|Applicative $fa, $a, \Closure $f): bool
    {
        return $fa->pure($a)->apply($fa->pure($f))->eqv($fa->pure($f($a)), Some(42));
    }

    public function applicativeInterchange(Eq|Applicative $fa, $a, Eq|Applicative $fab): bool
    {
        return $fa->pure($a)
                    ->apply($fab)->eqv(
                        $fab->apply(
                            $fa->pure(
                                fn ($f) => $f($a)
                            )
                        ),
                        Some(42)
                    );
    }

    public function applicativeMap(Eq|Applicative $fa, \Closure $f): bool
    {
        return $fa->map($f)->eqv($fa->apply($fa->pure($f)), Some(42));
    }
}
