<?php

function monad_examples()
{
    printLn(ImmList(1, 2, 3)->flatMap(fn ($x) => Some($x + 1)));
    printLn(ImmList(1, 2, 3)->flatMap(fn ($x) => $x % 2 === 0 ? None() : Some($x + 1)));
    printLn(ImmList(1, 2, 3)->flatMap(fn ($x) => None()));
    printLn(ImmList(1, 2, 3)->flatMap(fn ($x) => ImmList($x + 1, $x + 2)));
    printLn(Some(1)->flatMap(fn ($x) => Some($x + 1)));
    printLn(Some(1)->flatMap(fn ($x) => None()));
    printLn(None()->flatMap(fn ($x) => Some(42)));
    printLn(ImmList(1, 2, 3)->flatMap(fn ($x) => ImmList(Some($x + 1))));

    printLn(Some(Some(42))->flatten());
    printLn(ImmList(ImmList(1, 2, 3))->flatten());
}
