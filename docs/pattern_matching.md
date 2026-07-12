# Pattern Matching

Pattern matching in Phunkie provides a functional way to destructure and match on complex data types, working alongside PHP's native pattern matching features.

## Basic Pattern Matching

Phunkie's pattern matching combines with PHP's `match` expression:

```php
$on = pmatch($value);
$result = match(true) {
    $on(42) => "Found 42",
    $on(_) => "Something else"
};
```

## Working with PHP 8 Features

### With Match Expression
```php
// PHP native match
$status = match($value) {
    200 => "OK",
    404 => "Not Found",
    default => "Unknown"
};

// Phunkie pattern matching
$on = pmatch(Some($response));
$status = match(true) {
    $on(Just($code)) => match($code) {
        200 => "OK",
        404 => "Not Found",
        default => "Unknown"
    },
    $on(None()) => "No Response"
};
```

### With Enums
```php
enum Status {
    case Success;
    case Error;
}

$on = pmatch(Some(Status::Success));
$result = match(true) {
    $on(Just(Status::Success)) => "All good!",
    $on(Just(Status::Error)) => "Something went wrong",
    $on(None()) => "No status"
};
```

## Common Patterns

### Option Matching
```php
use function Phunkie\PatternMatching\Referenced\Some as Just;

$on = pmatch(Some(42));
$result = match(true) {
    $on(Just($x)) => "Got $x",
    $on(None()) => "Got nothing"
}; // "Got 42"
```

### List Matching
```php
use function Phunkie\PatternMatching\Referenced\ListWithTail;

$on = pmatch(ImmList(1, 2, 3));
$result = match(true) {
    $on(Nil()) => "Empty list",
    $on(ListWithTail($head, $tail)) => "First: $head",
}; // "First: 1"
```

### Validation Matching
```php
use function Phunkie\PatternMatching\Referenced\Success as Valid;
use function Phunkie\PatternMatching\Referenced\Failure as Invalid;

$on = pmatch(Success("yay!"));
$result = match(true) {
    $on(Valid($x)) => "Success: $x",
    $on(Invalid($e)) => "Failed: $e"
}; // "Success: yay!"
```

### Either Matching
```php
use function Phunkie\PatternMatching\Referenced\Right as RightOf;
use function Phunkie\PatternMatching\Referenced\Left as LeftOf;

$on = pmatch(Right(42));
$result = match(true) {
    $on(RightOf($x)) => "Right: $x",
    $on(LeftOf($e)) => "Left: $e"
}; // "Right: 42"
```

### Pair and Tuple Matching

A pattern matches a tuple holding as many values as the pattern names, so a
pattern of three does not match a tuple of four. A tuple of two is a `Pair`, and
is matched with the `Pair` pattern.

```php
use function Phunkie\PatternMatching\Referenced\Pair as PairOf;
use function Phunkie\PatternMatching\Referenced\Tuple as TupleOf;

$on = pmatch(Pair(1, 2));
$result = match(true) {
    $on(PairOf($x, $y)) => $x + $y
}; // 3

$on = pmatch(Tuple(1, 2, 3));
$result = match(true) {
    $on(TupleOf($x, $y, $z)) => $x + $y + $z
}; // 6
```

### Non Empty List Matching

`Nel` matches a `NonEmptyList` and nothing else. An ordinary list is matched
with `ListWithTail`, even when it happens to hold something.

```php
use function Phunkie\PatternMatching\Referenced\Nel as NelOf;

$on = pmatch(Nel(1, 2, 3));
$result = match(true) {
    $on(NelOf($head, $tail)) => "First: $head, rest: " . $tail->mkString(",")
}; // "First: 1, rest: 2,3"
```

### Function1 Matching
```php
use function Phunkie\PatternMatching\Referenced\Function1 as Function1Of;

$on = pmatch(Function1::identity());
$result = match(true) {
    $on(Function1Of($f)) => $f(42)
}; // 42
```

### Matching Your Own Classes

Any class can be taken apart with `GenericReferenced`, as long as each
constructor argument is named after the property it is stored in. The values are
read whatever their visibility, and whether the class declares them itself or
inherits them.

```php
use Phunkie\PatternMatching\Referenced\GenericReferenced;

final class Person
{
    public function __construct(private string $name, private int $age)
    {
    }
}

$name = $age = null;
$on = pmatch(new Person("Alice", 30));
$result = match(true) {
    $on(new GenericReferenced(Person::class, $name, $age)) => "$name is $age"
}; // "Alice is 30"
```

## Pattern Matching with Guards

Combine patterns with conditions:

```php
$on = pmatch(Some(42));
$result = match(true) {
    $on(Just($x)) && $x > 50 => "Large number",
    $on(Just($x)) => "Number: $x",
    $on(None()) => "No number"
}; // "Number: 42"
```

## Using Wildcards

The underscore (`_`) matches any value:

```php
$on = pmatch(ImmList(1, 2, 3));
$result = match(true) {
    $on(ListWithTail($first, _)) => "First is $first",
    $on(_) => "Something else"
}; // "First is 1"
```

## Best Practices

1. Use PHP's native `match` for simple value matching
2. Use Phunkie's pattern matching for complex data structures
3. Combine with PHP 8 features like enums where appropriate
4. Keep patterns simple and readable
5. Always include a default case

## Implementation Notes

- Built on top of PHP's `match` expression
- Supports type-safe pattern matching
- Works with PHP 8 features
- Provides variable binding through referenced patterns
- Maintains functional programming principles
