# Free Monads

A Free monad is a way to build a monad from any functor. It allows you to construct a program as a sequence of commands, separating the description of the program from its interpretation.

## What is a Free Monad?

A Free monad consists of three constructors:
- `Pure(a)`: Wraps a pure value
- `Suspend(fa)`: Lifts a functor into the Free monad
- `Bind(target, f)`: Represents sequential composition

```php
abstract class Free {
    public static function pure($a)
    {
        return new Free\Pure($a);
    }

    public static function liftM(Kind $fa)
    {
        return new Free\Suspend($fa);
    }

    public function flatMap($f)
    {
        return new Free\Bind($this, $f);
    }
}
```

## Core Operations

### pure
Creates a Free monad containing a pure value:
```php
$program = Free::pure(42);
// Pure(42)
```

### liftM
Lifts a functor into the Free monad:
```php
$program = Free::liftM(Some(42));
// Suspend(Some(42))
```

### flatMap
Chains Free monad operations:
```php
$program = Free::liftM(Some(42))
    ->flatMap(fn($x) => Free::liftM(Some($x + 1)));
// Bind(Suspend(Some(42)), fn($x) => Suspend(Some($x + 1)))
```

### foldMap
Interprets a Free structure using a natural transformation:
```php
$program->foldMap(new NaturalTransformation(optionToList));
// Converts from Option to ImmList context
```

## Natural Transformations

Natural transformations provide a way to interpret Free structures by converting between functors:

```php
$nt = new NaturalTransformation(
    fn(Option $o) => $o->toList()
);
// NaturalTransformation[Option ~> ImmList]
```

## Common Use Cases

1. Building Domain-Specific Languages (DSLs):
```php
interface DatabaseF {}
class Query implements DatabaseF {
    public function __construct(public string $sql) {}
}
class Insert implements DatabaseF {
    public function __construct(public string $table, public array $data) {}
}

$program = Free::liftM(new Query("SELECT * FROM users"))
    ->flatMap(fn($users) => Free::liftM(new Insert("logs", ["users" => count($users)])));
```

2. Separating Business Logic from Effects:
```php
$program = for_(
    __($user) ->_(getUserInfo()),
    __($perms) ->_(getPermissions($user)),
    __($log)   ->_(logAccess($user, $perms))
)->yields($user);

// Interpret with different effects (database, HTTP, mock, etc.)
$program->foldMap($interpreter);
```

## Implementation Notes

- Free monads are built on top of pattern matching
- Natural transformations preserve functor laws
- The implementation supports type-safe operations
- Pattern matching enables recursive interpretation
- Free structures can be composed and nested
