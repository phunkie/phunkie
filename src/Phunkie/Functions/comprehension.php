<?php

namespace {

    use Phunkie\Functions\comprehension\Bind;
    use Phunkie\Functions\comprehension\ForComprehension;

    /**
     * Creates a for-comprehension over monadic values.
     * 
     * For-comprehensions provide a more readable syntax for working with
     * nested monadic operations, similar to Scala's for-yield expressions.
     *
     * Example:
     * ```php
     * // Instead of nested flatMaps:
     * $users->flatMap(fn($user) =>
     *     $user->getOrders()->flatMap(fn($order) =>
     *         $order->getItems()
     *     )
     * );
     * 
     * // Use for-comprehension:
     * for_(
     *     __($user)->_($users),
     *     __($order)->_($user->getOrders()),
     *     __($items)->_($order->getItems())
     * )->yields($items);
     * ```
     *
     * @param Bind ...$binds The monadic bindings
     * @return ForComprehension The comprehension builder
     */
    function for_(...$binds)
    {
        return new ForComprehension($binds);
    }

    /**
     * Creates variable bindings for for-comprehension.
     * 
     * Allows binding values from monadic contexts to variables that
     * can be used in subsequent expressions.
     *
     * Example:
     * ```php
     * for_(
     *     __($x)->_(Some(1)),     // Bind x to Some(1)
     *     __($y)->_(Some($x + 1)) // Use x in next binding
     * )->yields($x + $y);         // Use both x and y
     * ```
     *
     * @param mixed &...$vars References to bind values to
     * @return Bind The binding object
     */
    function __(
        &$_1 = _,
        &$_2 = _,
        &$_3 = _,
        &$_4 = _,
        &$_5 = _,
        &$_6 = _,
        &$_7 = _,
        &$_8 = _,
        &$_9 = _,
        &$_10 = _,
        &$_11 = _,
        &$_12 = _,
        &$_13 = _,
        &$_14 = _,
        &$_15 = _,
        &$_16 = _,
        &$_17 = _,
        &$_18 = _,
        &$_19 = _,
        &$_20 = _,
        &$_21 = _
    )
    {
        $xs = [];
        for ($i = 1; $i <= 21; $i++) {
            $xs[] = &${"_$i"};
        }
        return new Bind(...$xs);
    }
}

namespace Phunkie\Functions\comprehension {

    use Phunkie\Types\Tuple;
    use const Phunkie\Functions\function1\identity;

    /**
     * Represents a monadic binding in a for-comprehension.
     * 
     * Binds values from monadic contexts to variables that can be
     * used in subsequent expressions.
     *
     * @property mixed $_1
     * @property mixed $_2
     * @property mixed $_3
     * @property mixed $_4
     * @property mixed $_5
     * @property mixed $_6
     * @property mixed $_7
     * @property mixed $_8
     * @property mixed $_9
     * @property mixed $_10
     * @property mixed $_11
     * @property mixed $_12
     * @property mixed $_13
     * @property mixed $_14
     * @property mixed $_15
     * @property mixed $_16
     * @property mixed $_17
     * @property mixed $_18
     * @property mixed $_19
     * @property mixed $_20
     * @property mixed $_21
     */
    class Bind
    {
        public $_1;
        public $_2;
        public $_3;
        public $_4;
        public $_5;
        public $_6;
        public $_7;
        public $_8;
        public $_9;
        public $_10;
        public $_11;
        public $_12;
        public $_13;
        public $_14;
        public $_15;
        public $_16;
        public $_17;
        public $_18;
        public $_19;
        public $_20;
        public $_21;

        /**
         * Creates a new binding.
         *
         * @param mixed &...$vars References to bind values to
         */
        public function __construct(
            &$_1 = _,
            &$_2 = _,
            &$_3 = _,
            &$_4 = _,
            &$_5 = _,
            &$_6 = _,
            &$_7 = _,
            &$_8 = _,
            &$_9 = _,
            &$_10 = _,
            &$_11 = _,
            &$_12 = _,
            &$_13 = _,
            &$_14 = _,
            &$_15 = _,
            &$_16 = _,
            &$_17 = _,
            &$_18 = _,
            &$_19 = _,
            &$_20 = _,
            &$_21 = _
        )
        {
            for ($i = 1; $i <= 21; $i++) {
                $this->{"_$i"} = &${"_$i"};
            }
        }

        /**
         * Assigns a value to the bound variables.
         *
         * @param mixed $x Value or tuple to assign
         */
        public function to($x)
        {
            if (!$x instanceof Tuple) {
                $this->_1 = $x;
            } else {
                for ($i = 1; $i <= $x->getArity(); $i++) {
                    $this->{"_$i"} = $x->{"_$i"};
                }
            }
        }

