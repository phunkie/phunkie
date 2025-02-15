# Immutable Lists

Immutable Lists (ImmList) in Phunkie are ordered collections of elements that cannot be modified after creation. They implement various functional programming patterns and type classes.

## Creating Lists

There are several ways to create immutable lists:

```php
// Empty list
$empty = ImmList(); // or Nil()
// List with elements
$list = ImmList(1, 2, 3);
// Cons constructor (head::tail)
$list = Cons(1, ImmList(2, 3));
```

## Basic Operations

Lists provide several basic operations:

```php
$list = ImmList(1, 2, 3);

// Access elements
$list->head;       // 1
$list->tail;       // ImmList(2, 3)
$list->init;       // ImmList(1, 2)
$list->last;       // 3
$list->length;     // 3

// Check if empty
$list->isEmpty();  // false

// Get nth element
$list->nth(1);     // Some(2)
$list->nth(10);    // None
```

## List Transformations

### Map and Filter Operations

```php
$list = ImmList(1, 2, 3);

// Map
$doubled = $list->map(fn($x) => $x * 2);  // ImmList(2, 4, 6)

// Filter
$evens = $list->filter(fn($x) => $x % 2 == 0);  // ImmList(2)

// Reject (opposite of filter)
$odds = $list->reject(fn($x) => $x % 2 == 0);   // ImmList(1, 3)

// WithFilter (for chaining conditions)
$result = $list
    ->withFilter(fn($x) => $x > 1)
    ->map(fn($x) => $x * 2);
```

### List Manipulation

```php
$list = ImmList(1, 2, 3);

// Take and Drop
$first2 = $list->take(2);           // ImmList(1, 2)
$last2 = $list->drop(1);            // ImmList(2, 3)

// TakeWhile and DropWhile
$lessThan3 = $list->takeWhile(fn($x) => $x < 3);  // ImmList(1, 2)
$from2 = $list->dropWhile(fn($x) => $x < 2);      // ImmList(2, 3)

// Append and Prepend
$appended = $list->append(4);       // ImmList(1, 2, 3, 4)
$prepended = $list->prepend(0);     // ImmList(0, 1, 2, 3)

// Reverse
$reversed = $list->reverse();       // ImmList(3, 2, 1)
```

## Functional Operations

### Foldable Operations

```php
$list = ImmList(1, 2, 3);

// Reduce (fold)
$sum = $list->reduce(fn($x, $y) => $x + $y);  // 6

// FoldLeft
$result = $list->foldLeft(0)(fn($acc, $x) => $acc + $x);

// FoldRight
$result = $list->foldRight(0)(fn($x, $acc) => $x + $acc);
```

### Applicative Operations

```php
use function Phunkie\Functions\applicative\ap;
use function Phunkie\Functions\applicative\pure;

// Apply
$fs = ImmList(fn($x) => $x + 1);
$result = ap($fs)(ImmList(1));  // ImmList(2)

// Pure
$result = pure(ImmList)(42);    // ImmList(42)
```

### Monad Operations

```php
use function Phunkie\Functions\monad\bind;
use function Phunkie\Functions\monad\flatten;

// FlatMap (bind)
$result = $list->flatMap(fn($x) => ImmList($x, $x));

// Flatten
$nested = ImmList(ImmList(1), ImmList(2));
$flat = flatten($nested);  // ImmList(1, 2)
```

## Working with Pairs and Zipping

```php
$list1 = ImmList(1, 2, 3);
$list2 = ImmList("A", "B", "C");

// Zip lists together
$zipped = $list1->zip($list2);  // ImmList(Pair(1,"A"), Pair(2,"B"), Pair(3,"C"))

// Split list at index
$split = $list->splitAt(2);     // Pair(ImmList(1,2), ImmList(3))

// Partition list based on predicate
$partition = $list->partition(fn($x) => $x % 2 == 0);
// Returns Pair(ImmList(2), ImmList(1,3))
```

## String Representation

```php
$list = ImmList(1, 2, 3);

// Default toString
echo $list->toString();  // "List(1, 2, 3)"

// Custom string formatting
$list->mkString(", ");           // "1, 2, 3"
$list->mkString("[", ", ", "]"); // "[1, 2, 3]"
```

## Type Safety

Lists in Phunkie maintain type information and implement various type classes:
- Functor
- Applicative
- Monad
- Foldable
- Traversable

The type system helps ensure type-safe operations across transformations.

## Best Practices

1. Use ImmList when you need an ordered, immutable collection
2. Leverage the functional operations for clean, composable code
3. Use pattern matching with Lists when processing complex structures
4. Take advantage of the type class implementations for generic programming