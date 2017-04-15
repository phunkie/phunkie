<?php

namespace {

    use Phunkie\Functions\comprehension\Bind;
    use Phunkie\Functions\comprehension\ForComprehension;

    function for_(...$binds)
    {
        return new ForComprehension($binds);
    }

    function __(&$_1 = _, &$_2 = _, &$_3 = _, &$_4 = _, &$_5 = _, &$_6 = _, &$_7 = _, &$_8 = _, &$_9 = _, &$_10 = _,
        &$_11 = _, &$_12 = _, &$_13 = _, &$_14 = _, &$_15 = _, &$_16 = _, &$_17 = _, &$_18 = _, &$_19 = _,
        &$_20 = _, &$_21 = _)
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

    class Bind {
        function __construct(&$_1 = _, &$_2 = _, &$_3 = _, &$_4 = _, &$_5 = _, &$_6 = _, &$_7 = _, &$_8 = _, &$_9 = _, &$_10 = _,
            &$_11 = _, &$_12 = _, &$_13 = _, &$_14 = _, &$_15 = _, &$_16 = _, &$_17 = _, &$_18 = _, &$_19 = _,
            &$_20 = _, &$_21 = _)
        {
            for ($i = 1; $i <= 21; $i++) {
                $this->{"_$i"} = &${"_$i"};
            }
        }

        function to($x)
        {
            if (!$x instanceof Tuple) {
                $this->_1 = $x;
            } else {
                for ($i = 1; $i <= $x->getArity(); $i++) {
                    $this->{"_$i"} = $x->{"_$i"};
                }
            }
        }

        function _($monad)
        {
            $monad->map(function($x) {
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

        public function yields(&$_1 = _, &$_2 = _, &$_3 = _, &$_4 = _, &$_5 = _, &$_6 = _, &$_7 = _, &$_8 = _, &$_9 = _, &$_10 = _,
            &$_11 = _, &$_12 = _, &$_13 = _, &$_14 = _, &$_15 = _, &$_16 = _, &$_17 = _, &$_18 = _, &$_19 = _,
            &$_20 = _, &$_21 = _)
        {
            $result = [];
            for ($i = 1; $i <= 21; $i++) {
                if (${"_$i"} !== _) {
                    $result[] = &${"_$i"};
                }
            }

            $loop = function($binds) use (&$loop, $result) {
                switch (count($binds)) {
                    case 0:
                        throw new \Error("for comprehension requires at least one binding");
                        break;
                    case 1:
                        $last = $binds[0];
                        return $last->monad->map(function ($x) use ($last, $result) {
                            $last->bind->to($x);
                            switch (count($result)) {
                                case 0:
                                    return Unit();
                                case 1:
                                    if ($result[0] === _) {
                                        return Unit();
                                    }
                                    return $result[0];
                                case 2:
                                    return Pair($result[0], $result[1]);
                                default:
                                    return Tuple(...$result);
                            }
                        });
                        break;
                    default:
                        $current = $binds[0];
                        return $current->monad->flatMap(function ($x) use ($binds, $current, $loop) {
                            $current->bind->to($x);
                            return $loop(array_slice($binds, 1));
                        });
                        break;
                }
            };
            return $loop($this->binds);
        }
    }
}
