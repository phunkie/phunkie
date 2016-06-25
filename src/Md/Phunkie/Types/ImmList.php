<?php

namespace Md\Phunkie\Types;

use Md\Phunkie\Cats\Show;
use function Md\Phunkie\Functions\get_value_to_show;
use Md\Phunkie\Ops\ImmList\ImmListApplicativeOps;
use Md\Phunkie\Ops\ImmList\ImmListEqOps;

final class ImmList implements Kind
{
    use Show;
    const kind = "ImmList";
    use ImmListApplicativeOps, ImmListEqOps;
    private $values;
    public function __construct(...$values) { $this->values = $values; }
    private function isEmpty(): bool { return count($this->values) == 0; }
    public function toString(): string {
        return "List(". implode(",", $this->map(function($e) { return get_value_to_show($e); })->values) . ")";
    }

    /**
     * @param callable $condition
     * @return ImmList<T>
     */
    public function filter(callable $condition) { return ImmList(...array_filter($this->values, $condition)); }

    public function __get($property)
    {
        switch($property) {
            case 'length': return count($this->values);
        }
        throw new \Error("value $property is not a member of ImmList");
    }

    public function __set($property, $unused)
    {
        switch($property) {
            case 'length': throw new \BadMethodCallException("Can't change the value of members of a ImmList");
        }
        throw new \Error("value $property is not a member of ImmList");
    }

    public function toArray(): array { return $this->values; }

    /**
     * @param ImmList<T> $list
     * @return ImmList<Pair<T>>
     */
    public function zip(ImmList $list): ImmList
    {
        if ($this->length <= $list->length) {
            $other = $list->toArray();
            reset($other);
            return $this->map(function($x) use ($other) {
                $pair = Pair($x, current($other));
                next($other);
                return $pair;
            });
        }
        return $list->zip($this);
    }

    /**
     * @param int $index
     * @return ImmList<ImmList<T>>
     */
    public function splitAt(int $index)
    {
        switch(true) {
            case ($this->isEmpty()): return ImmList(ImmList(), ImmList());
            case ($index == 0): return ImmList(ImmList(), clone $this);
            case ($index >= count($this->values)): return ImmList(clone $this, ImmList());
            default:
                return ImmList(ImmList(...array_slice($this->values, 0, $index)), ImmList(...array_slice($this->values, $index)));
        }
    }

    public function partition(callable $condition): ImmList
    {
        $trues = $falses = [];
        foreach ($this->values as $value) {
            switch ($result = call_user_func($condition, $value)) {
                case (true):
                    $trues[] = $value;
                    break;
                case false:
                    $falses[] = $value;
                    break;
                default:
                    throw new \BadMethodCallException(sprintf("partition must be passed a callable returning a boolean, %s returned", gettype($result)));
            }
        }
        return ImmList(ImmList(...$trues), ImmList(...$falses));
    }
}