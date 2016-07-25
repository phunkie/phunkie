<?php

namespace Md\Phunkie\PatternMatching;

use Md\Phunkie\PatternMatching\Wildcarded\Function1 as WildcardedFunction1;
use Md\Phunkie\PatternMatching\Wildcarded\Cons as WildcardedCons;
use Md\Phunkie\PatternMatching\Referenced\Cons as ReferencedCons;
use Md\Phunkie\PatternMatching\Referenced\ConsX as ReferencedConsX;
use Md\Phunkie\PatternMatching\Referenced\ConsXs as ReferencedConsXs;
use Md\Phunkie\Types\Function1;
use Md\Phunkie\Types\ImmList;
use Md\Phunkie\Types\NonEmptyList;
use Md\Phunkie\Types\Option;
use Md\Phunkie\Types\Some;
use Md\Phunkie\Validation\Failure;
use Md\Phunkie\Validation\Success;

class Match{
    private $value;
    public $Nil = Nil;
    public $_ = _;
    public function __construct($value) { $this->value = $value; }

    public function __invoke($condition)
    {
        if ($condition === _) return true;
        if ($this->matchByReference($condition)) return true;
        if ($this->matchesNone($condition)) return true;
        if ($this->matchesNil($condition)) return true;
        if ($this->matchesWildcardedNel($condition)) return true;
        if ($this->matchesConsWildcardedHead($condition)) return true;
        if ($this->matchesConsWildcardedTail($condition)) return true;
        if ($this->matchesWildcardedFunction1($condition)) return true;
        if ($this->matchesWildcardedSome($condition)) return true;
        if ($this->matchesWildcardedFailure($condition)) return true;
        if ($this->matchesWildcardedSuccess($condition)) return true;
        if ($this->sameTypeSameValue($condition)) return true;
        return false;
    }

    private function matchesWildcardedSome($condition)
    {
        return $condition instanceof Some && $condition == Some(_) && $this->value instanceof Some;
    }

    private function matchesWildcardedFunction1($condition)
    {
        return $condition instanceof WildcardedFunction1 && $this->value instanceof Function1;
    }

    private function sameTypeSameValue($condition)
    {
        return gettype($condition) == gettype($this->value) && $this->value == $condition;
    }

    private function matchesNone($condition)
    {
        return $condition == None && $this->value instanceof Option && $this->value == None();
    }

    private function matchesNil($condition)
    {
        return $condition == Nil && $this->value instanceof ImmList && $this->value == Nil();
    }

    private function matchesWildcardedFailure($condition)
    {
        return $condition instanceof Failure && $condition == Failure(_) && $this->value instanceof Failure;
    }

    private function matchesWildcardedSuccess($condition)
    {
        return $condition instanceof Success && $condition == Success(_) && $this->value instanceof Success;
    }

    private function matchesConsWildcardedHead($condition)
    {
        if ($condition instanceof WildcardedCons && $condition->head == _ && $this->value instanceof ImmList) {
            $match = new Match ($this->value->tail());
            return $match($condition->tail);
        }
        return false;
    }

    private function matchesConsWildcardedTail($condition)
    {
        if ($condition instanceof WildcardedCons && $condition->tail == _ && $this->value instanceof ImmList) {
            $match = new Match ($this->value->head);
            return $match($condition->head);
        }
        return false;
    }

    private function matchesWildcardedNel($condition)
    {
        return $condition instanceof NonEmptyList && $condition == Nel(_) &&
        $this->value instanceof NonEmptyList && $this->value->length > 0;
    }

    private function matchByReference($condition)
    {
        if ($condition instanceof ReferencedCons && $this->value instanceof ImmList) {
            if ($condition->head == null) $condition->head = $this->value->head;
            elseif ($condition->head != $this->value->head) return false;
            if ($condition->tail == null) $condition->tail = $this->value->tail;
            elseif ($condition->tail != $this->value->tail) return false;
            return true;
        }
        if ($condition instanceof ReferencedConsX && $this->value instanceof ImmList) {
            $condition->head = $this->value->head;
            if ($condition->tail != $this->value->tail) return false;
            return true;
        }
        if ($condition instanceof ReferencedConsXs && $this->value instanceof ImmList) {
            if ($condition->head != $this->value->head) return false;
            $condition->tail = $this->value->tail;
            return true;
        }
        return false;
    }
}