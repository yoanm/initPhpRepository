# Contributing

## Getting Started
 * Fork, then clone the repo:
```bash
git clone git@github.com:your-username/initPhpRepository.git
````

 * Make sure everything goes well:
```bash
composer ci
```

 * Make your changes (Add/Update tests according to your changes).
 * Make sure tests are still green:
```bash
composer ci
```

 * To check code coverage, launch
```bash
composer coverage
```

 * Push to your fork and [submit a pull request](https://github.com/yoanm/initPhpRepository/compare/).
 * Wait for feedback or merge.

  Some stuff that will increase your pull request's acceptance:
    * Write tests.
    * Follow PSR-2 coding style.
    * Write good commit messages.

## Tests
### Technical tests
**Contributor/code point of view**
Test scope is a public php `class` method

 * **Unit level** (PhpUnit)
*Directory : `tests/Technical/Unit/`*
*Base namespace : `Technical\Unit\Yoanm\InitPhpRepository`*

**Any `object` used in the tested class (aka dependency, could be contructor argument or an instanciation inside the class) must be mocked** using **Prophecy**, if "not possible", the test must be moved under Integration level. 

*Most of tests should be done at Unit level as it's the faster one (execution time point of view), so issues are found earlier.*

 * **Integration level** (PhpUnit)
*Directory : `tests/Technical/Integration/`*
*Base namespace : `Technical\Integration\Yoanm\InitPhpRepository`
*Launched after Unit level tests*

Dependencies *could* be mocked but it's not mandatory.
Put here all tests that test :

 * the behavior of class with an another one
 * a class that use a final class as dependency
 * a method that internally instanciate other class
 * ...
 * a method that have dependencies not mocked for any other reasons

### Functional tests
**End-user point of view**
Test scope is the "public API" of this repository (testing api payloads or website generated html pages for instance).

 * With **Phpunit**
 *Directory : `features/bootstrap/`*
 *Base namespace : `Functional\Yoanm\InitPhpRepository`*
 *Launched after Technical Integration tests*

Test could use a slice of repository source code (to ensure a functionality for instance but without taking in account a "upper level" of code)

 * With **Behat**
 *Behat context directory : `features/bootstrap`*
 *Behat context base namespace : `Functional\Yoanm\InitPhpRepository\BehatContext/`*
 *Behat features directory : `features/`*
 *Launched after Phpunit Functional tests*

Tests will use the complete repository source code and will perform tests to cover production end-user actions

### Example
Let's say we have a class called `ExampleHelper`,
with the following namespace `Yoanm\InitPhpRepository\Helper\ExampleHelper`
and class source file located at `src/Yoanm/InitPhpRepository/Helper/ExampleHelper.php`

A test for ExampleHelper class must have one of the following path and namespace:

 * Technical Unit test
    * namespace `Technical\Unit\Yoanm\InitPhpRepository\Helper`
    * path `tests/Technical/Unit/Helper/ExampleHelperTest.php`
 * Technical Integration test
    * namespace `Technical\Integration\Yoanm\InitPhpRepository\Helper`
    * path `tests/Technical/Integration/Helper/ExampleHelperTest.php`
 * Functional test - Phpunit only
    * namespace `Functional\Yoanm\InitPhpRepository`
    * path `tests/Functional/Helper/ExampleHelperTest.php` or `tests/Functional/*FunctionalityName*Test.php`


A Behat context must have the following namespace and path:
Path `features/bootstrap/MyContext.php`
Namespace `Functional\Yoanm\InitPhpRepository\BehatContext`
