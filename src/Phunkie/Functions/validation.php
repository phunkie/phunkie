<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace {

    use Phunkie\Types\Option;
    use Phunkie\Validation\Failure;
    use Phunkie\Validation\Success;
    use Phunkie\Validation\Validation;
    use function Phunkie\Functions\currying\applyPartially;

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
        return applyPartially([$message], func_get_args(), function ($result) use ($message) {
            if (($result instanceof Option && $result == None()) || $result === null) {
                return Failure($message);
            }
            return Success($result);
        });
    }

    function Attempt(callable $f): Validation
    {
        try {
            $a = $f();
            return Success($a);
        } catch (\Throwable $e) {
            return Failure($e);
        }
    }
}

namespace Phunkie\Functions\validation {

    use Phunkie\Types\Option;
    use Phunkie\Validation\Validation;
    use function Phunkie\Functions\semigroup\combine;
    use function Phunkie\PatternMatching\Referenced\Success as Valid;
    use function Failure as Invalid;

    function apply(...$validations)
    {
        return call_user_func(
            ImmList(...$validations)->foldLeft(Unit()),
            function ($x, $y) {
                return combine($x, $y);
            }
        );
    }

    const toOption = "\\Phunkie\\Functions\\validation\\toOption";
    function toOption(Validation $v): Option
    {
        $on = pmatch($v);
        switch (true) {
        case $on(Valid($a)): return Some($a);
        case $on(Invalid(_)): return None(); }
    }
}
