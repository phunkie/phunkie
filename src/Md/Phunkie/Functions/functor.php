<?php

use Md\Phunkie\Cats\Functor\FunctorComposite;

function Functor($type)  { return new FunctorComposite($type); }