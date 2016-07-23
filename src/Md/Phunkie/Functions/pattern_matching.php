<?php

namespace Md\Phunkie\Functions\pattern_matching;

use Error;
use Md\Phunkie\Functions\function1\WildcardedFunction1;
use function Md\Phunkie\Functions\immlist\concat;
use function Md\Phunkie\Functions\show\show;
use Md\Phunkie\Types\Function1;
use Md\Phunkie\Types\ImmList;
use Md\Phunkie\Types\Lazy;
use Md\Phunkie\Types\None;
use Md\Phunkie\Types\Some;
use Md\Phunkie\Validation\Failure;
use Md\Phunkie\Validation\Success;

require_once dirname(dirname(dirname(dirname(__DIR__)))) . "/vendor/autoload.php";

/**
 * @param mixed $value | array $conditions
 * @param array $conditions = null
 * @return mixed
 */
function matching(...$args) {
    if (count($args) == 0) throw new Error("matching takes 1 or more arguments, 0 given");

    $conditions = [];
    $value = true;

    if (matching_only_with_conditions($args)) {
        $conditions = $args;
    } elseif (matching_with_values_and_conditions($args)) {
        $value = $args[0];
        $conditions = array_slice($args, 1);
    }

    if ($conditions != []) { return (new Matched($value, $conditions))->getResult(); }

    throw new Error("invalid syntax in matching. At least 1 condition is required.");
}

function matching_only_with_conditions($args)
{
    if (is_object($args[0]) && $args[0] instanceof ConditionBehaviour) {
        foreach ($args as $arg)
            if (!$arg instanceof ConditionBehaviour) return false;
        return true;
    }
    return false;
}

function matching_with_values_and_conditions($args)
{
    if (count($args) > 1 && (!is_object($args[0]) || (is_object($args[0]) && !$args[0] instanceof ConditionBehaviour))) {
        foreach (array_slice($args, 1) as $arg)
            if (!$arg instanceof ConditionBehaviour)  return false;
        return true;
    }
    return false;
}

function on($condition) {
    return new Condition($condition);
}

final class ConditionPair
{
    private $_1;
    private $_2;
    public function __construct($_1, $_2) { $this->_1 = $_1; $this->_2 = $_2; }
    public function toList() { return concat($this->_1, $this->_2); }
}

final class Condition {
    private $conditions = [];
    public function __construct($conditions) {
        $this->conditions = $conditions instanceof ConditionPair ? $conditions->toList() : ImmList($conditions);
    }
    public function returns($result) { return new ReturnableValue($this->conditions, $result); }
    public function throws($e) { return new ThrowableMessage($this->conditions, $e); }
    public function or($condition) { return new Condition(new ConditionPair($this->conditions, $condition)); }
}

interface ConditionBehaviour {
    public function isThrowable();
    public function anyMatch($value);
    public function isWildcardedFunction1();
    public function isWildcardedSome();
    public function isWildcardedList();
    public function isWildcardedFor($class);
}

abstract class BasicConditionBehaviour {
    protected $conditions;
    public function getConditions(): ImmList { return $this->conditions; }
    public function anyMatch($value) {
        return (bool)$this->getConditions()->filter(function($condition) use ($value) {
            return gettype($condition) == gettype($value) && $condition == $value;
        })->length;
    }
    public function isWildcardedFunction1() {
        return (bool)$this->getConditions()->filter(function($condition) {
            return $condition instanceof WildcardedFunction1;
        })->length;
    }
    public function isWildcardedSome() {
        return (bool)$this->getConditions()->filter(function($condition) {
            return $condition instanceof Some && $condition == Some(_);
        })->length;
    }
    public function isWildcardedList() {
        return (bool)$this->getConditions()->filter(function($condition) {
            return $condition instanceof ImmList && $condition == ImmList(_);
        })->length;
    }
    public function isWildcardedFor($class) {
        $reflection = new \ReflectionClass($class);
        return (bool)$this->getConditions()->filter(function($condition) use ($class, $reflection) {
            $namespacePieces = explode("\\", $class);
            $constructor = $namespacePieces[count($namespacePieces) - 1];
            if (function_exists($constructor)) {
                $instance = $constructor(_);
            }
            return is_a($condition, $class) && $condition == $instance;
        })->length;
    }
}

final class ReturnableValue extends BasicConditionBehaviour implements ConditionBehaviour {
    protected $value;
    public function __construct(ImmList $condition, $value) { $this->conditions = $condition; $this->value = $value; }
    public function getValue() { return $this->value instanceof Lazy ? $this->value->run() : $this->value;  }
    public function isThrowable() { return false; }
}
final class ThrowableMessage extends BasicConditionBehaviour implements ConditionBehaviour {
    protected $e;
    public function __construct(ImmList $condition, $e) { $this->e = $e; $this->conditions = $condition; }
    public function throwThrowable() { throw $this->e instanceof Lazy ? $this->e->run() : $this->e; }
    public function getValue() { return $this->e instanceof Lazy ? $this->e->run() : $this->e; }
    public function isThrowable() { return true; }
}

final class MatchError extends \RuntimeException {}

final class Matched {
    private $value;
    private $conditions;

    public function __construct($value, array $conditions) {
        $this->value = $value;
        $this->conditions = ImmList(...$conditions);
    }

    public function getResult()
    {
        $validResults = $this->collectValidResults();

        if ($validResults->isEmpty()) {
            throw new MatchError("No matches found");
        }

        return $validResults->head()->getValue();
    }

    /**
     * @return ImmList<Result>
     */
    private function collectValidResults(): ImmList
    {
        foreach ($this->conditions->toArray() as $condition) {
            if ($this->checkCondition($condition)) {
                return ImmList($condition);
            }
        }
        return ImmList();
    }

    private function checkCondition (ConditionBehaviour $conditionBehaviour) {
        if ($conditionBehaviour->isThrowable() && $conditionBehaviour->anyMatch($this->value)) {
            $conditionBehaviour->throwThrowable();
        }
        if ($conditionBehaviour->anyMatch(_)) return true;
        if ($conditionBehaviour->anyMatch(None) && $this->value instanceof None) return true;
        if ($conditionBehaviour->isWildcardedFunction1() && $this->value instanceof Function1) return true;
        if ($conditionBehaviour->isWildcardedFor(Some::class) && $this->value instanceof Some) return true;
        if ($conditionBehaviour->isWildcardedFor(ImmList::class) && $this->value instanceof ImmList) return true;
        if ($conditionBehaviour->isWildcardedFor(Success::class) && $this->value instanceof Success) return true;
        if ($conditionBehaviour->isWildcardedFor(Failure::class) && $this->value instanceof Failure) return true;

        return $conditionBehaviour->anyMatch($this->value);
    }
}
