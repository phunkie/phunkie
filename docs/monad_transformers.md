# Monad Transformers

Monad transformers allow you to combine multiple monads into a single monad. Phunkie provides several monad transformers to help you work with nested monadic structures.

## What are Monad Transformers?

Monad transformers wrap one monad inside another, allowing you to work with both contexts simultaneously. For example, `OptionT` wraps an `Option` inside another monad `F`.

## Available Transformers

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
Combines `State` with another monad:

```php
use Phunkie\Cats\StateT;

// Some(State<Int, Int>)
$stateT = new StateT(Some(fn($n) => Some(Pair($n + 1, $n))));
$result = $stateT->run(1); // Some(Pair(2, 1))
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

### Additional OptionT Operations

```php
$optionT->isDefined(); // F<Boolean>
$optionT->isEmpty(); // F<Boolean>
$optionT->getOrElse(42); // F<A>
```

## Common Use Cases

### 1. Handling Optional Values in Collections
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

### 2. Stateful Computations with Effects
```php
$computation = new StateT(
    Some(fn($state) => Some(Pair($state + 1, $state)))
);
```

### 3. Composing Validations
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
