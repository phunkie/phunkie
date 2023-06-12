<?php

namespace spec\Phunkie\Cats;

use Phunkie\Cats\Lens;
use Phunkie\Laws\LensLaws;
use Phunkie\Types\Option;
use Phunkie\Utils\Copiable;
use Md\Unit\TestCase;
use function Phunkie\Functions\lens\contains;
use function Phunkie\Functions\lens\fst;
use function Phunkie\Functions\lens\member;
use function Phunkie\Functions\lens\self;
use function Phunkie\Functions\lens\snd;
use function Phunkie\Functions\lens\trivial;
use function Phunkie\Functions\lens\makeLenses;
use function Phunkie\Functions\semigroup\combine;

class LensSpec extends TestCase
{
    use LensLaws;

    /**
     * @test
     */
    public function it_obeys_the_law_of_identity()
    {
        $name = new Name("Jack Bauer");
        $user = new User(new Name("Chuck Norris"));
        $userNameLens = $this->userNameLens();
        $this->assertTrue($this->identityLaw($userNameLens, $user, $name));
    }

    /**
     * @test
     */
    public function it_obeys_the_law_of_retention()
    {
        $name = new Name("Jack Bauer");
        $anotherName = new Name("Nina Myers");
        $user = new User(new Name("Chuck Norris"));
        $userNameLens = $this->userNameLens();
        $this->assertTrue($this->retentionLaw($userNameLens, $user, $name, $anotherName));
    }

    /**
     * @test
     */
    public function it_obeys_the_law_of_double_set()
    {
        $user = new User(new Name("Chuck Norris"));
        $userNameLens = $this->userNameLens();
        $this->assertTrue($this->doubleSetLaw($userNameLens, $user));
    }

    /**
     * @test
     */
    public function it_implements_trivial()
    {
        $this->assertTrue(trivial()->get(42) == Unit());
        $this->assertEquals(34, trivial()->set(42, 34));
    }

    /**
     * @test
     */
    public function it_implements_self()
    {
        $this->assertEquals(42, self()->get(42));
        $this->assertEquals(42, self()->set(42, 34));
    }

    /**
     * @test
     */
    public function it_implements_fst()
    {
        $this->assertEquals(1, fst()->get(Pair(1, 2)));
        $this->assertTrue(Pair(3, 2) == fst()->set(3, Pair(1, 2)));
    }

    /**
     * @test
     */
    public function it_implements_snd()
    {
        $this->assertEquals(2, snd()->get(Pair(1, 2)));
        $this->assertTrue(Pair(1, 3) == snd()->set(3, Pair(1, 2)));
    }

    public function it_implements_contain()
    {
        $s = ImmSet(1, 2, 3);
        $this->assertTrue(contains(2)->get($s));
        $this->assertTrue(
            contains(4)->set($s, true) == ImmSet(1, 2, 3, 4)
        );
        $this->assertTrue(
            contains(3)->set($s, false) == ImmSet(1, 2)
        );
    }

    /**
     * @test
     */
    public function it_implements_member()
    {
        $m = ImmMap(["a" => 1, "b" => 2]);
        $this->assertTrue(member("b")->get($m) == Some(2));
        $this->assertTrue(
            member("b")->set($m, None())->eqv(ImmMap(["a" => 1]))
        );
        $this->assertTrue(
            member("b")->set($m, Some(3))->eqv(ImmMap(["a" => 1, "b" => 3]))
        );
        $this->assertTrue(
            member("b")->set($m, Some(4))->eqv(ImmMap(["a" => 1, "b" => 4]))
        );
    }

    /**
     * @test
     */
    public function it_offers_shortcut_for_lenses_getter()
    {
        $user = new User(new Name("Jack Bauer"));

        $lenses = makeLenses("name");

        $this->assertTrue($lenses->name->get($user) == new Name("Jack Bauer"));
    }

