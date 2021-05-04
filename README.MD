# Aspect Override

Override methods (Through an aspect oriented approach) and global functions within your PHP tests

## Getting Started

### Installing

```
composer require --dev mcannucci/aspect-override
```

### Bootstraping
Include this within your bootstrap file
```php
<?php

use AspectOverride\Core\Instance;

require __DIR__ . '/../vendor/autoload.php';

// options are:
// setDirectories => The directory in which php classes are elligble for monkey patching/interception
// setTemporaryFilesDirectory => where to save the temporary files, by default it is /tmp/aspect-override
// setShouldUseCache => whether aspect classes should use their previously creating files if there are no changes
AspectOverride\Facades\Instance::initialize(
    \AspectOverride\Core\Configuration::create()
        ->setTemporaryFilesDirectory("/tmp/temporary_directory")
        ->setDirectories([
            __DIR__ . '/../tests/Util/TestClasses'
        ])
        ->setShouldUseCache(false)
);
```

### Usage
```php
use AspectOverride\Override;

// for any instances of 'MyClass', return true for the method 'myMethod'
Override::method(MyClass::class|'\App\MyClass', 'myMethod', function(){
  return true;
});
// for any invocation of the global method 'time' return 10
Override::function('time', function(){
  return 10;
})
```

## Versioning

[SemVer](http://semver.org/) for versioning. For the versions available, see the [tags on this repository](https://github.com/your/project/tags). 

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details

## Acknowledgements

Heavily inspired by [AspectMock](https://github.com/Codeception/AspectMock) and [GO AOP!](https://github.com/goaop/framework)
