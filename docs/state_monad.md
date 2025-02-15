# State Monad

The State monad represents computations that carry state through a sequence of operations. In Phunkie, it provides a way to handle stateful computations in a pure functional manner.

## What is State Monad?

A State monad wraps a function of type `S -> Pair<S,A>`, where:
- `S` is the type of the state
- `A` is the type of the computed value
- `Pair<S,A>` contains both the new state and the computed value

```php
class State {
    public function __construct(callable $run) {
        // run: S -> Pair<S,A>
    }
}
```

## Core Operations

### get
Retrieves the current state:
```php
use function Phunkie\Functions\state\get;

$state = get();
$result = $state->run(42); // Pair(42, 42)
```

### gets
Applies a function to the current state:
```php
use function Phunkie\Functions\state\gets;

$state = gets(fn($x) => $x * 2);
$result = $state->run(21); // Pair(21, 42)
```

### put
Replaces the state:
```php
use function Phunkie\Functions\state\put;

$state = put(42);
$result = $state->run(10); // Pair(42, Unit())
// Initial state 10 is replaced with 42
```

### modify
Modifies the state using a function:
```php
use function Phunkie\Functions\state\modify;

$state = modify(fn($x) => $x + 1);
$result = $state->run(41); // Pair(42, Unit())
```

## State Transformations

### map
Transform the computed value while keeping the state unchanged:
```php
$state = State(42)->map(fn($x) => $x * 2);
$result = $state->run(0); // Pair(0, 84)
```

### flatMap
Chain state computations:
```php
$state = State(21)->flatMap(
    fn($x) => State($x * 2)
);
$result = $state->run(0); // Pair(0, 42)
```

## Common Use Cases

### 1. Counter Implementation
```php
$increment = modify(fn($n) => $n + 1)
    ->flatMap(fn($_) => get());

$result = $increment->run(41); // Pair(42, 42)
```

### 2. Stack Operations
```php
$push = fn($x) => modify(fn($stack) => array_merge([$x], $stack));
$pop = gets(fn($stack) => array_shift($stack));

$operations = $push(1)
    ->flatMap(fn($_) => $push(2))
    ->flatMap(fn($_) => $pop);

$result = $operations->run([]); // Pair([1], 2)
```

### 3. Complex State Management
```php
$updateUser = fn($id, $data) => gets(function($db) use ($id, $data) {
    $db[$id] = array_merge($db[$id] ?? [], $data);
    return $db;
});

$transaction = $updateUser(1, ["name" => "John"])
    ->flatMap(fn($db) => $updateUser(2, ["name" => "Jane"]));
```

## State Transformer (StateT)

StateT allows combining State with other monads:

```php
use Phunkie\Cats\StateT;

$stateT = new StateT($monad);
$result = $stateT->map(fn($x) => $x * 2);
```

## Best Practices

1. Use State for pure stateful computations
2. Compose operations using flatMap
3. Keep state transformations simple and focused
4. Use helper functions (get, gets, put, modify)
5. Consider StateT for complex monad stacks

## Implementation Notes

- State is implemented as a callable that produces a Pair
- All operations preserve referential transparency
- StateT provides monad transformer capabilities
- Helper functions simplify common state operations
- The implementation supports generic state types
