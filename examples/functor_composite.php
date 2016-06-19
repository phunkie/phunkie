<?php

function functor_composite_examples() {
    $fa = Functor(Option);
    printLn($fa->map(Option(1), function($x) { return $x + 1; }));

    $fa = Functor(Option)->compose(ImmList);
    printLn($fa->map(Option(ImmList(1,2,3)), function($x) { return $x + 1; }));
}