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
Go to repository folder an type : 
 * Library
```bash
bin/defaultPhpRepository library
```
 * Project
Go to project repository folder an type :
```bash
bin/defaultPhpRepository [project]
```

## Symfony
In case the **library** is used in symfony invironment, type the following : 
```bash
bin/defaultPhpRepository library --symfony
```

## Run specific templates
```bash
bin/defaultPhpRepository [project|library] [--symfony] --id ID_1 --id ID_2
```

## List
```bash
bin/defaultPhpRepository [project|symfony-library] [--symfony] -l
```

## Help
```bash
bin/defaultPhpRepository -h
```

## Contributing
See [contributing note](./CONTRIBUTING.md)
