<?php

function function1_examples()
{
    $f = Function1('strlen');
    printLn($f("hello"));
    $g = Function1(function ($x) {
        return $x % 2 === 0;
    });
    printLn($g($f("hello")));
    $h = $g->compose($f);
    printLn($h("hello"));
    $h = $f->andThen($g);
    printLn($h("hello"));
}
