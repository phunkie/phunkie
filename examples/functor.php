<?php

function functor_examples()
{
    printLn(Some(1)->map(fn ($x) => $x + 1));
    printLn(None()->map(fn ($x) => $x + 1));
    printLn(ImmList(1, 2, 3)->map(fn ($x) => $x + 1));
    printLn(ImmList(1, 2, 3)->zipWith(fn ($x) => $x + 1));
}
