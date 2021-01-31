[![Continuous Integration](https://github.com/hjerichen/class-instantiator/workflows/Continuous%20Integration/badge.svg?branch=master)](https://github.com/hjerichen/class-instantiator/actions)
[![Coverage Status](https://coveralls.io/repos/github/hjerichen/class-instantiator/badge.svg?branch=master&service=github)](https://coveralls.io/github/hjerichen/class-instantiator?branch=master)

# Class Instantiator

The class instantiator tries to create an object of the passed class recursively.

```php
<?php

use HJerichen\ClassInstantiator\ClassInstantiator;

class ClassA {

}

class ClassB {
    public function __construct(ClassA $instanceA) {}
}

$instantiator = new ClassInstantiator();
$object = $instantiator->instantiateClass(ClassB::class);
```

Some arguments for a constructor cannot be created automatically.
For example primitive types like integer or interfaces.  
Or you want to inject a specific object, that should not be created automatically, like a PDO object.  
For those scenarios, there are multiple solutions.

Pass predefined arguments: 

```php
<?php

use HJerichen\ClassInstantiator\ClassInstantiator;

class ClassA {
    public function __construct(PDO $database, int $id) {}
}

class ClassB {
    public function __construct(ClassA $instanceA) {}
}

$predefinedArguments = [
    'database' => new PDO('dsn'), 
    'id' => 5
];

$instantiator = new ClassInstantiator();
$object = $instantiator->instantiateClass(ClassB::class, $predefinedArguments);
```

Extend the class instantiator:

```php
<?php

use HJerichen\ClassInstantiator\ClassInstantiator;

class ClassA {
    public function __construct(PDO $database, int $id) {}
}

class MyInstantiator extends ClassInstantiator {
    /* The class instantiator will match the property name to the constructor parameter name */
    private $id = 5;
    
    /* The class instantiator will match the return type to the constructor parameter type */
    public function getDatabase(): PDO
    {
        return new PDO('dsn');
    }
}

$instantiator = new MyInstantiator();
$object = $instantiator->instantiateClass(ClassA::class);
```

Use an annotation to define a class instantiator extension:

```php
<?php

use HJerichen\ClassInstantiator\Annotation\Instantiator;
use HJerichen\ClassInstantiator\ClassInstantiator;

/**
 * @Instantiator(MyInstantiator::class)
 */
class ClassA {
    public function __construct(PDO $database, int $id) {}
}

class ClassB {
    public function __construct(ClassA $a) {}
}

class MyInstantiator extends ClassInstantiator {
    /* The class instantiator will match the property name to the constructor parameter name */
    private $id = 5;
    
    /* The class instantiator will match the return type to the constructor parameter type */
    public function getDatabase(): PDO
    {
        return new PDO('dsn');
    }
}

$instantiator = new ClassInstantiator();
$object = $instantiator->instantiateClass(ClassB::class);
```

##### PSR-11 Container

There is also a [PSR-11](https://www.php-fig.org/psr/psr-11/) Container Implementation. 
You can use this container in frameworks and libraries who support those.  
Example for [GraphQLite](https://graphqlite.thecodingmachine.io/docs/other-frameworks#requirements):

```php
<?php

use HJerichen\ClassInstantiator\ClassInstantiator;
use HJerichen\ClassInstantiator\ClassInstantiatorContainer;
use TheCodingMachine\GraphQLite\SchemaFactory;

$container = new ClassInstantiatorContainer(new ClassInstantiator()); // You can also inject an extension.
$factory = new SchemaFactory($cache, $container);
```

##### License and authors
This project is free and under the MIT Licence. Responsible for this project is Heiko Jerichen (heiko@jerichen.de).