        /**
         * Creates a monadic context for this binding.
         *
         * @param mixed $monad The monadic value to bind from
         * @return MonadicContext The binding context
         */
        public function _($monad)
        {
            $monad->map(function ($x) {
                $this->to($x);
            });
            return new MonadicContext($this, $monad);
        }
    }

    /**
     * Represents a monadic context in a for-comprehension.
     * 
     * Holds the binding and monadic value for a single step in
     * the comprehension.
     */
    class MonadicContext
    {
        /** @var Bind The variable binding */
        public $bind;
        /** @var mixed The monadic value */
        public $monad;
        /** @var mixed The next context */
        public $next;

        public function __construct(Bind $bind, $monad)
        {
            $this->bind = $bind;
            $this->monad = $monad;
        }
    }

    /**
     * Builder for for-comprehensions.
     * 
     * Handles the construction and execution of for-comprehensions
     * over monadic values.
     */
    class ForComprehension
    {
        private $binds;

        public function __construct(array $binds)
        {
            $this->binds = $binds;
        }

        /**
         * Yields a value from the comprehension.
         * 
         * @param mixed &...$vars Values to yield
         * @return mixed The comprehension result
         */
        public function yields(
            &$_1 = _,
            &$_2 = _,
            &$_3 = _,
            &$_4 = _,
            &$_5 = _,
            &$_6 = _,
            &$_7 = _,
            &$_8 = _,
            &$_9 = _,
            &$_10 = _,
            &$_11 = _,
            &$_12 = _,
            &$_13 = _,
            &$_14 = _,
            &$_15 = _,
            &$_16 = _,
            &$_17 = _,
            &$_18 = _,
            &$_19 = _,
            &$_20 = _,
            &$_21 = _
        )
        {
            return $this->resolve(
                identity,
                $_1,
                $_2,
                $_3,
                $_4,
                $_5,
                $_6,
                $_7,
                $_8,
                $_9,
                $_10,
                $_11,
                $_12,
                $_13,
                $_14,
                $_15,
                $_16,
                $_17,
                $_18,
                $_19,
                $_20,
                $_21
            );
        }

        /**
         * Calls a function with the comprehension result.
         *
         * @param callable $f Function to call
         * @param mixed &...$vars Values to pass
         * @return mixed The function result
         */
        public function call(
            callable $f,
            &$_1 = _,
            &$_2 = _,
            &$_3 = _,
            &$_4 = _,
            &$_5 = _,
            &$_6 = _,
            &$_7 = _,
            &$_8 = _,
            &$_9 = _,
            &$_10 = _,
            &$_11 = _,
            &$_12 = _,
            &$_13 = _,
            &$_14 = _,
            &$_15 = _,
            &$_16 = _,
            &$_17 = _,
            &$_18 = _,
            &$_19 = _,
            &$_20 = _,
            &$_21 = _
        )
        {
            return $this->resolve(
                $f,
                $_1,
                $_2,
                $_3,
                $_4,
                $_5,
                $_6,
                $_7,
                $_8,
                $_9,
                $_10,
                $_11,
                $_12,
                $_13,
                $_14,
                $_15,
                $_16,
                $_17,
                $_18,
                $_19,
                $_20,
                $_21
            );
        }

        /**
         * Resolves the comprehension with a function and values.
         */
        private function resolve(
            callable $f,
            &$_1 = _,
            &$_2 = _,
            &$_3 = _,
            &$_4 = _,
            &$_5 = _,
            &$_6 = _,
            &$_7 = _,
            &$_8 = _,
            &$_9 = _,
            &$_10 = _,
            &$_11 = _,
            &$_12 = _,
            &$_13 = _,
            &$_14 = _,
            &$_15 = _,
            &$_16 = _,
            &$_17 = _,
            &$_18 = _,
            &$_19 = _,
            &$_20 = _,
            &$_21 = _
        )
        {
            $result = [];
            for ($i = 1; $i <= 21; $i++) {
                if (${"_$i"} !== _) {
                    $result[] = &${"_$i"};
                }
            }

            $loop = fn ($loop, $binds) => match(count($binds)) {
                0 => throw new \Error("for comprehension requires at least one binding"),
                1 => ($f = function() use (&$last, $result, $f, $binds) {
                        $last = $binds[0];
                        return $last->monad->map(function ($x) use ($last, $result, $f) {
                            $last->bind->to($x);
                            return match (count($result)) {
                                0 => Unit(),
                                1 => $result[0] === _ ? Unit() : $f($result[0]),
                                2 => $f === identity ? Pair($result[0], $result[1]) : $f($result[0], $result[1]),
                                default => $f === identity ? Tuple(...$result) : $f(...$result)
                            };
                        });
                    })(),
                default => $binds[0]->monad->flatMap(function ($x) use ($binds, &$loop) {
                        $binds[0]->bind->to($x);
                        return $loop($loop, array_slice($binds, 1));
                    })};
            return $loop($loop, $this->binds);
        }
    }
}
