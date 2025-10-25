# Either

Either in Phunkie represents a value of one of two possible types. An Either type can be `Right(value)` representing success, or `Left(value)` representing failure or error. This is commonly used for error handling and validation where you want to preserve error information.

## Creating Eithers

There are several ways to create Eithers:

```php
// Using explicit constructors
$success = Right(42);        // Right(42)
$failure = Left("error");    // Left("error")

// From computations that might fail
function divide($a, $b) {
    return $b === 0
        ? Left("Division by zero")
        : Right($a / $b);
}

$result = divide(10, 2);  // Right(5)
$error = divide(10, 0);   // Left("Division by zero")
```

## Basic Operations

Eithers provide several basic operations:

```php
$right = Right(42);
$left = Left("error");

// Check which side
$right->isRight();      // true
$right->isLeft();       // false
$left->isRight();       // false
$left->isLeft();        // true

// Get value with fallback
$right->getOrElse(0);   // 42
$left->getOrElse(0);    // 0
```

## Functional Operations

Either implements several type classes that provide powerful functional operations:

### Functor Operations (map)

Map only transforms Right values - Left values are passed through unchanged:

```php
$right = Right(2);
$result = $right->map(fn($x) => $x * 2);  // Right(4)

$left = Left("error");
$result = $left->map(fn($x) => $x * 2);   // Left("error") - unchanged
```

### Monad Operations (flatMap)

FlatMap allows chaining computations that might fail:

```php
$safeDivide = fn($a, $b) => $b === 0 ? Left("div by zero") : Right($a / $b);
$safeSqrt = fn($x) => $x < 0 ? Left("negative sqrt") : Right(sqrt($x));

// Success case
$result = Right(16)
    ->flatMap(fn($x) => $safeDivide($x, 4))  // Right(4)
    ->flatMap($safeSqrt);                     // Right(2.0)

// Failure case - short-circuits on first error
$result = Right(16)
    ->flatMap(fn($x) => $safeDivide($x, 0))  // Left("div by zero")
    ->flatMap($safeSqrt);                     // Left("div by zero") - never executes sqrt
```

### Applicative Operations

```php
$value = Right(5);
$function = Right(fn($x) => $x * 2);
$result = $value->apply($function);  // Right(10)

// With Left values
$error = Left("error");
$result = $error->apply($function);  // Left("error")
```

### Fold Operations

Fold allows you to handle both cases and produce a single result:

```php
$right = Right(42);
$result = ($right->fold(
    fn($error) => "Error: $error"
))(fn($value) => "Success: $value");
// "Success: 42"

$left = Left("not found");
$result = ($left->fold(
    fn($error) => "Error: $error"
))(fn($value) => "Success: $value");
// "Error: not found"
```

## Transformations

### Swap

Swap exchanges Left and Right:

```php
Right(42)->swap();      // Left(42)
Left("error")->swap();  // Right("error")
```

### Bimap

Transform both Left and Right values:

```php
$either = Right(5);
$result = $either->bimap(
    fn($e) => "Error: $e",
    fn($v) => $v * 2
);
// Right(10)

$either = Left("fail");
$result = $either->bimap(
    fn($e) => "Error: $e",
    fn($v) => $v * 2
);
// Left("Error: fail")
```

### LeftMap

Transform only Left values:

```php
Right(42)->leftMap(fn($e) => "Error: $e");   // Right(42) - unchanged
Left("fail")->leftMap(fn($e) => "Error: $e"); // Left("Error: fail")
```

## Filtering

### filterOrElse

Filter Right values based on a predicate:

```php
$isEven = fn($x) => $x % 2 === 0;

Right(42)->filterOrElse($isEven, "not even");  // Right(42)
Right(41)->filterOrElse($isEven, "not even");  // Left("not even")
Left("error")->filterOrElse($isEven, "not even"); // Left("error")
```

## Conversions

### To Option

Convert Either to Option - Right becomes Some, Left becomes None:

```php
Right(42)->toOption();      // Some(42)
Left("error")->toOption();  // None
```

### orElse

Provide an alternative Either:

```php
Right(42)->orElse(Right(0));           // Right(42)
Left("error")->orElse(Right(0));       // Right(0)
Left("error 1")->orElse(Left("error 2")); // Left("error 2")
```

## Query Operations

### contains

Check if Right contains a specific value:

```php
Right(42)->contains(42);      // true
Right(42)->contains(0);       // false
Left("error")->contains(42);  // false
```

### exists

Check if Right value satisfies a predicate:

