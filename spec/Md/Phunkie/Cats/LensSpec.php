<?php

namespace spec\Md\Phunkie\Cats;

use Md\Phunkie\Cats\Lens;
use function Md\Phunkie\Functions\lens\contains;
use function Md\Phunkie\Functions\lens\fst;
use function Md\Phunkie\Functions\lens\member;
use function Md\Phunkie\Functions\lens\self;
use function Md\Phunkie\Functions\lens\snd;
use function Md\Phunkie\Functions\lens\trivial;
use Md\Phunkie\Laws\LensLaws;
use PhpSpec\ObjectBehavior;

class LensSpec extends ObjectBehavior
{
    use LensLaws;
    function it_obeys_the_law_of_identity()
    {
        $name = new Name("Jack Bauer");
        $user = new User(new Name("Chuck Norris"));
        $userNameLens = $this->userNameLens();
        expect($this->identityLaw($userNameLens, $user, $name))->toBe(true);
    }

    function it_obeys_the_law_of_retention()
    {
        $name = new Name("Jack Bauer");
        $anotherName = new Name("Nina Myers");
        $user = new User(new Name("Chuck Norris"));
        $userNameLens = $this->userNameLens();
        expect($this->retentionLaw($userNameLens, $user, $name, $anotherName))->toBe(true);
    }

    function it_obeys_the_law_of_double_set()
    {
        $user = new User(new Name("Chuck Norris"));
        $userNameLens = $this->userNameLens();
        expect($this->doubleSetLaw($userNameLens, $user));
    }

    function it_implements_trivial()
    {
        expect(trivial()->get(42))->toBeLike(Unit());
        expect(trivial()->set(42, 34))->toBe(34);
    }

    function it_implements_self()
    {
        expect(self()->get(42))->toBe(42);
        expect(self()->set(42, 34))->toBe(42);
    }

    function it_implements_fst()
    {
        expect(fst()->get(Pair(1,2)))->toBe(1);
        expect(fst()->set(3, Pair(1,2)))->toBeLike(Pair(3,2));
    }

    function it_implements_snd()
    {
        expect(snd()->get(Pair(1,2)))->toBe(2);
        expect(snd()->set(3, Pair(1,2)))->toBeLike(Pair(1,3));
    }

    function it_implements_contain()
    {
        $s = ImmSet(1,2,3);
        expect(contains(2)->get($s))->toBe(true);
        expect(contains(4)->set($s, true))->toBeLike(ImmSet(1,2,3,4));
        expect(contains(3)->set($s, false))->toBeLike(ImmSet(1,2));
    }

    function it_implements_member()
    {
        $m = ImmMap(["a" => 1, "b" => 2]);
        expect(member("b")->get($m))->toBeLike(Some(2));
        expect(member("b")->set($m, None())->eqv(ImmMap(["a" => 1])))->toBe(true);
        expect(member("b")->set($m, Some(3))->eqv(ImmMap(["a" => 1, "b" => 3])))->toBe(true);
    }

    private function userNameLens()
    {
        return new class(
            function(User $user) { return $user->getName(); },
            function(Name $name, User $user) { return $user->copy($name); }
        ) extends Lens {};
    }
}

class User {
    private $name;
    public function __construct(Name $name) { $this->name = $name; }
    public function getName() { return $this->name; }
    public function copy(Name $name) { return new User($name); }
}

class Name {
    private $name;
    public function __construct(string $name) { $this->name = $name; }
}