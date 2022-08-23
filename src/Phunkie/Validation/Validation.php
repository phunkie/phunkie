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
    public function isRight(): bool { return match (true) {
        $this instanceof Failure => false,
        $this instanceof Success => true,
        default => throw new TypeError("Validation cannot be extended outside namespace") };
    }

    public function isLeft(): bool { return match (true) {
        $this instanceof Success => false,
        $this instanceof Failure => true,
        default => throw new TypeError("Validation cannot be extended outside namespace") };
    }

    public function getTypeArity(): int
    {
        return 2;
    }

    public function getTypeVariables(): array { $on = pmatch($this); return match (true) {
        $on(Valid($a)) => ['E', showType($a)],
        $on(Invalid($e)) => [showType($e), 'A']};
    }

    public function combine(Validation $that): Validation { $on = pmatch($this, $that); return match (true) {
        $on(Valid($a), Valid($b)) => Success(combine($a, $b)),
        $on(Invalid($x), Invalid($y)) => Failure(combine($x, $y)),
        $on(Failure(_), _) => $this,
        $on(_) => $that };
    }

    public function toOption(): Option { $on = pmatch($this); return match (true) {
        $on(Valid($a)) => Some($a),
        $on(Failure(_)) => None() };
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
        return ($this->foldLeft(zero($this->getOrElse(null))))(fn ($b, $a) => combine($b, $f($a)));
    }
}
