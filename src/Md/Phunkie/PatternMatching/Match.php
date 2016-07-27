<?php

namespace Md\Phunkie\PatternMatching;

use Md\Phunkie\PatternMatching\Referenced\Failure as ReferencedFailure;
use Md\Phunkie\PatternMatching\Referenced\Some as ReferencedSome;
use Md\Phunkie\PatternMatching\Referenced\Success as ReferencedSuccess;
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
    private $values;
    public $Nil = Nil;
    public $_ = _;
    public function __construct(...$values) { $this->values = $values; }

    public function __invoke(...$conditions)
    {
        $conditions = $this->wildcardGuard($conditions);
        $this->guardNumberOfConditionsAndValuesNotEqual($conditions);

        for ($position = 0; $position < count($conditions); $position++)
            if (!$this->conditionIsValid($conditions[$position], $this->values[$position]))
                return false;

        return true;
    }

    private function matchesWildcardedSome($condition, $value)
    {
        return $condition instanceof Some && $condition == Some(_) && $value instanceof Some;
    }

    private function matchesWildcardedFunction1($condition, $value)
    {
        return $condition instanceof WildcardedFunction1 && $value instanceof Function1;
    }

    private function sameTypeSameValue($condition, $value)
    {
        return gettype($condition) == gettype($value) && $value == $condition;
    }

    private function matchesNone($condition, $value)
    {
        return $condition == None && $value instanceof Option && $value == None();
    }

    private function matchesNil($condition, $value)
    {
        return $condition == Nil && $value instanceof ImmList && $value == Nil();
    }

    private function matchesWildcardedFailure($condition, $value)
    {
        return $condition instanceof Failure && $condition == Failure(_) && $value instanceof Failure;
    }

    private function matchesWildcardedSuccess($condition, $value)
    {
        return $condition instanceof Success && $condition == Success(_) && $value instanceof Success;
    }

    private function matchesConsWildcardedHead($condition, $value)
    {
        if ($condition instanceof WildcardedCons && $condition->head == _ && $value instanceof ImmList) {
            $match = new Match ($value->tail());
            return $match($condition->tail);
        }
        return false;
    }

    private function matchesConsWildcardedTail($condition, $value)
    {
        if ($condition instanceof WildcardedCons && $condition->tail == _ && $value instanceof ImmList) {
            $match = new Match ($value->head);
            return $match($condition->head);
        }
        return false;
    }

    private function matchesWildcardedNel($condition, $value)
    {
        return $condition instanceof NonEmptyList && $condition == Nel(_) &&
        $value instanceof NonEmptyList && $value->length > 0;
    }

    private function matchByReference($condition, $value)
    {
        if ($condition instanceof ReferencedCons && $value instanceof ImmList) {
            if ($condition->head == null) $condition->head = $value->head;
            elseif ($condition->head != $value->head) return false;
            if ($condition->tail == null) $condition->tail = $value->tail;
            elseif ($condition->tail != $value->tail) return false;
            return true;
        }
        if ($condition instanceof ReferencedConsX && $value instanceof ImmList) {
            $condition->head = $value->head;
            if ($condition->tail != $value->tail) return false;
            return true;
        }
        if ($condition instanceof ReferencedConsXs && $value instanceof ImmList) {
            if ($condition->head != $value->head) return false;
            $condition->tail = $value->tail;
            return true;
        }
        if ($condition instanceof ReferencedSome && $value instanceof Some) {
            $condition->value = $value->get();
            return true;
        }
        if (($condition instanceof ReferencedSuccess && $value instanceof Success) ||
            ($condition instanceof ReferencedFailure && $value instanceof Failure)) {
            $condition->value = $value->map(Function1::identity());
            return true;
        }
        return false;
    }

    private function conditionIsValid($condition, $value)
    {
        if ($condition === _) {
            return true;
        }
        if ($this->matchByReference($condition, $value)) {
            return true;
        }
        if ($this->matchesNone($condition, $value)) {
            return true;
        }
        if ($this->matchesNil($condition, $value)) {
            return true;
        }
        if ($this->matchesWildcardedNel($condition, $value)) {
            return true;
        }
        if ($this->matchesConsWildcardedHead($condition, $value)) {
            return true;
        }
        if ($this->matchesConsWildcardedTail($condition, $value)) {
            return true;
        }
        if ($this->matchesWildcardedFunction1($condition, $value)) {
            return true;
        }
        if ($this->matchesWildcardedSome($condition, $value)) {
            return true;
        }
        if ($this->matchesWildcardedFailure($condition, $value)) {
            return true;
        }
        if ($this->matchesWildcardedSuccess($condition, $value)) {
            return true;
        }
        if ($this->sameTypeSameValue($condition, $value)) {
            return true;
        }
        return false;
    }

    private function wildcardGuard($conditions)
    {
        if (count($conditions) == 1 && $conditions[0] == _ && count($conditions) < count($this->values)) {
            return array_fill(0, count($this->values), _);
        }
        return $conditions;
    }

    private function guardNumberOfConditionsAndValuesNotEqual($conditions)
    {
        if (count($conditions) != count($this->values)) {
            throw new \Error("number of conditions must equal number of arguments in match.");
        }
    }
}