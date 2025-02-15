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
