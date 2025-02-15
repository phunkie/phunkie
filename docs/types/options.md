# Options

Options in Phunkie represent optional values. An Option type can either be `Some(value)` containing a value, or `None` representing the absence of a value. This is a safer alternative to using null values in your code.

## Creating Options

There are several ways to create Options:

```php
// Using the Option constructor function
$some = Option(42); // Some(42)
$none = Option(null); // None
// Using explicit constructors
$some = Some(42); // Some(42)
$none = None(); // None
```

## Basic Operations

Options provide several basic operations:

```php
$option = Option(42);

// Check if value exists
$option->isDefined();      // true for Some, false for None
$option->isEmpty();        // false for Some, true for None

// Get value with fallback
$option->getOrElse(0);    // returns the value or the fallback if None
```

## Functional Operations

Options implement several type classes that provide powerful functional operations:

### Functor Operations (map)

```php
$option = Option(2);
$result = $option->map(fn($x) => $x * 2);  // Some(4)

$none = None();
$result = $none->map(fn($x) => $x * 2);    // None
```

### Filtering

```php
$option = Option(42);
$result = $option->filter(fn($x) => $x > 40);  // Some(42)
$result = $option->filter(fn($x) => $x < 40);  // None
```

### Applicative Operations

```php
$value = Option(1);
$function = Option(fn($x) => $x + 1);
$result = $value->apply($function);  // Some(2)
```

### Monad Operations (flatMap)

```php
$option = Option(2);
$result = $option->flatMap(fn($x) => Option($x * 2));  // Some(4)
```

### Foldable Operations

```php
$option = Option(42);
$result = $option->fold(
    0,                     // Initial value if None
    fn($x) => $x * 2      // Function to apply if Some
);
```

## Pattern Matching

Options can be used in pattern matching:

```php
$result = match(true) {
    $option instanceof Some => "Got value: " . $option->get(),
    $option instanceof None => "No value",
};
```

## Working with Collections

Options provide utility functions for working with lists:

```php
use function Phunkie\Functions\option\listToOption;
use function Phunkie\Functions\option\optionToList;

$list = ImmList(1);
$option = listToOption($list);      // Some(1)
$backToList = optionToList($option); // ImmList(1)
```

## Type Safety

Options are type-safe and maintain their type information throughout operations. They implement the `Kind` interface and support higher-kinded types in Phunkie.

## Best Practices

1. Use Options instead of null values to make potential absence of values explicit
2. Leverage the functional operations to chain transformations safely
3. Use `getOrElse()` when you need to extract the value with a fallback
4. Consider using pattern matching for complex branching based on Option values

## Laws

Options satisfy several important laws:

- Functor laws
- Applicative laws
- Monad laws
- Foldable laws

These laws ensure that Options behave consistently and predictably in your code.