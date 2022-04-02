<?php

namespace Md\Unit;

trait AssertIsLike
{
    public function assertIsLike(object $a, object $b): void
    {
        $this->addToAssertionCount(1);
        $result = false;

        try {
            $result = ($this->getProps($a) === $this->getProps($b)) ?
                true :
                ($this->getPropsDeep($a) === $this->getPropsDeep($b));
        } catch (\Throwable $_) {
            $result = false;
            $this->fail("Failed asserting that the two objects are equal");
        }

        $this->assertTrue($result);
    }

    public function assertPropertyCount(int $count, object $a)
    {
        $this->addToAssertionCount(1);
        $result = false;

        try {
            $result = \count(\array_merge(...$this->getPropsDeep($a))) === $count;
        } catch (\Throwable $_) {
            $result = false;
            $this->fail("Failed asserting that the two objects have the same property counts");
        }

        $this->assertTrue($result);
    }

    private function getProps($obj, bool $null = false)
    {
        $ref = new \ReflectionClass($obj);

        return \array_reduce(
            $ref->getProperties(),
            function ($acc, $val) use ($ref, $obj, $null) {
                $name = $val->getName();
                $prop = $ref->getProperty($name);
                $prop->setAccessible(true);

                $acc[$name] = $null ? (is_object($prop->getValue($obj)) ? null : $prop->getValue($obj)) : $prop->getValue($obj);

                return $acc;
            },
            []
        );
    }

    private function getPropsDeep($obj)
    {
        return \array_reduce(
            $this->getProps($obj),
            function ($acc, $val) {
                $acc[] = is_array($val) ?
                    $this->mapDeep(
                        function ($x) {
                            return \is_object($x) ? $this->getProps($x) : $x;
                        },
                        $val
                    ) :
                    (\is_object($val) ? $this->getPropsDeep($val) : $val);

                return $acc;
            },
            []
        );
    }

    private function fold($f, $l, $acc)
    {
        foreach ($l as $key => $val) {
            $acc = $f($acc, $val, $key);
        }

        return $acc;
    }

    private function mapDeep($f, $l)
    {
        return $this->fold(
            function ($acc, $val, $idx) use ($f) {
                $acc[$idx] = \is_array($val) ? $this->mapDeep($f, $val) : $f($val);

                return $acc;
            },
            $l,
            []
        );
    }
}
