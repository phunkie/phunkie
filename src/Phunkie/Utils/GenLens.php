<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\Utils;

use Phunkie\Cats\Lens;
use Phunkie\Types\ImmMap;
use Phunkie\Types\Pair;
use Phunkie\Types\Some;

final class GenLens
{
    private $modifierToken = false;

    public function __construct(...$fields)
    {
        foreach ($fields as $field) {
            $g = function ($data) use ($field) {
                if ($data instanceof ImmMap || ($data instanceof Some && $data->get() instanceof ImmMap)) {
                    if ($data instanceof Some) {
                        return $data->get()->get($field);
                    }
                    return $data->get($field);
                }
                if ($data instanceof Pair) {
                    return $data->$field;
                }
                $getter = "get{$field}";

                return $data->$getter();
            };
            $s = function ($newValue, Copiable $data) use ($field) {
                return $data->copy([$field => $newValue]);
            };
            $this->addLens($field, new Lens($g, $s));
        }
    }

    public function __get(string $lens)
    {
        if (!isset($this->$lens)) {
            throw new \Error("Lens $lens has not been configured.");
        }
    }

    public function __set(string $name, Lens $lens)
    {
        if (!$this->modifierToken) {
            throw new \Error("Lenses are immutable.");
        }
        $this->$name = $lens;
    }

    private function addLens(string $name, Lens $lens)
    {
        $this->modifierToken = true;
        $this->__set($name, $lens);
        $this->modifierToken = false;
    }
}
