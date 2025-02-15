# Validation

Validation is a data type that represents computations that might fail. Unlike Either, Validation can accumulate errors using a Semigroup instance.

## What is Validation?

Validation has two variants:
- `Success(a)`: Represents a successful computation with value `a`
- `Failure(e)`: Represents a failed computation with error `e`

```php
abstract class Validation implements Applicative, Monad, Kind, Foldable {
    abstract public function getOrElse($default);
    abstract public function map(callable $f): Kind;
}
```

## Creating Validations

### Basic Creation
```php
use Phunkie\Validation\Success;
use Phunkie\Validation\Failure;

$success = Success(42);
$failure = Failure("Invalid input");
```

### Using Either
```php
$validation = Either("Invalid email")(
    fn() => filter_var($email, FILTER_VALIDATE_EMAIL)
);
```

### Using Attempt
```php
$validation = Attempt(function() {
    if (!file_exists("config.php")) {
        throw new \Exception("Config file not found");
    }
    return require "config.php";
});
```

## Core Operations

### map
Transform successful values:
```php
$success = Success(42)->map(fn($x) => $x * 2); // Success(84)
$failure = Failure("error")->map(fn($x) => $x * 2); // Failure("error")
```

### flatMap
Chain validations:
```php
$validateAge = fn($age) => 
    $age >= 18 ? Success($age) : Failure("Must be 18 or older");

$success = Success(20)->flatMap($validateAge); // Success(20)
$failure = Success(16)->flatMap($validateAge); // Failure("Must be 18 or older")
```

### getOrElse
Provide default values:
```php
$success = Success(42)->getOrElse(0); // 42
$failure = Failure("error")->getOrElse(0); // 0
```

### combine
Accumulate errors or combine successes:
```php
$v1 = Success(2);
$v2 = Success(3);
$v3 = Failure("First error");
$v4 = Failure("Second error");

$v1->combine($v2); // Success(5)
$v3->combine($v4); // Failure("First errorSecond error")
```

## Common Use Cases

### 1. Form Validation
```php
$validateName = fn($name) => 
    strlen($name) >= 2 
        ? Success($name) 
        : Failure("Name too short");

$validateEmail = fn($email) => 
    filter_var($email, FILTER_VALIDATE_EMAIL)
        ? Success($email)
        : Failure("Invalid email");

$validateAge = fn($age) => 
    $age >= 18
        ? Success($age)
        : Failure("Must be 18 or older");

// Combine validations
$form = Success(['name' => 'John', 'email' => 'john@example.com', 'age' => 25]);
$result = $form
    ->map(fn($data) => $validateName($data['name']))
    ->flatMap(fn($_) => $validateEmail($data['email']))
    ->flatMap(fn($_) => $validateAge($data['age']));
```

### 2. Error Accumulation
```php
$validations = ImmList(
    Success(1),
    Failure("Error 1"),
    Failure("Error 2")
);

apply(...$validations); // Failure("Error 1Error 2")
```

### 3. Exception Handling
```php
$result = Attempt(function() {
    $config = require "config.php";
    $db = new PDO($config['dsn']);
    return $db->query("SELECT * FROM users");
});
```

## Best Practices

1. Use Validation when you need to accumulate errors
2. Combine multiple validations using `combine` or `apply`
3. Provide meaningful error messages
4. Use `Attempt` for exception handling
5. Consider using `Nel` for non-empty error lists

## Implementation Notes

- Validation implements Applicative, Monad, and Foldable
- Errors accumulate using Semigroup instances
- Success values can be transformed and combined
- Supports conversion to Option via toOption()
- Provides pattern matching support

## Validation vs Either

Unlike Scala or Cats where Either is a disjunction of Left and Right, Phunkie's Validation and Either functions are specialized for validation scenarios with Success and Failure:

```php
// Scala/Cats style Either (not in Phunkie)
Either[String, Int]  // Left[String] | Right[Int]

// Phunkie's Validation
Validation          // Success[A] | Failure[E]

// Phunkie's Either helper function
Either("error message")(fn() => computation)  // Success[A] | Failure[String]
```

The `Either` function in Phunkie is a helper that creates a Validation, not a general-purpose Either type. It automatically wraps the result in Success or Failure:

```php
// Will become Success(42) if computation returns 42
// Will become Failure("error message") if computation returns null or None
$validation = Either("error message")(
    fn() => computation()
);
```

This specialization makes Phunkie's validation system more focused but less general than Scala/Cats' Either. When you need validation with error accumulation in Phunkie, use Validation directly or the Either helper function.
