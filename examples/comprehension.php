<?php

function letter($x)
{
    return Option($x);
}

function uppercase($x)
{
    return Option(strtoupper($x));
}

function quote($x)
{
    return Option('"' . $x . '"');
}

function comprehension_examples()
{
    for_(__($l) ->_(letter('x')), __($u) ->_(uppercase($l)), __($res) ->_(quote($u)))
    -> yields($res);

    printLn($res);
}
