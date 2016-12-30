# defaultPhpRepository

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
