<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Validation;

use Phunkie\Cats\Applicative;
use Phunkie\Cats\Foldable;
use Phunkie\Cats\Monad;
use Phunkie\Cats\Show;
use Phunkie\Ops\FunctorOps;
use Phunkie\Types\Kind;
use Phunkie\Types\Option;
use TypeError;
use function Phunkie\Functions\semigroup\combine;
use function Phunkie\Functions\semigroup\zero;
use function Phunkie\Functions\show\showType;
use function Phunkie\PatternMatching\Referenced\Success as Valid;
use function Phunkie\PatternMatching\Referenced\Failure as Invalid;

abstract class Validation implements Applicative, Monad, Kind, Foldable
{
    use Show;
    use FunctorOps;
    public const kind = "Validation";
    public function isRight(): bool
    {
        switch (true) {
        case $this instanceof Failure: return false;
        case $this instanceof Success: return true;
        default: throw new TypeError("Validation cannot be extended outside namespace"); }
    }

    public function isLeft(): bool
    {
        switch (true) {
        case $this instanceof Success: return false;
        case $this instanceof Failure: return true;
        default: throw new TypeError("Validation cannot be extended outside namespace"); }
    }

    public function getTypeArity(): int
    {
        return 2;
    }

    public function getTypeVariables(): array
    {
        $on = pmatch($this);
        switch (true) {
        case $on(Valid($a)): return ['E', showType($a)];
        case $on(Invalid($e)): return [showType($e), 'A'];}
    }

    public function combine(Validation $that): Validation
    {
        $on = pmatch($this, $that);
        switch (true) {
        case $on(Valid($a), Valid($b)): return Success(combine($a, $b));
        case $on(Invalid($x), Invalid($y)): return Failure(combine($x, $y));
        case $on(Failure(_), _): return $this;
        case $on(_): return $that;}
    }

    public function toOption(): Option
    {
        $on = pmatch($this);
        switch (true) {
        case $on(Valid($a)): return Some($a);
        case $on(Failure(_)): return None(); }
    }

    abstract public function getOrElse($default);
    abstract public function map(callable $f): Kind;
    public function imap(callable $f, callable $g): Kind
    {
        return $this->map($f);
    }

    public function foldLeft($initial)
    {
        return $this->fold($initial);
    }

    public function foldRight($initial)
    {
        return $this->fold($initial);
    }

    public function foldMap(callable $f)
    {
        return ($this->foldLeft(zero($this->getOrElse(null))))(function ($b, $a) use ($f) {
            return combine($b, $f($a));
        });
    }
}
