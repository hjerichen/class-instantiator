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
    # The class instantiator will match the property name to the constructor parameter name
    private $id = 5;
    
    # The class instantiator will match the return type to the constructor parameter type
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
    # The class instantiator will match the property name to the constructor parameter name
    private $id = 5;
    
    # The class instantiator will match the return type to the constructor parameter type 
    public function getDatabase(): PDO
    {
        return new PDO('dsn');
    }
}

$instantiator = new ClassInstantiator();
$object = $instantiator->instantiateClass(ClassB::class);
```

Instantiate classes with an extension has a higher priority then using an annotation.  
This allows overwriting the annotation und instantiate classes depending on your current needs.  

##### Storing Objects

It is possible to sore objects permanently with an annotation.  
Those objects will then be returned when a matching class is requested instead of creating a new one every time.  

```php
<?php

use HJerichen\ClassInstantiator\Annotation\Instantiator;

/**
 * @Instantiator(class=MyInstantiator::class, store=true)
 */
class ClassA {
    public function __construct(PDO $database, int $id) {}
}
```

Storing objects with annotation does not work when the instantiation is done in the extension.  
This is because of the higher priority for extensions.  
But you can store objects as well when using the method "injectObject" inside the creation method of the extension.

##### Inject Objects

It is possible to inject objects into the class instantiator.  
Those objects will be returned when a matching class is requested.

```php
<?php

use HJerichen\ClassInstantiator\ClassInstantiator;

interface InterfaceA {}
class ClassA implements InterfaceA {}

$instantiator = new ClassInstantiator();

# only using the object resolves to this object when exactly this class is requested
$instantiator->injectObject(new ClassA());
$object = $instantiator->instantiateClass(ClassA::class);

# using the object and an interface as second parameter
# resolves to this object when exactly this interface is requested
$instantiator->injectObject(new ClassA(), InterfaceA::class);
$object = $instantiator->instantiateClass(InterfaceA::class);
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

# you can also inject an extension.
$container = new ClassInstantiatorContainer(new ClassInstantiator()); 
$factory = new SchemaFactory($cache, $container);
```

##### License and authors
This project is free and under the MIT Licence. Responsible for this project is Heiko Jerichen (heiko@jerichen.de).