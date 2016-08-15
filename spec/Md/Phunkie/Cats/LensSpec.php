<?php

namespace spec\Md\Phunkie\Cats;

use Md\Phunkie\Cats\Lens;
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