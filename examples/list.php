<?php

function list_examples()
{
    printLn(ImmList(2, 3, 4));
    printLn(ImmList(Some(1), None(), Some(3)));
}