```php
$isPositive = fn($x) => $x > 0;

Right(42)->exists($isPositive);      // true
Right(-1)->exists($isPositive);      // false
Left("error")->exists($isPositive);  // false
```

### forall

Check if all Right values satisfy a predicate (vacuously true for Left):

```php
$isPositive = fn($x) => $x > 0;

Right(42)->forall($isPositive);      // true
Right(-1)->forall($isPositive);      // false
Left("error")->forall($isPositive);  // true (no value to check)
```

## Pattern Matching

Either works with Phunkie's pattern matching:

```php
$result = match($either) {
    Right($value) => "Success: $value",
    Left($error) => "Error: $error"
};
```

## Real-World Examples

### API Response Handling

```php
function fetchUser($id) {
    $user = database()->find($id);
    return $user ? Right($user) : Left("User not found");
}

function validateAge($user) {
    return $user['age'] >= 18
        ? Right($user)
        : Left("User must be 18 or older");
}

$result = fetchUser(123)
    ->flatMap(fn($user) => validateAge($user))
    ->map(fn($user) => $user['name'])
    ->getOrElse("Anonymous");
```

### Configuration Validation

```php
function validateConfig($config) {
    return isset($config['api_key'])
        ? Right($config)
        : Left("Missing API key");
}

function validateTimeout($config) {
    return $config['timeout'] > 0
        ? Right($config)
        : Left("Timeout must be positive");
}

$result = Right($config)
    ->flatMap(fn($c) => validateConfig($c))
    ->flatMap(fn($c) => validateTimeout($c))
    ->fold(
        fn($error) => throw new Exception($error),
        fn($config) => $config
    );
```

### File Operations

```php
function readFile($path) {
    return file_exists($path)
        ? Right(file_get_contents($path))
        : Left("File not found: $path");
}

function parseJson($content) {
    $data = json_decode($content, true);
    return json_last_error() === JSON_ERROR_NONE
        ? Right($data)
        : Left("Invalid JSON");
}

$result = readFile('config.json')
    ->flatMap(fn($content) => parseJson($content))
    ->map(fn($data) => $data['setting'])
    ->getOrElse('default_value');
```

## Either vs Validation

While both Either and Validation can represent success/failure, they differ in how they handle errors:

- **Either**: Short-circuits on the first error (fail-fast)
- **Validation**: Accumulates all errors (useful for form validation)

```php
// Either - stops at first error
$result = Right(5)
    ->flatMap(fn($x) => Left("error 1"))
    ->flatMap(fn($x) => Left("error 2"));
// Result: Left("error 1") - second error never seen

// Validation - accumulates errors
$v1 = Failure("error 1");
$v2 = Failure("error 2");
$result = $v1->combine($v2);
// Result: Failure(Nel("error 1", "error 2"))
```

Use Either when:
- You want to stop at the first error
- Errors are sequential and dependent
- You're doing error handling in business logic

Use Validation when:
- You want to collect all errors
- You're validating independent fields
- You're doing form validation

## Type Class Laws

Either satisfies the laws for:

### Functor Laws
- **Identity**: `fa.map(id) == fa`
- **Composition**: `fa.map(f).map(g) == fa.map(f ∘ g)`

### Monad Laws
- **Left Identity**: `pure(a).flatMap(f) == f(a)`
- **Right Identity**: `m.flatMap(pure) == m`
- **Associativity**: `m.flatMap(f).flatMap(g) == m.flatMap(x => f(x).flatMap(g))`

### Applicative Laws
- **Identity**: `pure(id).apply(v) == v`
- **Homomorphism**: `pure(f).apply(pure(x)) == pure(f(x))`
- **Interchange**: `u.apply(pure(y)) == pure(fn => fn(y)).apply(u)`

## Best Practices

1. **Use Either for fail-fast error handling** where you want to stop at the first error
2. **Prefer Right-biased operations** - map, flatMap operate on Right values
3. **Use descriptive error messages** in Left values to make debugging easier
4. **Chain operations with flatMap** to handle sequential computations that might fail
5. **Use fold to handle both cases** when you need to produce a final result
6. **Consider Validation instead** when you need to accumulate multiple errors

## Implementation Notes

- Either is right-biased: map, flatMap, and other operations work on Right values
- Left values short-circuit further operations
- Either implements Functor, Applicative, Monad, Foldable, Traversable, and Eq type classes
- flatten recursively unwraps nested Either values
- The implementation is lazy and doesn't execute computations until needed
