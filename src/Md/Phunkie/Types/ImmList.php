<?php

namespace Md\Phunkie\Types;

use Md\Phunkie\Std\ImmList\ImmListEqOps;
use Md\Phunkie\Std\ImmList\ImmListFunctorOps;

class ImmList implements Kind
{
    use ImmListFunctorOps, ImmListEqOps;
    private $values;
    public function __construct(...$values) { $this->values = $values; }
}