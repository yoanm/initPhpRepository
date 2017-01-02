# initPhpRepository

[![Scrutinizer Build Status](https://img.shields.io/scrutinizer/build/g/yoanm/initPhpRepository.svg?label=Scrutinizer)](https://scrutinizer-ci.com/g/yoanm/initPhpRepository/?branch=master) [![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/yoanm/initPhpRepository.svg?label=Code%20quality)](https://scrutinizer-ci.com/g/yoanm/initPhpRepository/?branch=master) [![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/yoanm/initPhpRepository.svg?label=Coverage)](https://scrutinizer-ci.com/g/yoanm/initPhpRepository/?branch=master)

[![Latest Stable Version](https://img.shields.io/packagist/v/yoanm/init-php-repository.svg)](https://packagist.org/packages/yoanm/init-php-repository)

# Install
```bash
git clone git@github.com:yoanm/initPhpRepository.git
cd initPhpRepository
composer build
```

# How to

## Initiliaze
Go to repository folder an type : 
 * Library
```bash
PATH_TO_BIN/initPhpRepository library
```
 * Project
Go to project repository folder an type :
```bash
PATH_TO_BIN/initPhpRepository [project]
```

## Symfony
In case the **library** is used in symfony invironment, type the following : 
```bash
PATH_TO_BIN/initPhpRepository library --symfony
```

## Run specific templates
```bash
PATH_TO_BIN/initPhpRepository --id ID_1 --id ID_2
```

## List
```bash
PATH_TO_BIN/initPhpRepository -l
```

## Existing file override
### Override all
```bash
PATH_TO_BIN/initPhpRepository -f
```

### Ask before overriding
```bash
PATH_TO_BIN/initPhpRepository --ask-before-override
```

## Help
```bash
PATH_TO_BIN/initPhpRepository -h
```

# Tests
This repository follow a [custom test strategy](https://gist.github.com/yoanm/3944890d0adda5fc7e0c306a1870727d#file-tests-md)

# Contributing
See [contributing note](./CONTRIBUTING.md)
