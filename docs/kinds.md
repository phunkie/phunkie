# Kinds in Phunkie

Kinds are type constructors that describe the "shape" of types in functional programming. They help us understand how types can be combined and manipulated.

## Basic Kinds

### * (Star)
The simplest kind, representing concrete types that have values. Examples:
- `Int`
- `String`
- `Boolean`

### * -> * (Type Constructor)
Takes one type parameter to create a concrete type. Examples:
- `Option<A>`
- `ImmList<A>`
- `Set<A>`

### * -> * -> * (Binary Type Constructor)
Takes two type parameters to create a concrete type. Examples:
- `Either<A, B>`
- `Map<K, V>`
- `Pair<A, B>`

## Kind Implementation in Phunkie

In Phunkie, kinds are represented through the `Kind` interface:

```php
use Phunkie\Types\Kind;
class Option implements Kind {
public const kind = "Option";
// ...
}
class ImmList implements Kind {
public const kind = ImmList;
// ...
}
```

## Type Classes and Kinds

Kinds are fundamental to type classes in Phunkie. Type classes like `Functor`, `Applicative`, and `Monad` operate on types of kind `* -> *`:

```php
// Option is a Functor (kind -> )
$option = Option(42);
$doubled = $option->map(fn($x) => $x 2); // Some(84)
// ImmList is a Functor (kind -> )
$list = ImmList(1, 2, 3);
$doubled = $list->map(fn($x) => $x 2); // ImmList(2, 4, 6)
```

## Higher-Order Kinds

Higher-kinded types are type constructors that take other type constructors as parameters. While PHP doesn't directly support higher-kinded types, Phunkie simulates them through its type class hierarchy:

```php
/ Functor operations on different kinds
$option->map(fn($x) => $x + 1); // Option<A> -> Option<B>
$list->map(fn($x) => $x + 1); // ImmList<A> -> ImmList<B>
```

## Common Kinds in Phunkie

1. `Option<A>`: Kind `* -> *`
   - Represents optional values
   - Takes one type parameter

2. `ImmList<A>`: Kind `* -> *`
   - Represents immutable lists
   - Takes one type parameter

3. `Map<K, V>`: Kind `* -> * -> *`
   - Represents key-value associations
   - Takes two type parameters

4. `Function1<A, B>`: Kind `* -> * -> *`
   - Represents functions from A to B
   - Takes two type parameters

## Benefits of Kinds

1. **Type Safety**: Kinds help ensure type-safe operations across different data structures

2. **Abstraction**: Allow writing generic code that works with any type of the appropriate kind

3. **Composition**: Enable composition of types and type classes in a predictable way

4. **Documentation**: Provide clear information about how types can be combined

Understanding kinds is essential for working with Phunkie's type system and utilizing its functional programming features effectively.