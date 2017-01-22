<?php

namespace Md\Phunkie\PatternMatching;

use Md\Phunkie\Types\ImmMap;

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