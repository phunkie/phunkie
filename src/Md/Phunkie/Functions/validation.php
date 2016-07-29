<?php

namespace {

    use Md\Phunkie\Validation\Failure;
    use Md\Phunkie\Validation\Success;

    function Failure($e)
    {
        return new Failure($e);
    }

    function FailureNel(...$e)
    {
        return Nel(...$e)->failure();
    }

    function Success($a)
    {
        return new Success($a);
    }

}

namespace Md\Phunkie\Functions\validation {

    use function Md\Phunkie\Functions\semigroup\combine;

    function apply(...$validations) {
        return call_user_func(ImmList(...$validations)->foldLeft(Unit()),
            function($x, $y) {
                return combine($x, $y);
            }
        );
    }
}