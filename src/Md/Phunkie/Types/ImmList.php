<?php

namespace Md\Phunkie\Types;

use Md\Phunkie\Ops\ImmList\ImmListEqOps;
use Md\Phunkie\Ops\ImmList\ImmListFunctorOps;

class ImmList implements Kind
{
    use ImmListFunctorOps, ImmListEqOps;
    private $values;
    public function __construct(...$values) { $this->values = $values; }
}