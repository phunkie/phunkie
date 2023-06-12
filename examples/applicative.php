<?php

function applicative_examples()
{
    printLn(None()->pure(42));
    printLn(ImmList()->pure(42));

    printLn(Some(1)->apply(Some(fn ($x) => $x + 1)));
    printLn(None()->apply(Some(fn ($x) => $x + 1)));
    printLn(ImmList(1, 2, 3)->apply(ImmList(fn ($x) => $x + 1)));
    printLn(ImmList()->apply(ImmList(fn ($x) => $x + 1)));

    printLn(Some(1)->map2(Some(2), fn ($x, $y) => $x + $y));
    printLn(ImmList(1, 2, 3)->map2(ImmList(4, 5, 6), fn ($x, $y) => $x + $y));
}
