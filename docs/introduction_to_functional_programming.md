# An Introduction to Functional Programming

## What is Functional Programming?

Functional programming (FP) is a programming paradigm that treats computation as the evaluation of mathematical functions and avoids changing state and mutable data. The key principles include:

- Functions as first-class citizens
- Pure functions (same input always produces same output)
- No side effects
- Declarative rather than imperative style
- Expression-based rather than statement-based

## The Power of Immutability

Immutability is a cornerstone of functional programming. When data is immutable:

- It can't be changed after it's created
- Each transformation creates a new value
- No need to track state changes
- Easier to reason about code
- Thread-safe by default
- Enables referential transparency

Example:
```php
// Imperative approach (mutable)
$numbers = [1, 2, 3];
$numbers[] = 4;  // Modifies original array

// Functional approach (immutable)
$numbers = ImmList(1, 2, 3);
$newNumbers = $numbers->append(4);  // Creates new list, original unchanged
```

## The Importance of Algebraic Types, Laws and Data Types

Algebraic Data Types (ADTs) provide a way to compose complex data types from simpler ones. They come with mathematical laws that guarantee behavior:

### Sum Types (OR)
```php
Option = Some(value) | None
Either = Right(value) | Left(error)
```

### Product Types (AND)
```php
Pair<A,B> = Pair(valueA, valueB)
```

These types follow specific laws:
- Identity
- Associativity
- Composition

By adhering to these laws, we can reason about our code with confidence. In practice, this means that we can use algebraic types to model our data and functions reliably, and we can use laws to test our code.

Here is an example of testing a implementation of Option using the laws of associativity and identity:

```php
$some = Option(10);
$none = Option(null);

// Associativity
assert($some->map(fn($x) => $x * 2)->map(fn($x) => $x + 1) === $some->map(fn($x) => $x * 2 + 1));
assert($none->map(fn($x) => $x * 2)->map(fn($x) => $x + 1) === $none);

// Identity
assert($some->map(fn($x) => $x) === $some);
assert($none->map(fn($x) => $x) === $none);
```

## Functional Programming in PHP

While PHP wasn't designed as a functional language, it supports many functional concepts:

- First-class functions
- Anonymous functions (closures)
- Array functions (map, filter, reduce)
- Type hints and return types

However, PHP lacks some FP features out of the box:
- True immutability
- Pattern matching
- Tail call optimization
- Higher-kinded types

## The Approach of Phunkie

Phunkie brings functional programming concepts to PHP in a practical way:

### Core Features
- Immutable data structures
- Option, Either, and other functional types
- Pattern matching capabilities
- Function composition
- Type safety

### Example Usage
```php
$maybeUser = Option(findUser($id));
$userName = $maybeUser
    ->map(fn($user) => $user->name)
    ->getOrElse("Guest");
```

Phunkie strives to:
- Make functional programming accessible to PHP developers
- Provide type-safe functional abstractions
- Balance purity with practicality
- Integrate smoothly with existing PHP code

By adopting Phunkie's approach, you can gradually introduce functional programming concepts into your PHP applications while maintaining readability and reliability. 