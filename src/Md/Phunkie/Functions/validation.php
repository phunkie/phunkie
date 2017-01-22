<?php

namespace {

    use function Md\Phunkie\Functions\currying\applyPartially;
    use Md\Phunkie\Types\Option;
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

    function SuccessNel(...$a)
    {
        return Nel(...$a)->success();
    }

    function Either($message)
    {
        return applyPartially([$message], func_get_args(), function($result) use ($message){
            if (($result instanceof Option && $result == None()) || $result === null) {
                return Failure($message);
            }
            return Success($result);
        });
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