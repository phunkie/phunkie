<?php

namespace Md\Phunkie\Types;

use Md\Phunkie\Cats\Show;
use function Md\Phunkie\Functions\get_value_to_show;
use Md\Phunkie\Ops\ImmList\ImmListEqOps;
use Md\Phunkie\Ops\ImmList\ImmListFunctorOps;

class ImmList implements Kind
{
    use Show;
    const kind = "ImmList";
    use ImmListFunctorOps, ImmListEqOps;
    private $values;
    public function __construct(...$values) { $this->values = $values; }
    public function toString(): string {
        return "List(". implode(",", $this->map(function($e) { return get_value_to_show($e); })->values) . ")";
    }
}