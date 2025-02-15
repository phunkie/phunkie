# Traverse and Sequence

Traverse and sequence are powerful operations that allow you to transform and combine effects in a collection. In Phunkie, these operations are primarily implemented for immutable lists.

## Understanding Traverse

Traverse is an operation that maps a function over a structure and then sequences the results. It's particularly useful when working with effects or type constructors.

### Definition

```php
interface Traversable extends Functor, Monad {
    public function traverse(callable $f): Kind;
    public function filter(callable $filter): Traversable;
    public function withFilter(callable $filter): WithFilter;
    public function withEach(callable $block);
}
```

### Basic Usage

```php
$numbers = ImmList(1, 2, 3);

// Convert numbers to Options and sequence them
$result = $numbers->traverse(fn($x) => Some($x * 2));
// Some(ImmList(2, 4, 6))

// If any conversion fails, the entire result is None
$result = $numbers->traverse(function($x) {
    return $x > 1 ? Some($x) : None();
});
// None()
```

## Understanding Sequence

Sequence is an operation that turns a list of effects into an effect of a list. It's used to combine multiple effects into a single effect.

### Basic Usage

```php
// A list of Options
$optionList = ImmList(Some(1), Some(2), Some(3));
$result = $optionList->sequence();
// Some(ImmList(1, 2, 3))

// If any element is None, the entire result is None
$optionList = ImmList(Some(1), None(), Some(3));
$result = $optionList->sequence();
// None()
```

## Common Use Cases

### 1. Validating Multiple Values

```php
$validateAge = fn($age) => 
    $age >= 0 ? Some($age) : None();

$ages = ImmList(25, 30, -1);
$result = $ages->traverse($validateAge);
// None() because one age is invalid
```

### 2. Combining Effects

```php
// Converting a list of IDs to a list of users
$userIds = ImmList(1, 2, 3);
$result = $userIds->traverse(function($id) {
    return findUserById($id); // Returns Option<User>
});
// Some(ImmList(User1, User2, User3)) if all users found
// None() if any user not found
```

### 3. Parallel Operations

```php
$urls = ImmList("url1", "url2", "url3");
$result = $urls->traverse(function($url) {
    return fetchAsync($url); // Returns Future<Response>
});
// Future<ImmList<Response>>
```

## Implementation Details

### Type Safety

The implementation includes type checking to ensure proper usage:

```php
private function guardIsListOfTypeConstructor(): string
{
    $listType = showArrayType($this->toArray());
    $typeConstructor = substr($listType, 0, strpos($listType, "<"));
    if ($typeConstructor == "") {
        throw new \TypeError("Cannot find a type constructor in elements");
    }
    if (!is_callable($typeConstructor)) {
        throw new \TypeError("$typeConstructor is not a callable type constructor");
    }
    return $typeConstructor;
}
```

### Relationship with Other Type Classes

Traverse builds on other functional concepts:
- Extends Functor for mapping operations
- Extends Monad for sequencing operations
- Provides additional filtering capabilities

## Best Practices

1. Use traverse when you need to apply an effect-producing function to a collection
2. Use sequence when you have a collection of effects that need to be combined
3. Handle potential failures appropriately (None results)
4. Consider performance implications with large collections
5. Ensure type consistency across the operation

## Common Pitfalls

1. Not handling None cases
2. Mixing incompatible type constructors
3. Using non-callable type constructors
4. Ignoring type safety checks
5. Not considering the performance impact of sequential operations

## Implementation Notes

- Traverse is implemented primarily for ImmList
- Type checking ensures proper type constructor usage
- Sequence requires consistent type constructors
- The implementation supports various effect types (Option, Future, etc.)
- Performance depends on the size of the collection and the effects used
