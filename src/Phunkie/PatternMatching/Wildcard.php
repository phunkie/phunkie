<?php

/*
 * This file is part of Phunkie, library with functional structures for PHP.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phunkie\PatternMatching;

use Phunkie\Types\ImmMap;

class Wildcard
{
    private $member;

    public function __construct(string $member)
    {
        $this->member = $member;
    }

    public function __invoke($data)
    {
        if (is_object($data) && method_exists($data, "get" . $this->member)) {
            return $data->{"get$this->member"}();
        } elseif (is_object($data) && (new \ReflectionProperty($data, $this->member))->isPublic()) {
            return $data->{$this->member};
        } elseif ($data instanceof ImmMap && $data->offsetExists($this->member)) {
            return $data->get($this->member);
        }
        return None();
    }
}