Learning Phunkie
================

Phunkie is a functional structures library written in PHP. Much has been happening in the world of functional
programming. Languages like Haskell and Scala have become increasingly popular. Functional programming is
experiencing a come back. Most of the what exists out there in the internet (80%) is still written in PHP, which
is not a functional language: identifiers are mainly variables, immutable identifiers (constants) are limited to a very
small group of types, the syntax for functions is quite verbose, closures do not automatically enclose values — unless
you explicitly tells it to do so, it is not a lazy language etc... However, when you know what the language can offer,
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

[Installation](installation.md)
 - [Requirements](installation.md#requirements)
 - [Installation via Composer](installation.md#installation-via-composer)
 - [Phunkie console](installation.md#phunkie-console)
 - [Using the console](installation.md#using-the-console)

[An introduction to functional programming](introduction_to_functional_programming.md)
- [What is functional programming?](introduction_to_functional_programming.md#what-is-functional-programming)
- [The power of immutability](introduction_to_functional_programming.md#the-power-of-immutability)
- [The importance of algebraic types, laws and data types](introduction_to_functional_programming.md#the-importance-of-algebraic-types-laws-and-data-types)
- [Functional programming in PHP](introduction_to_functional_programming.md#functional-programming-in-php)
- [The approach of Phunkie](introduction_to_functional_programming.md#the-approach-of-phunkie)

[Kinds and parametricity](kinds.md)
- [Kinds](kinds.md#kinds)
- [Kind implementation in Phunkie](kinds.md#kind-implementation-in-phunkie)
- [Type Classes and Kinds](kinds.md#type-classes-and-kinds)
- [Higher-Order Kinds](kinds.md#higher-order-kinds)
- [Common Kinds in Phunkie](kinds.md#common-kinds-in-phunkie)
- [Benefits of Kinds](kinds.md#benefits-of-kinds)
- [Showing types and kinds in the console](kinds.md#showing-types-and-kinds-in-the-console)
- [Parametricity](kinds.md#parametricity)

[Phunkie Types](phunkie_types.md)
- [Options](types/options.md)
- [Lists](types/lists.md)
- [Tuples and Pairs](types/tuples_and_pairs.md)
- [Unit](types/unit.md)
- [Maps](types/maps.md)
- [Sets](types/sets.md)
- [Function1](types/function1.md)

[Composability of functions](composability_of_functions.md)
- [Pure functions](composability_of_functions.md#pure-functions)
- [Higher-order functions](composability_of_functions.md#higher-order-functions)
- [Currying](composability_of_functions.md#currying)
- [Composing functions](composability_of_functions.md#composing-functions)

[Functors](functors.md)
- [What is a Functor?](functors.md#what-is-a-functor)
- [Core Operations](functors.md#core-operations)
  - [map](functors.md#map)
  - [lift](functors.md#lift)
  - [as](functors.md#as)
  - [void](functors.md#void)
  - [zipWith](functors.md#zipwith)
- [Functor Laws](functors.md#functor-laws)
- [Functor Composition](functors.md#functor-composition)
- [Common Functors in Phunkie](functors.md#common-functors-in-phunkie)
- [Best Practices](functors.md#best-practices)
- [Implementation Notes](functors.md#implementation-notes)

[Applicatives](applicatives.md)
- [Understanding Applicatives](applicatives.md#understanding-applicatives)
- [Applicative Laws](applicatives.md#applicative-laws)
- [Using Applicatives](applicatives.md#using-applicatives)
- [Common Applicatives](applicatives.md#common-applicatives)

[Monads](monads.md)
- [What is a Monad?](monads.md#what-is-a-monad)
- [The Identity Monad](monads.md#the-identity-monad)
- [Monad Laws](monads.md#monad-laws)
- [Common Monads](monads.md#common-monads)
- [Working with Monads](monads.md#working-with-monads)

[Semigroups and Monoids](semigroups_and_monoids.md)
- [Semigroups](semigroups_and_monoids.md#semigroups)
- [Monoids](semigroups_and_monoids.md#monoids)
- [Laws and Properties](semigroups_and_monoids.md#laws-and-properties)
- [Common Semigroups and Monoids](semigroups_and_monoids.md#common-semigroups-and-monoids)

[Composability of everything](composability_of_everything.md)
- [Type Class Composition](composability_of_everything.md#type-class-composition)
- [Combining Different Types](composability_of_everything.md#combining-different-types)
- [Composition Patterns](composability_of_everything.md#composition-patterns)

[Traverse and sequence](traverse_and_sequence.md)
- [Understanding Traverse](traverse_and_sequence.md#understanding-traverse)
- [Understanding Sequence](traverse_and_sequence.md#understanding-sequence)
- [Common Use Cases](traverse_and_sequence.md#common-use-cases)

[State Monad](state_monad.md)
- [What is State Monad?](state_monad.md#what-is-state-monad)
- [Using State](state_monad.md#using-state)
- [State Transformations](state_monad.md#state-transformations)

[Lenses](lenses.md)
- [Understanding Lenses](lenses.md#understanding-lenses)
- [Lens Laws](lenses.md#lens-laws)
- [Common Lens Operations](lenses.md#common-lens-operations)

[IO Monad](io_monad.md)
- [What is IO Monad?](io_monad.md#what-is-io-monad)
- [Core Operations](io_monad.md#core-operations)
- [Writing IO Operations](io_monad.md#writing-io-operations)
- [Best Practices](io_monad.md#best-practices)
- [Implementation Notes](io_monad.md#implementation-notes)

[Reader Monad](reader_monad.md)
- [What is Reader Monad?](reader_monad.md#what-is-reader-monad)
- [Creating Readers](reader_monad.md#creating-readers)
- [Core Operations](reader_monad.md#core-operations)
- [Kleisli Composition](reader_monad.md#kleisli-composition)
- [Common Use Cases](reader_monad.md#common-use-cases)
- [Best Practices](reader_monad.md#best-practices)
- [Implementation Notes](reader_monad.md#implementation-notes)

[Monad Transformers](monad_transformers.md)
- [What are Monad Transformers?](monad_transformers.md#what-are-monad-transformers)
- [Available Transformers](monad_transformers.md#available-transformers)
- [Core Operations](monad_transformers.md#core-operations)
- [Common Use Cases](monad_transformers.md#common-use-cases)
- [Best Practices](monad_transformers.md#best-practices)
- [Implementation Notes](monad_transformers.md#implementation-notes)

[Validations](validations.md)
- [What is Validation?](validations.md#what-is-validation)
- [Creating Validations](validations.md#creating-validations)
- [Core Operations](validations.md#core-operations)
- [Common Use Cases](validations.md#common-use-cases)
- [Best Practices](validations.md#best-practices)
- [Implementation Notes](validations.md#implementation-notes)
- [Validation vs Either](validations.md#validation-vs-either)

[Pattern matching](pattern_matching.md)
- [Basic Pattern Matching](pattern_matching.md#basic-pattern-matching)
- [Working with PHP 8 Features](pattern_matching.md#working-with-php-8-features)
  - [With Match Expression](pattern_matching.md#with-match-expression)
  - [With Enums](pattern_matching.md#with-enums)
- [Common Patterns](pattern_matching.md#common-patterns)
- [Pattern Matching with Guards](pattern_matching.md#pattern-matching-with-guards)
- [Using Wildcards](pattern_matching.md#using-wildcards)
- [Best Practices](pattern_matching.md#best-practices)
- [Implementation Notes](pattern_matching.md#implementation-notes)

[Free Monads](free_monads.md)
- [What is a Free Monad?](free_monads.md#what-is-a-free-monad)
- [Core Operations](free_monads.md#core-operations)
  - [pure](free_monads.md#pure)
  - [liftM](free_monads.md#liftm)
  - [flatMap](free_monads.md#flatmap)
  - [foldMap](free_monads.md#foldmap)
- [Natural Transformations](free_monads.md#natural-transformations)
- [Common Use Cases](free_monads.md#common-use-cases)
- [Implementation Notes](free_monads.md#implementation-notes)