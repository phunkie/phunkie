Learning Phunkie
================

Phunkie is a functional structures library written in PHP. Much has been happening in the world of functional
programming. Languages like Haskell and Scala have become increasingly popular. Functional programming is
experiencing a come back. Most of the what exists out there in the internet (80%) is still written in PHP, which
is not a functional language: most identifiers cannot be declared immutable, constants are limited to a very
few group of types, the syntax for functions is quite verbose, closures do not automatically enclose values — unless
you explicitly tells it to do so, it is not a lazy language, etc... However when you know what the language can offer
you can bend it quite extensively and create the feel of functional programming.

Phunkie is an attempt to create the functional programming feel for PHP developers. PHP developers will be able to
learn the patterns, apply the principles and familiarise themselves with the functional structures and way of
thinking.

Phunkie has been inspired by libraries like Scalaz and Cats from the Scala community. It also offers a library of
functions to capture a bit of the Haskell flavour.

This manual will list all the features contained in the library and offer some illustrations of how they can
be used.

Table of contents
-----------------

[Kinds and parametricity](kinds.md)
- Kinds (Proper kinds, First order kinds, Higher order kinds)
- Parametricity
- Phunkie console (Show,  Phunkie commands)

[Phunkie Types](phunkie_types.md)
- Options
- Lists
- Tuples
- Pairs
- Unit
- Maps
- Sets
- Function1

[Composability of functions](composability_of_functions.md)
- Pure functions
- Higher other functions
- Currying
- Composing functions

[Pattern matching](pattern_matching.md)
- Working with lists
- Working with options

[Composability of everything](composability_of_everything.md)
- Semigroups
  - combine
- Monoids
  - zero

[Functors](functors.md)

[Applicatives](applicatives.md)

[Monads](monads.md)

[Traverse and sequence](traverse_and_sequence.md)

[State Monad](state_monad.md)

[Lenses](lenses.md)

[IO Monad](io_monad.md)

[Reader Monad](reader_monad.md)

[Monad Transformers](monad_transformers.md)

[Validations](validations.md)

- Disjunction of Success and Failure
- NonEmptyList
- Either constructor
- Attempt constructor
- Cascading and concatenating validations
- Pattern matching