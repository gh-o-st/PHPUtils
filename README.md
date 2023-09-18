
# PHP Utility Library by Joshua Jones

This repository contains several utility classes designed to assist in various tasks related to date and time operations, array manipulations, and password strength evaluation. Each class has its unique set of features to simplify complex operations.

## Chronomat

**Chronomat** is a PHP utility class for date and time operations.

- Purpose: Provides a collection of static methods for various date and time operations like calculating date differences, working with moon phases, and managing date ranges.

## ArrMate

**ArrMate** is an advanced array utility designed for PHP.

- Purpose: Comprehensive utility for complex array operations.
- Features:
  - Deep search within multi-dimensional arrays.
  - Recursive map for nested data manipulation.
  - Distinct array merging for overlapping keys.
  - Efficient data extraction and insertion with 'pluck' and 'inject'.
  - Clear array navigation utilities.
  - Dot notation for array flattening and reconstruction.

## BrutusConfig

**BrutusConfig** defines configuration rules for password strength and validation.

- Purpose: Creation of configuration instances to define password requirements.
- Features:
  - Minimum password length.
  - Character class requirements: lowercase, uppercase, numbers, symbols.
  - Entropy constraints.
  - Brute force considerations.

```php
$brutus = new Brutus();
$config = $brutus->config
             ->setLengthRule(12)
             ->setLowercaseRule(true, 2)
             // ...
```

> **Note:** Direct instantiation is prevented. Use `getInstance()` to create a configuration object.

## Brutus

**Brutus** evaluates password strength against various security criteria.

- Purpose: Evaluate password strength based on set criteria.
- Features:
  - Checks for minimum length, presence of various character types.
  - Entropy evaluation.
  - Resistance to brute force attacks.

```php
$brutus = new Brutus();
$brutus->setPw('YourPassword123!');
$isValid = $brutus->testAll();
```

## Author

- Joshua Jones
- Version: 2.1