# Kinds and parametricity

Kinds are type constructors that describe the "shape" of types in functional programming. They help us understand how types can be combined and manipulated.

## Kinds

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

## Showing types and kinds in the console

To start the console, simply run:

```bash
$ bin/phunkie-console
Welcome to phunkie console.

Type in expressions to have them evaluated.

phunkie >
```

Then you can use the `:type` command to show the type of an expression:

```bash
phunkie > :type Some(42)
Option<Int>
```

And the `:kind` command to show the kind of a type:

```bash
phunkie > :kind Some(42)
* -> *
```

## Benefits of Kinds

1. **Type Safety**: Kinds help ensure type-safe operations across different data structures

2. **Abstraction**: Allow writing generic code that works with any type of the appropriate kind

3. **Composition**: Enable composition of types and type classes in a predictable way

4. **Documentation**: Provide clear information about how types can be combined

Understanding kinds is essential for working with Phunkie's type system and utilizing its functional programming features effectively.

## Parametricity

Parametricity is a fundamental principle in functional programming that describes how polymorphic functions must behave uniformly across all types. In Phunkie, this means that the behavior of generic functions must be consistent regardless of the specific types they operate on.

### Examples in Phunkie

```php
// ImmList example - map behavior is consistent across types
$intList = ImmList(1, 2, 3);
$stringList = ImmList("a", "b", "c");
$intList->map(fn($x) => $x 2); // Behavior is consistent
$stringList->map(fn($x) => $x . $x); // Same structure, different type
// Option example - flatMap works the same way for any type
$intOption = Option(42);
$stringOption = Option("hello");
$intOption->flatMap(fn($x) => Option($x + 1)); // Some(43)
$stringOption->flatMap(fn($x) => Option($x . "!")); // Some("hello!")
```

### Key Properties

1. **Type Abstraction**
   - Functions must work without knowing the specific types
   - Cannot inspect or depend on the concrete type's structure

2. **Uniform Behavior**
   - Operations like `map`, `flatMap`, `filter` work consistently
   - The structure of the container is preserved regardless of type

3. **Free Theorems**
   - Properties that hold true for all possible implementations
   - Example: `$list->map(f)->map(g)` equals `$list->map(fn($x) => g(f($x)))`

### Benefits in Phunkie

1. **Type Safety**
   ```php
   // This works for any type A
   function double<A>(ImmList<A> $list, callable $f): ImmList<A> {
       return $list->map($f);
   }
   ```

2. **Code Reuse**
   ```php
   // Works with any Functor (Option, ImmList, etc.)
   function increment($functor) {
       return $functor->map(fn($x) => $x + 1);
   }
   ```

3. **Refactoring Safety**
   - Changes to implementation details won't affect the interface
   - Client code remains stable due to parametric guarantees

### Limitations in PHP

While Phunkie implements parametricity principles, PHP's type system has some limitations:

1. No true generics support
2. Type erasure at runtime
3. Limited compile-time type checking

Despite these limitations, following parametricity principles in Phunkie helps write:
- More maintainable code
- More reusable components
- More predictable behavior
- More testable functions

### Best Practices

1. Write type-agnostic functions when possible:
   ```php
   // Good - works with any type
   function length/*<A>*/(ImmList/*<A>*/ $list): int {
       return $list->length;
   }

   // Avoid - tied to specific type
   function sumInts(ImmList/*<int>*/ $list): int {
       return $list->reduce(fn($a, $b) => $a + $b);
   }
   ```

2. Use type class constraints instead of concrete types:
   ```php
   // Works with any Monad
   function chain/*<M>*/($monad, callable $f) {
       return $monad->flatMap($f);
   }
   ```

3. Respect type class laws and contracts:
   ```php
   // Functor law: preserve identity
   $list->map(fn($x) => $x) === $list
   ```

Understanding and applying parametricity in Phunkie leads to more robust and maintainable functional code, even within PHP's type system limitations.

