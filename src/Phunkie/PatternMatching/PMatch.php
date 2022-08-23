<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\PatternMatching;

use Phunkie\PatternMatching\Referenced\GenericReferenced;
use Phunkie\PatternMatching\Referenced\ListWithTail;
use Phunkie\PatternMatching\Referenced\Some as ReferencedSome;
use Phunkie\PatternMatching\Wildcarded\Function1 as WildcardedFunction1;
use Phunkie\PatternMatching\Wildcarded\ImmList as WildcardedCons;
use Phunkie\PatternMatching\Referenced\ListNoTail;
use Phunkie\Types\Function1;
use Phunkie\Types\ImmList;
use Phunkie\Types\NonEmptyList;
use Phunkie\Types\Option;
use Phunkie\Types\Some;
use Phunkie\Validation\Failure;
use Phunkie\Validation\Success;

class PMatch
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

        for ($position = 0; $position < count($conditions); $position++) {
            if (!conditionIsValid($conditions[$position], $this->values[$position])) {
                return false;
            }
        }

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

function conditionIsValid($condition, $value) { return match (true) {
    $condition === _,
    matchSomeByReference($condition, $value),
    matchByReference($condition, $value),
    matchesNone($condition, $value),
    matchesNil($condition, $value),
    matchesWildcardedNel($condition, $value),
    matchesConsWildcardedHead($condition, $value),
    matchesConsWildcardedTail($condition, $value),
    matchesWildcardedFunction1($condition, $value),
    matchesWildcardedSome($condition, $value),
    matchesWildcardedFailure($condition, $value),
    matchesWildcardedSuccess($condition, $value),
    sameTypeSameValue($condition, $value) =>
        true,
    default => false };
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
    return gettype($condition) == gettype($value) &&
           ($value == $condition || ($condition instanceof ImmList && $condition->eqv($value)));
}

function matchSomeByReference($condition, $value)
{
    if ($condition instanceof ReferencedSome && $value instanceof Some) {
        $condition->value = $value->get();
        return true;
    }
    return false;
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
        $pmatch = new PMatch($value->tail());

        return $pmatch($condition->tail);
    }
    return false;
}

function matchesConsWildcardedTail($condition, $value)
{
    if ($condition instanceof WildcardedCons && $condition->tail == _ && $value instanceof ImmList) {
        $pmatch = new PMatch($value->head);
        return $pmatch($condition->head);
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
    if ($condition instanceof GenericReferenced) {
        return matchGenericByReference($condition, $value, $condition->class);
    }
    return match (true) {
        matchListByReference($condition, $value),
        matchListHeadByReference($condition, $value) =>
            true,
        default => false
    };
}

function matchGenericByReference($condition, $object, $class)
{
    if ($condition instanceof GenericReferenced && is_object($object) && get_class($object) === $class) {
        $reflected = new \ReflectionClass($object);
        $parameters = $reflected->getConstructor()->getParameters();
        for ($i = 1; $i <= count($parameters); $i++) {
            if (!$reflected->hasProperty($parameters[$i - 1]->getName())) {
                throw new \Error("To use generic pattern matching you have to name the constructor argument as you ".
                    "have named the class property");
            }
            if (isset(((array) $object)["\0$class\0{$parameters[$i - 1]->getName()}"])) {
                $condition->{"_$i"} = ((array)$object)["\0$class\0{$parameters[$i - 1]->getName()}"];
            } elseif (isset(((array)$object)["{$parameters[$i - 1]->getName()}"])) {
                $condition->{"_$i"} = ((array)$object)["{$parameters[$i - 1]->getName()}"];
            }
        }
        return true;
    }
    return false;
}

function matchListByReference($condition, $value)
{
    if ($condition instanceof ListWithTail && $value instanceof ImmList) {
        if ($condition->head == null) {
            $condition->head = $value->head;
        } elseif ($condition->head != $value->head) {
            return false;
        }
        if ($condition->tail == null) {
            $condition->tail = $value->tail;
        } elseif ($condition->tail != $value->tail) {
            return false;
        }
        return true;
    }

    return false;
}

function matchListHeadByReference($condition, $value)
{
    if ($condition instanceof ListNoTail && $value instanceof ImmList) {
        $condition->head = $value->head;
        if ($value->tail != Nil()) {
            return false;
        }
        return true;
    }
    return false;
}
