<?php

function foldable(): void {
    $list = ImmList(1, 2, 3, 4);
    printLn($list->foldLeft(0)(fn($acc, $x) => $acc + $x));
    printLn($list->foldRight(0)(fn($x, $acc) => $x + $acc));
    printLn($list->foldMap(fn($x) => $x + 1));
    printLn($list->fold(8)(fn($x, $y) => $x + $y));

    $option = Option(42);
    printLn($option->fold(
        0,               // Initial value if None
        fn($x) => $x * 2      // Function to apply if Some
    ));
    printLn($option->foldMap(fn($x) => $x + 1));
}
