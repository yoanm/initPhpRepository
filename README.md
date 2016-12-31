# defaultPhpRepository

[![Scrutinizer Build Status](https://img.shields.io/scrutinizer/build/g/yoanm/defaultPhpRepository.svg?label=Scrutinizer)](https://scrutinizer-ci.com/g/yoanm/defaultPhpRepository/?branch=master) [![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/yoanm/defaultPhpRepository.svg?label=Code%20quality)](https://scrutinizer-ci.com/g/yoanm/defaultPhpRepository/?branch=master) [![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/yoanm/defaultPhpRepository.svg?label=Coverage)](https://scrutinizer-ci.com/g/yoanm/defaultPhpRepository/?branch=master)

[![Latest Stable Version](https://img.shields.io/packagist/v/yoanm/default-php-repository.svg)](https://packagist.org/packages/yoanm/default-php-repository)

### Installation
```bash
git clone git@github.com:yoanm/defaultPhpRepository.git
cd defaultPhpRepository
composer build
```

# How to

## Initiliaze

### Library
Go to library repository folder an type : 
```bash
DEFAULT_PHP_REPOSITORY_PATH/bin/defaultPhpRepository init
```

#### Symfony
In case the library is used in symfony invironment, type the following : 
```bash
DEFAULT_PHP_REPOSITORY_PATH/bin/defaultPhpRepository init symfony-library
```

### Project
Go to project repository folder an type :
```bash
DEFAULT_PHP_REPOSITORY_PATH/bin/defaultPhpRepository init project
```

## Run specific templates
First, type the following command to list template ids :
```bash
DEFAULT_PHP_REPOSITORY_PATH/bin/defaultPhpRepository init [project|symfony-library] --list
```
When you have choosen template ids, type the following : 
```bash
DEFAULT_PHP_REPOSITORY_PATH/bin/defaultPhpRepository init [project|symfony-library] --id ID_1 --id ID_2
```

## Contributing
See [contributing note](./CONTRIBUTING.md)
