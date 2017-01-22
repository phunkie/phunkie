<?php

use Phunkie\Algebra\Monoid;
use function Phunkie\Functions\show\show;
use Phunkie\Cats\Show as Showable;
use function Phunkie\Functions\state\modify;
use Phunkie\Types\ImmList;

class Balance {
    use Showable;
    public $amount;

    public function __construct($amount = 0)
    {
        $this->amount = $amount;
    }

    public function toString() { return "Balance({$this->amount})"; }
}

class Transaction {
    public $accountNo;
    public $amount;

    public function __construct($accountNumber, $amount)
    {
        $this->accountNo = $accountNumber;
        $this->amount = $amount;
    }
}

$balancesMonoid = new class implements Monoid {
    public function zero()
    {
        return new Balance(0);
    }

    public function combine($one, $another)
    {
        return array_merge($one, $another);
    }
};

$balances = [
    'a1' => new Balance(),
    'a2' => new Balance(),
    'a3' => new Balance(),
    'a4' => new Balance(),
    'a5' => new Balance()
];

$transactions = [
    new Transaction('a1', 100),
    new Transaction('a2', 100),
    new Transaction('a1', 500000),
    new Transaction('a3', 100),
    new Transaction('a2', 200)
];

$updateBalance = function (ImmList $txns) use ($balancesMonoid) {
    return modify(function($b) use ($txns, $balancesMonoid) {
        return $txns->foldLeft($b, function($a, $txn) use ($balancesMonoid) {
            return $balancesMonoid->combine($a, [$txn->accountNo => new Balance($txn->amount)]);
        });
    });
};

show($updateBalance(ImmList(...$transactions))->run($balances) );