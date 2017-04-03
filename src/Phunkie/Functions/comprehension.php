<?php

namespace {

    use Phunkie\Functions\comprehension\Bind;
    use Phunkie\Functions\comprehension\ForComprehension;

    function for_(...$binds)
    {
        return new ForComprehension(...$binds);
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

    class Bind
    {
        private $_1, $_2, $_3, $_4, $_5, $_6, $_7, $_8, $_9, $_10, $_11, $_12, $_13, $_14, $_15, $_16, $_17, $_18,
            $_19, $_20, $_21;

        public function __construct(&$_1 = _, &$_2 = _, &$_3 = _, &$_4 = _, &$_5 = _, &$_6 = _, &$_7 = _, &$_8 = _, &$_9 = _, &$_10 = _,
            &$_11 = _, &$_12 = _, &$_13 = _, &$_14 = _, &$_15 = _, &$_16 = _, &$_17 = _, &$_18 = _, &$_19 = _,
            &$_20 = _, &$_21 = _)
        {
            for ($i = 1; $i <= 21; $i++) {
                $this->{"_$i"} = &${"_$i"};
            }
        }

        public function _($value)
        {
            return $value->map(function($x) {
                if (!$x instanceof Tuple) {
                    $this->_1 = $x;
                } else {
                    for ($i = 1; $i <= $x->getArity(); $i++) {
                        $this->{"_$i"} = $x->{"_$i"};
                    }
                }
                return $x;
            });
        }
    }

    class ForComprehension
    {
        private $binds;

        public function __construct(...$binds)
        {
            $this->binds = $binds;
        }

        public function yields()
        {
            switch (func_num_args()) {
                case 0:
                    return Unit();
                case 1:
                    if (func_get_arg(0) === _) {
                        return Unit();
                    }
                    return func_get_arg(0);
                case 2:
                    return Pair(func_get_arg(0), func_get_arg(1));
                default:
                    return Tuple(...func_get_args());
            }
        }
    }
}