    /**
     * @test
     */
    public function it_offers_shortcut_for_lenses_setter()
    {
        $user = new User(new Name("Jack Bauer"));

        $lenses = makeLenses("name");
        $this->assertIsLike(
            $lenses->name->set(new Name("Chuck Norris"), $user),
            new User(new Name("Chuck Norris"))
        );
    }

    /**
     * @test
     */
    public function it_offers_shortcut_for_lenses_mod()
    {
        $user = new User(new Name("Jack Bauer"));

        $lenses = makeLenses("name");
        $this->assertIsLike(
            $lenses->name->mod(fn (Name $name) => new Name(strtoupper($name->getName())), $user),
            new User(new Name("JACK BAUER"))
        );
    }

    /**
     * @test
     */
    public function it_offers_shortcut_for_map_get()
    {
        $user = ImmMap(["name" => "Jack Bauer"]);

        $lenses = makeLenses("name");
        $this->assertIsLike($lenses->name->get($user), Some("Jack Bauer"));
    }

    /**
     * @test
     */
    public function it_offers_shortcut_for_map_set()
    {
        $user = ImmMap(["name" => "Jack Bauer"]);

        $lenses = makeLenses("name");
        $this->assertTrue(
            $lenses
                ->name
                ->set(new Name("Chuck Norris"), $user)
                ->eqv(ImmMap(["name" => new Name("Chuck Norris")]))
        );
    }

    /**
     * @test
     */
    public function it_offers_mod_for_maps()
    {
        $user = ImmMap(["name" => new Name("Jack Bauer")]);

        $lenses = makeLenses("name");
        $userCopy = $lenses->name->mod(fn (Option $name) => new Name(strtoupper($name->get()->getName())), $user);
        $this->assertTrue($userCopy->eqv(ImmMap(["name" => new Name("JACK BAUER")])));
    }

    /**
     * @test
     */
    public function it_offers_get_set_and_mod_for_pairs()
    {
        $user = Pair("Jack", "Bauer");
        $lenses = makeLenses("_1", "_2");

        $this->assertEquals("Jack", $lenses->_1->get($user));
        $this->assertIsLike(
            $lenses->_1->set("Chuck", $user),
            Pair("Chuck", "Bauer")
        );
        $this->assertIsLike(
            $lenses->_2->mod("strtoupper", $user),
            Pair("Jack", "BAUER")
        );
    }

    /**
     * @test
     */
    public function it_lets_you_create_multiple_lenses()
    {
        $lenses = makeLenses("name", "lastName");
        $user = new User(new Name("Jack Bauer"));
        $name = $lenses->name->get($user);
        $lastName = $lenses->lastName->get($name);

        $this->assertIsLike($name, new Name("Jack Bauer"));
        $this->assertEquals("Bauer", $lastName);
    }

    /**
     * @test
     */
    public function it_lets_you_compose_lenses()
    {
        $user = new User(new Name("Jack Bauer"));
        $lenses = makeLenses("name", "lastName");
        $lastName = combine($lenses->name, $lenses->lastName);

        $this->assertEquals("Bauer", $lastName->get($user));
    }

    /**
     * @test
     */
    public function it_lets_you_compose_lenses_for_maps()
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

        $this->assertIsLike($codeLens->get($user), Some("US"));
    }

    private function userNameLens()
    {
        return new class (
            fn (User $user) => $user->getName(),
            fn (Name $name, User $user) => $user->copy(["name" => $name])
        ) extends Lens {};
    }
}

class User implements Copiable
{
    private $name;
    public function __construct(Name $name)
    {
        $this->name = $name;
    }
    public function getName()
    {
        return $this->name;
    }
    public function copy(array $fields)
    {
        return new User($fields["name"]);
    }
}

class Name
{
    private $name;
    public function __construct(string $name)
    {
        $this->name = $name;
    }
    public function getName()
    {
        return $this->name;
    }
    public function getLastName()
    {
        return substr($this->name, strrpos($this->name, " ") + 1);
    }
}
