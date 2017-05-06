<?php

namespace Phunkie\PatternMatching\Referenced;

class GenericReferenced
{
    const UNUSED_ARGUMENT = "GenericReferenced::UNUSED_ARGUMENT";
    public $_1, $_2, $_3, $_4, $_5, $_6, $_7, $_8, $_9, $_10, $_11, $_12, $_13, $_14,
        $_15, $_16, $_17, $_18, $_19, $_20, $_21;
    public $class;

    public function __construct($class,
        &$_1 = self::UNUSED_ARGUMENT,
        &$_2 = self::UNUSED_ARGUMENT,
        &$_3 = self::UNUSED_ARGUMENT,
        &$_4 = self::UNUSED_ARGUMENT,
        &$_5 = self::UNUSED_ARGUMENT,
        &$_6 = self::UNUSED_ARGUMENT,
        &$_7 = self::UNUSED_ARGUMENT,
        &$_8 = self::UNUSED_ARGUMENT,
        &$_9 = self::UNUSED_ARGUMENT,
        &$_10 = self::UNUSED_ARGUMENT,
        &$_11 = self::UNUSED_ARGUMENT,
        &$_12 = self::UNUSED_ARGUMENT,
        &$_13 = self::UNUSED_ARGUMENT,
        &$_14 = self::UNUSED_ARGUMENT,
        &$_15 = self::UNUSED_ARGUMENT,
        &$_16 = self::UNUSED_ARGUMENT,
        &$_17 = self::UNUSED_ARGUMENT,
        &$_18 = self::UNUSED_ARGUMENT,
        &$_19 = self::UNUSED_ARGUMENT,
        &$_20 = self::UNUSED_ARGUMENT,
        &$_21 = self::UNUSED_ARGUMENT)
    {
        for ($i = 1; $i <= 21; $i++) {
            $this->{"_$i"} = &${"_$i"};
        }
        $this->class = $class;
    }
}