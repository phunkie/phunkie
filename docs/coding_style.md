# Phunkie Coding Style Guide

This guide outlines the coding conventions and standards for the Phunkie library. Phunkie follows functional programming principles and modern PHP standards to ensure a clean, maintainable, and robust codebase.

## 1. Code Formatting

Phunkie adheres to the **PSR-12** and **PER** (PHP Evolved Recommendations) coding style standards.

*   All code must be formatted using `php-cs-fixer`. A configuration file is provided in the repository.
*   Run the fixer before committing:
    ```bash
    composer run cs-fix
    ```

## 2. Static Analysis

We strive for type safety and correctness using **PHPStan**.

*   The goal is to maintain **0 errors** at the configured level.
*   Run analysis:
    ```bash
    composer run phpstan
    ```

### 2.1. Template Pattern Exception
Phunkie frequently uses "constructor functions" (e.g., `Option($x)`, `ImmList(...$xs)`) that return parameterized types. PHPStan often flags generic types in these functions as "not referenced in a parameter" because the type inference happens via the return type, not the input arguments (or implicitly).

**We explicitly ignore this rule** in our configuration (`phpstan.neon`) to support this functional pattern:
```neon
ignoreErrors:
    - '#Template type \w+ of (function|method) .+ is not referenced in a parameter#'
```
Do not remove this suppression.

## 3. Functional Programming Conventions

### 3.1. Immutability
*   All data structures (e.g., `ImmList`, `ImmMap`, `Option`) must be **immutable**.
*   Modification methods (like `plus`, `minus`, `map`) must return a **new instance**.
*   Use `final` classes where appropriate to prevent extension that could break immutability contracts.

### 3.2. Constructor Functions
*   Prefer "constructor functions" over `new ClassName()`.
*   Example: Use `Option(42)` instead of `new Some(42)` or `new Option(42)`.
*   These functions are usually defined in `src/Phunkie/Functions/*.php`.

### 3.3. Typeclasses and Ops Traits
*   Functionality corresponding to typeclasses (Functor, Monad, etc.) is implemented via traits, typically named `*Ops`.
*   Example: `ImmList` uses `ImmListMonadOps`, `ImmListFunctorOps`, etc.
*   Keep the core class clean; delegate logic to these traits.

### 3.4. Generics and PHPDoc
*   We use PHPDoc `@template` tags extensively to support generics.
*   Always specify `@template`, `@param`, and `@return` types for generic classes and methods.
*   Example:
    ```php
    /**
     * @template A
     * @param A $value
     * @return Option<A>
     */
    function Option($value) { ... }
    ```

### 3.5. Variable Naming in Bindings
*   For tuple destructuring or bindings, use `_1`, `_2`, etc., or descriptive names if applicable.
*   In `for_` comprehensions, use `__($var)` syntax.

## 4. Contributing
*   Ensure all tests pass (`composer test`).
*   Ensure static analysis passes (`composer run phpstan`).
*   Ensure coding style is fixed (`composer run cs-fix`).
*   Add PHPDoc for all new methods and classes.
