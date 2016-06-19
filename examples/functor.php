<?php

function functor_examples() {
    printLn(Some(1)->map(function($x) { return $x + 1;}));
    printLn(None()->map(function($x) { return $x + 1;}));
    printLn(ImmList(1,2,3)->map(function($x) { return $x + 1;}));
}