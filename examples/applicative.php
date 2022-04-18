<?php

function applicative_examples()
{
    printLn(None()->pure(42));
    printLn(ImmList()->pure(42));

    printLn(Some(1)->apply(Some(function ($x) {
        return $x + 1;
    })));
    printLn(None()->apply(Some(function ($x) {
        return $x + 1;
    })));
    printLn(ImmList(1, 2, 3)->apply(ImmList(function ($x) {
        return $x + 1;
    })));
    printLn(ImmList()->apply(ImmList(function ($x) {
        return $x + 1;
    })));

    printLn(Some(1)->map2(Some(2), function ($x, $y) {
        return $x + $y;
    }));
    printLn(ImmList(1, 2, 3)->map2(ImmList(4, 5, 6), function ($x, $y) {
        return $x + $y;
    }));
}
