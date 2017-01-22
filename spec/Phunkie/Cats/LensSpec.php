<?php

namespace spec\Phunkie\Cats;

use Phunkie\Cats\Lens;
use function Phunkie\Functions\lens\contains;
use function Phunkie\Functions\lens\fst;
use function Phunkie\Functions\lens\member;
use function Phunkie\Functions\lens\self;
use function Phunkie\Functions\lens\snd;
use function Phunkie\Functions\lens\trivial;
use function Phunkie\Functions\lens\makeLenses;
use function Phunkie\Functions\semigroup\combine;
use Phunkie\Laws\LensLaws;
use Phunkie\Types\Option;
use Phunkie\Utils\Copiable;
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
        expect(member("b")->set($m, Some(4))->eqv(ImmMap(["a" => 1, "b" => 4])))->toBe(true);
    }

    function it_offers_shortcut_for_lenses_getter()
    {
        $user = new User(new Name("Jack Bauer"));

        $lenses = makeLenses("name");
        expect($lenses->name->get($user))->toBeLike(new Name("Jack Bauer"));
    }

    function it_offers_shortcut_for_lenses_setter()
    {
        $user = new User(new Name("Jack Bauer"));

        $lenses = makeLenses("name");
        expect($lenses->name->set(new Name("Chuck Norris"), $user))->toBeLike(new User(new Name("Chuck Norris")));
    }

    function it_offers_shortcut_for_lenses_mod()
    {
        $user = new User(new Name("Jack Bauer"));

        $lenses = makeLenses("name");
        expect($lenses->name->mod(function(Name $name) { return new Name(strtoupper($name->getName())); }, $user))->toBeLike(new User(new Name("JACK BAUER")));
    }

    function it_offers_shortcut_for_map_get()
    {
        $user = ImmMap(["name" => "Jack Bauer"]);

        $lenses = makeLenses("name");
        expect($lenses->name->get($user))->toBeLike(Some("Jack Bauer"));
    }

    function it_offers_shortcut_for_map_set()
    {
        $user = ImmMap(["name" => "Jack Bauer"]);

        $lenses = makeLenses("name");
        expect($lenses->name->set(new Name("Chuck Norris"), $user)->eqv(ImmMap(["name" => new Name("Chuck Norris")])))->toBe(true);
    }

    function it_offers_mod_for_maps()
    {
        $user = ImmMap(["name" => new Name("Jack Bauer")]);

        $lenses = makeLenses("name");
        $userCopy = $lenses->name->mod(function(Option $name) { return new Name(strtoupper($name->get()->getName())); }, $user);
        expect($userCopy->eqv(ImmMap(["name" => new Name("JACK BAUER")])))->toBe(true);
    }

    function it_offers_get_set_and_mod_for_pairs()
    {
        $user = Pair("Jack", "Bauer");
        $lenses = makeLenses("_1", "_2");

        expect($lenses->_1->get($user))->toBe("Jack");
        expect($lenses->_1->set("Chuck", $user))->toBeLike(Pair("Chuck", "Bauer"));
        expect($lenses->_2->mod("strtoupper", $user))->toBeLike(Pair("Jack", "BAUER"));
    }
    
    function it_lets_you_create_multiple_lenses()
    {
        $lenses = makeLenses("name", "lastName");
        $user = new User(new Name("Jack Bauer"));
        $name = $lenses->name->get($user);
        $lastName = $lenses->lastName->get($name);

        expect($name)->toBeLike(new Name("Jack Bauer"));
        expect($lastName)->toBeLike("Bauer");
    }

    function it_lets_you_compose_lenses()
    {
        $user = new User(new Name("Jack Bauer"));
        $lenses = makeLenses("name", "lastName");
        $lastName = combine($lenses->name, $lenses->lastName);

        expect($lastName->get($user))->toBe("Bauer");
    }

    function it_lets_you_compose_lenses_for_maps()
    {
        $user = ImmMap([
            "name" => "Jack Bauer",
            "address" => ImmMap([
                "first line" => "Sesame Street",
                "second line" => "San Diego",
                "country" => ImmMap([
                    "code" => "US",
                    "name" => "United States of America"
                ])
            ])
        ]);

        $lenses = makeLenses("address", "country", "code");
        $codeLens = combine($lenses->address, $lenses->country, $lenses->code);
        expect($codeLens->get($user))->toBeLike(Some("US"));
    }

    private function userNameLens()
    {
        return new class(
            function(User $user) { return $user->getName(); },
            function(Name $name, User $user) { return $user->copy(["name" => $name]); }
        ) extends Lens {};
    }
}

class User implements Copiable {
    private $name;
    public function __construct(Name $name) { $this->name = $name; }
    public function getName() { return $this->name; }
    public function copy(array $fields) { return new User($fields["name"]); }
}

class Name {
    private $name;
    public function __construct(string $name) { $this->name = $name; }
    public function getName() { return $this->name; }
    public function getLastName() { return substr($this->name, strrpos($this->name, " ") + 1); }
}