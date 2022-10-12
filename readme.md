# PHP Attribute-based Injection.

Sounds exciting, doesn't it?! Well this is aimed to be a simple container that
can _help_ you prevent dependency-hell.

## Super-quick Getting Started.

```php
// Map interfaces to concrete classes.
$dependencyMap = [
    MyDependencyInterface::class => MyDependencyPort::class
];

// Pass them to the container.
$container = new Container($dependencyMap);

// Annotate your classes with #[Injected] on properties or constructor params.
class Main 
{
    #[Injected]
    protected MyDependencyInterface $dependency;

    protected MyOtherDependencyInterface $otherDependency;

    public function __construct(MyOtherDependencyInterface $otherDependency)
    {
        $this->otherDependency = $otherDependency;
    }
}

$main = $container->make(Main::class);

// Now the dependency will be fulfilled, and you can call methods that rely
// on them.
$main->whateverMethod();
```

## Getting started.

Say you've got a dependency that you'd like to bring in from some third party,
what you really should do, is write an interface for the methods you need, and
a port that interacts with the dependency. This way the third party dependency 
is isolated to a single port, and doesn't pollute it's way throughout your code.

The way that this project is meant to be used, is first to require it in your
project:

```sh
$ composer require fowlerwill/attribute-injection
```

Then, you can instantiate the Container, and provide it with your interface map
that maps the dependencies to ports.

```php
// Somewhere like index.php or during bootstrap of your application.
$dependencyMap = [
    MyDependencyInterface::class => MyDependencyPort::class
];

$container = new Container($dependencyMap);
```

Where `MyDependencyInterface.php` might look like this:

```php
interface MyDependencyInterface
{
    public function party(): string;
}
```

and `MyDependencyPort.php` might looks like this:

```php
use FowlerWill\AttributeInjection\Injected;
use Thirdparty\Dependency\SomeDependency;

class MyDependencyPort implements MyDependencyInterface
{

    #[Injected]
    protected SomeDependency $dependency;


    public function party(): string
    {
        return "party, " . $this->dependency->partyHard();
    }
}
```

Notice that we've used the PHP Attribute `#[Injected]` there to bring the 
dependency along for the ride, when we tie it all together with our class that
uses the interface...

```php
use FowlerWill\AttributeInjection\Injected;

class Main
{

    #[Injected]
    protected MyDependencyInterface $dependency;

    public function itsTimeToParty(): string
    {
        return "I believe it's time to ".$this->dependency->party();
    }
}
```

and back in our application, we instantiate the class via the container, and
can use the dependencies that have been provided to the properites in the class.

```php
$main = $container->make(Main::class);

echo $main->itsTimeToParty();
```

## Contributing.

First, I'm honoured that you'd want to contribute to the project, so thank you
for even considering. There are a lot of ideas that I have for the project, but
am super open to PR's at this stage to help grow into use cases that you need.

### Getting Started.

```sh
$ composer install
```

### Testing.

To run the tests locally:
```sh
$ composer run test
```

To run the tests on docker, with coverage report:
```sh
$ composer run test-docker
```