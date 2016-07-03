<?php

function monad_examples()
{
    printLn(ImmList(1,2,3)->flatMap(function($x) { return Some($x + 1); }));
    printLn(ImmList(1,2,3)->flatMap(function($x) { return $x % 2 === 0 ? None() : Some($x + 1); }));
    printLn(ImmList(1,2,3)->flatMap(function($x) { return None(); }));
    printLn(ImmList(1,2,3)->flatMap(function($x) { return ImmList($x + 1, $x + 2); }));
    printLn(Some(1)->flatMap(function($x) { return Some($x + 1); }));
    printLn(Some(1)->flatMap(function($x) { return None(); }));
    printLn(None()->flatMap(function($x) { return Some(42); }));
    printLn(ImmList(1,2,3)->flatMap(function($x) { return ImmList(Some($x + 1)); }));

    printLn(Some(Some(42))->flatten());
    printLn(ImmList(ImmList(1,2,3))->flatten());
}