<?php

namespace Md\Phunkie\Validation;

use Md\Phunkie\Cats\Show;
use function Md\Phunkie\Functions\semigroup\combine;
use function Md\Phunkie\PatternMatching\Referenced\Success as rSuccess;
use function Md\Phunkie\PatternMatching\Referenced\Failure as rFailure;
use TypeError;

abstract class Validation
{
    use Show;
    public function isRight(): bool { switch (true) {
        case $this instanceof Failure: return false;
        case $this instanceof Success: return true;
        default: throw new TypeError("Validation cannot be extended outside namespace"); }
    }

    public function isLeft(): bool { switch (true) {
        case $this instanceof Success: return false;
        case $this instanceof Failure: return true;
        default: throw new TypeError("Validation cannot be extended outside namespace"); }
    }

    public function combine(Validation $that): Validation { $on = match($this, $that); switch(true) {
        case $on(rSuccess($a), rSuccess($b)): return Success(combine($a, $b));
        case $on(rFailure($a), rFailure($b)): return Failure(combine($a, $b));
        case $on(Failure(_), _): return $this;
        case $on(_): return $that;}
    }

    public function fold($fe, $fa) { $on = match($this); switch(true) {
        case $on(rSuccess($a)): return $fa($a);
        case $on(rFailure($a)): return $fe($a); }
    }

    abstract public function getOrElse($default);
    abstract public function map($f);
}