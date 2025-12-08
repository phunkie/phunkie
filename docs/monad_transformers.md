# Monad Transformers

Monad transformers allow you to combine multiple monads into a single monad. Phunkie provides several monad transformers to help you work with nested monadic structures.

## What are Monad Transformers?

Monad transformers wrap one monad inside another, allowing you to work with both contexts simultaneously. For example, `OptionT` wraps an `Option` inside another monad `F`.

## Available Transformers

### EitherT
Combines `Either` with another monad:

```php
use Phunkie\Cats\EitherT;

// ImmList<Either<String, Int>>
$listOfEithers = ImmList(Right(1), Left("error"), Right(2));
$eitherT = EitherT($listOfEithers);

// Map over the Right values
$result = $eitherT->map(fn($x) => $x + 1);
// EitherT(ImmList(Right(2), Left("error"), Right(3)))

// FlatMap with another EitherT
$result = $eitherT->flatMap(
    fn($x) => EitherT(ImmList(Right($x * 2)))
);
// EitherT(ImmList(Right(2), Left("error"), Right(4)))

// Error handling with IO
$safeDivide = fn($a, $b) => IO(fn() =>
    $b === 0 ? Left("Division by zero") : Right($a / $b)
);

$result = EitherT($safeDivide(10, 2))
    ->map(fn($x) => $x * 2)
    ->getOrElse(0);
// IO that returns 10
```

### OptionT
Combines `Option` with another monad:

```php
use Phunkie\Cats\OptionT;

// ImmList<Option<Int>>
$listOfOptions = ImmList(Some(1), None(), Some(2));
$optionT = OptionT($listOfOptions);

// Map over the inner values
$result = $optionT->map(fn($x) => $x + 1);
// OptionT(ImmList(Some(2), None(), Some(3)))

// FlatMap with another OptionT
$result = $optionT->flatMap(
    fn($x) => OptionT(ImmList(Some($x + 1)))
);
// OptionT(ImmList(Some(2), None(), Some(3)))
```

### StateT
Combines `State` with another monad `F`. The standard function signature is `S => F[(S, A)]`.

```php
use Phunkie\Cats\StateT;

// StateT<Option, Int, Int>
// Function takes state (Int) and returns Option<Pair<State, Value>>
$stateT = new StateT(fn($n) => Some(Pair($n + 1, $n)));

$result = $stateT->run(1); 
// Some(Pair(2, 1))
```

### Kleisli (ReaderT)
Represents functions that return monadic values:

```php
use Phunkie\Cats\Kleisli;
use function Phunkie\Functions\kleisli\kleisli;

$validateLength = kleisli(fn($s) => 
    Option(strlen($s) > 3 ? $s : null)
);

$validateEmail = kleisli(fn($s) => 
    Option(filter_var($s, FILTER_VALIDATE_EMAIL) ? $s : null)
);

// Compose validations
$validateInput = $validateLength->andThen($validateEmail);

$result = $validateInput->run("a@b"); // None
$result = $validateInput->run("user@example.com"); // Some("user@example.com")
```

## Core Operations

### map
Transform values within the nested structure:

```php
$optionT->map(fn($x) => $x * 2);
$stateT->map(fn($x) => $x * 2);
```

### flatMap
Chain operations that return transformed values:

```php
$optionT->flatMap(fn($x) => OptionT(Some($x * 2)));
```

### Additional EitherT Operations

```php
$eitherT->isRight();         // F<Boolean>
$eitherT->isLeft();          // F<Boolean>
$eitherT->getOrElse(42);     // F<A>
$eitherT->fold(              // F<B>
    fn($e) => "Error: $e",
    fn($v) => "Success: $v"
);
$eitherT->swap();            // EitherT<F,R,L>
$eitherT->bimap(             // EitherT<F,A,B>
    fn($e) => "Error: $e",
    fn($v) => $v * 2
);
$eitherT->leftMap(           // EitherT<F,A,R>
    fn($e) => "Error: $e"
);
$eitherT->toOptionT();       // OptionT<F,R>

// Static constructors
EitherT::pure($monad, 42);          // EitherT(Right(42))
EitherT::left($monad, "error");     // EitherT(Left("error"))
EitherT::fromOptionT($optionT, "not found"); // Convert OptionT
```

### Additional OptionT Operations

```php
$optionT->isDefined(); // F<Boolean>
$optionT->isEmpty(); // F<Boolean>
$optionT->getOrElse(42); // F<A>
```

## Common Use Cases

### 1. Error Handling in IO Operations
```php
use Phunkie\Cats\EitherT;

$readConfig = fn($path) => IO(fn() =>
    file_exists($path)
        ? Right(file_get_contents($path))
        : Left("File not found: $path")
);

$parseJson = fn($content) => IO(fn() => {
    $data = json_decode($content, true);
    return json_last_error() === JSON_ERROR_NONE
        ? Right($data)
        : Left("Invalid JSON");
});

$config = EitherT($readConfig('config.json'))
    ->flatMap(fn($content) => EitherT($parseJson($content)))
    ->map(fn($data) => $data['setting'])
    ->getOrElse('default');
// Returns IO that produces either the setting or 'default'
```

### 2. Handling Optional Values in Collections
```php
$users = ImmList(
    Some(['name' => 'Alice']),
    None(),
    Some(['name' => 'Bob'])
);

$names = OptionT($users)
    ->map(fn($user) => $user['name'])
    ->getOrElse('Unknown');
// ImmList('Alice', 'Unknown', 'Bob')
```

### 3. Validation Chains with Either
```php
$validateAge = fn($user) =>
    $user['age'] >= 18
        ? Right($user)
        : Left("Must be 18 or older");

$validateEmail = fn($user) =>
    filter_var($user['email'], FILTER_VALIDATE_EMAIL)
        ? Right($user)
        : Left("Invalid email");

$users = ImmList(
    Right(['name' => 'Alice', 'age' => 25, 'email' => 'alice@example.com']),
    Right(['name' => 'Bob', 'age' => 16, 'email' => 'bob@example.com'])
);

$validated = EitherT($users)
    ->flatMap(fn($user) => EitherT(ImmList($validateAge($user))))
    ->flatMap(fn($user) => EitherT(ImmList($validateEmail($user))))
    ->getValue();
// ImmList(Right(['name' => 'Alice', ...]), Left("Must be 18 or older"))
```

### 4. Stateful Computations with Effects
```php
$computation = new StateT(
    fn($state) => Some(Pair($state + 1, $state))
);
```

### 5. Composing Validations
```php
$validate = kleisli(fn($input) => 
    Option($input)
        ->filter(fn($x) => strlen($x) > 3)
        ->filter(fn($x) => is_numeric($x))
);
```

## Best Practices

1. Use transformers to avoid nested monadic structures
2. Choose the appropriate transformer for your use case
3. Compose transformers for complex operations
4. Handle all possible states (Some/None, Success/Failure)
5. Consider performance implications of transformer stacks

## Implementation Notes

- Transformers maintain type safety through PHP's type system
- Operations are lazy when possible
- Kleisli provides monad transformer capabilities for functions
- The implementation supports generic type parameters
- Transformers can be nested for multiple effects
