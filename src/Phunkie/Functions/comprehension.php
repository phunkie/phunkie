<?php

namespace {

    use Phunkie\Functions\comprehension\Bind;
    use Phunkie\Functions\comprehension\ForComprehension;

    function for_(...$binds)
    {
        return new ForComprehension($binds);
    }

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

    class Bind
    {
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

        public function _($monad)
        {
            $monad->map(function ($x) {
                $this->to($x);
            });
            return new MonadicContext($this, $monad);
        }
    }

    class MonadicContext
    {
        public $bind;
        public $monad;
        public $next;

        public function __construct(Bind $bind, $monad)
        {
            $this->bind = $bind;
            $this->monad = $monad;
        }
    }

    class ForComprehension
    {
        private $binds;
        public function __construct(array $binds)
        {
            $this->binds = $binds;
        }

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
