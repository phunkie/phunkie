<?php

namespace Md\Phunkie\PatternMatching;

use const Md\Phunkie\Functions\function1\identity;
use Md\Phunkie\PatternMatching\Referenced\Failure as ReferencedFailure;
use Md\Phunkie\PatternMatching\Referenced\ListWithTail;
use Md\Phunkie\PatternMatching\Referenced\Some as ReferencedSome;
use Md\Phunkie\PatternMatching\Referenced\Success as ReferencedSuccess;
use Md\Phunkie\PatternMatching\Wildcarded\Function1 as WildcardedFunction1;
use Md\Phunkie\PatternMatching\Wildcarded\ImmList as WildcardedCons;
use Md\Phunkie\PatternMatching\Referenced\ListNoTail;
use Md\Phunkie\Types\Function1;
use Md\Phunkie\Types\ImmList;
use Md\Phunkie\Types\NonEmptyList;
use Md\Phunkie\Types\Option;
use Md\Phunkie\Types\Some;
use Md\Phunkie\Validation\Failure;
use Md\Phunkie\Validation\Success;

class Match
{
    private $values;

    public function __construct(...$values)
    {
        $this->values = $values;
    }

    public function __invoke(...$conditions): bool
    {
        $conditions = $this->wildcardGuard($conditions);
        $this->guardNumberOfConditionsAndValuesNotEqual($conditions);

        for ($position = 0; $position < count($conditions); $position++)
            if (!conditionIsValid($conditions[$position], $this->values[$position]))
                return false;

        return true;
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

function conditionIsValid($condition, $value)
{
    switch (true) {
        case $condition === _:
        case matchByReference($condition, $value):
        case matchesNone($condition, $value):
        case matchesNil($condition, $value):
        case matchesWildcardedNel($condition, $value):
        case matchesConsWildcardedHead($condition, $value):
        case matchesConsWildcardedTail($condition, $value):
        case matchesWildcardedFunction1($condition, $value):
        case matchesWildcardedSome($condition, $value):
        case matchesWildcardedFailure($condition, $value):
        case matchesWildcardedSuccess($condition, $value):
        case sameTypeSameValue($condition, $value):
            return true;
        default: return false;
    }
}

function matchesWildcardedSome($condition, $value)
{
    return $condition instanceof Some && $condition == Some(_) && $value instanceof Some;
}

function matchesWildcardedFunction1($condition, $value)
{
    return $condition instanceof WildcardedFunction1 && $value instanceof Function1;
}

function sameTypeSameValue($condition, $value)
{
    return gettype($condition) == gettype($value) && $value == $condition;
}

function matchesNone($condition, $value)
{
    return $condition == None && $value instanceof Option && $value == None();
}

function matchesNil($condition, $value)
{
    return $condition == Nil && $value instanceof ImmList && $value == Nil();
}

function matchesWildcardedFailure($condition, $value)
{
    return $condition instanceof Failure && $condition == Failure(_) && $value instanceof Failure;
}

function matchesWildcardedSuccess($condition, $value)
{
    return $condition instanceof Success && $condition == Success(_) && $value instanceof Success;
}

function matchesConsWildcardedHead($condition, $value)
{
    if ($condition instanceof WildcardedCons && $condition->head == _ && $value instanceof ImmList) {
        $match = new Match ($value->tail());
        return $match($condition->tail);
    }
    return false;
}

function matchesConsWildcardedTail($condition, $value)
{
    if ($condition instanceof WildcardedCons && $condition->tail == _ && $value instanceof ImmList) {
        $match = new Match ($value->head);
        return $match($condition->head);
    }
    return false;
}

function matchesWildcardedNel($condition, $value)
{
    return $condition instanceof NonEmptyList && $condition == Nel(_) &&
    $value instanceof NonEmptyList && $value->length > 0;
}

function matchByReference($condition, $value)
{
    switch (true) {
        case matchListByReference($condition, $value):
        case matchListHeadByReference($condition, $value):
        case matchSomeByReference($condition, $value):
        case matchValidationByReference($condition, $value):
            return true;
        default: return false;
    }
}

function matchValidationByReference($condition, $value) {
    if (($condition instanceof ReferencedSuccess && $value instanceof Success) ||
        ($condition instanceof ReferencedFailure && $value instanceof Failure)) {
        $condition->value = $value->map(identity);
        return true;
    }
    return false;
}

function matchSomeByReference($condition, $value) {
    if ($condition instanceof ReferencedSome && $value instanceof Some) {
        $condition->value = $value->get();
        return true;
    }
    return false;
}

function matchListByReference($condition, $value) {
    if ($condition instanceof ListWithTail && $value instanceof ImmList) {
        if ($condition->head == null) $condition->head = $value->head;
        elseif ($condition->head != $value->head) return false;
        if ($condition->tail == null) $condition->tail = $value->tail;
        elseif ($condition->tail != $value->tail) return false;
        return true;
    }

    return false;
}

function matchListHeadByReference($condition, $value) {
    if ($condition instanceof ListNoTail && $value instanceof ImmList) {
        $condition->head = $value->head;
        if ($value->tail != Nil()) return false;
        return true;
    }
    return false;
}