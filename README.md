# CakePHP3 + Behat and PHPSpec

This is CakePHP3 Blog Tutorial Application Example For Testing by Behat and PHPSpec

## Create CakePHP3 Blog Tutorial Application

I use Cakebox for creating application.

1: Install Vagrant and Virtualbox.
2: Install Cakebox

```bash
localhost:any     $ git clone https://github.com/alt3/cakebox.git
localhost:any     $ cd cakebox
localhost:cakebox $ cp Cakebox.yaml.default Cakebox.yaml
localhost:cakebox $ vagrant up
```

3: Generate CakePHP3 application skelton by cakebox

```bash
localhost:cakebox $ vagrant ssh
Welcome to Ubuntu 14.04.1 LTS (GNU/Linux 3.13.0-24-generic x86_64)

vagrant@cakebox:~$ cakebox application add blog-tutorial.app
```

4: Add host name to your host computer

```bash
localhost:cakebox $ sudo vi /etc/hosts
```

Add `10.33.10.10 blog-tutorial.app`.

5: Read tutorial docs

[Blog tutorial](http://book.cakephp.org/3.0/en/tutorials-and-examples/blog/blog.html)

Any configuration was finished by Cakebox.

## Install Behat/Mink and any components by composer

For testing, use following components:

- Mink Extension (dependent Mink and Behat)
- Mink goutte driver (Headless web driver)
- PHPUnit (for using CakePHP fixture manager)
- PHP dotenv (for switching environment)
- Cake Fabricate (Testdata generator)

```bash
vagrant@cakebox:~$ cd Apps/blog-tutorial.app/
vagrant@cakebox:~/Apps/blog-tutorial.app$ composer require --dev behat/mink-extension
vagrant@cakebox:~/Apps/blog-tutorial.app$ composer require --dev behat/mink-goutte-driver
vagrant@cakebox:~/Apps/blog-tutorial.app$ composer require --dev phpunit/phpunit
vagrant@cakebox:~/Apps/blog-tutorial.app$ composer require --dev sizuhiko/cake_fabricate
vagrant@cakebox:~/Apps/blog-tutorial.app$ composer require vlucas/phpdotenv
```

## Cakebox configuration

For running behat, recommend to change some configurations.

1: Up box memory to 2048M.

```yaml
# Cakebox.yml
vm:
  hostname: cakebox
  ip: 10.33.10.10
  memory: 2048
  cpus: 1
```

2: Set xdebug.max_nesting_level

```bash
localhost:cakebox $ sudo vi /etc/php5/mods-available/xdebug.ini
```

```ini
# /etc/php5/mods-available/xdebug.ini
xdebug.max_nesting_level=500
```

## How to made this example

### Generate behat skelton

```bash
vagrant@cakebox:~$ cd Apps/blog-tutorial.app/
vagrant@cakebox:~/Apps/blog-tutorial.app$ vendor/bin/behat --init
```

### Add behat.yml

Create `behat.yml` on `Apps/blog-tutorial.app` directory.
And activate `Mink Extension`.
See [Mink Extension Official documentation](https://github.com/Behat/MinkExtension/blob/master/doc/index.rst).

```yaml
# behat.yml
default:
  # ...
  extensions:
    Behat\MinkExtension:
      base_url: 'http://blog-tutorial.app.test/'
      sessions:
        default:
          goutte: ~
  suites:
    my_suite:
      contexts:
        - FeatureContext
        - Behat\MinkExtension\Context\MinkContext
```

### Add posts.feature

Add `posts.feature` onto `features` directory.
See example code.

### For switching environment

For testing by behat, you should switch application environment.
Because use test database when accessed by behat.
On this example application, uses CAKE_ENV environment.
Add `config/bootstrap.php` for switching database env.

```php
if (getenv('CAKE_ENV') === 'test') {
    ConnectionManager::alias('test', 'default');
}
```

I provided configuration of nginx for testing.
If you use Cakebox, then copy the config file like followings:

```bash
sudo cp blog-tutorial.app.test /etc/nginx/sites-available/
sudo ln -s /etc/nginx/sites-available/blog-tutorial.app.test /etc/nginx/sites-enabled/
sudo service nginx restart
```

And add `/etc/hosts` on Cakebox vm to hostname for testing.

```bash
localhost:cakebox $ sudo vi /etc/hosts

# Add host
10.33.10.10 blog-tutorial.app.test
```

### Integrate CakePHP3 and Behat

`features/bootstrap/FeatureContext.php` is bootstrap of Behat test.

```php
class FeatureContext implements Context, SnippetAcceptingContext
{
    public function __construct()
    {
        require_once dirname(dirname(__DIR__)) . '/tests/bootstrap.php'; // (1)

        // Always connect test database
        ConnectionManager::alias('test', 'default'); // (2)

        Fabricate::config(function($config) { // (3)
            $config->adaptor = new CakeFabricateAdaptor([
                CakeFabricateAdaptor::OPTION_FILTER_KEY => true,
                CakeFabricateAdaptor::OPTION_VALIDATE   => false
            ]);
        });

        $this->fixtureInjector = new FixtureInjector(new FixtureManager()); //(4)
        $this->fixture = new BddAllFixture();
    }
}
```

This bootstrap flow (especially 1 and 4)  inspired from `phpunit.xml.dist` of CakePHP3.
(1) is from `phpunit` tag. Load `bootstrap.php` for testing CakePHP application.
(4) is from `listeners` tag. For using fixture system into behat step.

```xml
<!-- phpunit.xml.dist -->
<phpunit
	colors="true"
	processIsolation="false"
	stopOnFailure="false"
	syntaxCheck="false"
	bootstrap="./tests/bootstrap.php"  // (1)
	>
	<php>
		<ini name="memory_limit" value="-1"/>
		<ini name="apc.enable_cli" value="1"/>
	</php>

	<!-- Add any additional test suites you want to run here -->
	<testsuites>
		<testsuite name="App Test Suite">
			<directory>./tests/TestCase</directory>
		</testsuite>
		<!-- Add plugin test suites here. -->
	</testsuites>

	<!-- Setup a listener for fixtures (4) -->
	<listeners>
		<listener
		class="\Cake\TestSuite\Fixture\FixtureInjector"
		file="./vendor/cakephp/cakephp/src/TestSuite/Fixture/FixtureInjector.php">
			<arguments>
				<object class="\Cake\TestSuite\Fixture\FixtureManager" />
			</arguments>
		</listener>
	</listeners>
</phpunit>
```

(2) is connection of (default) database for testing.
(3) is configuration for integration fabricate to CakePHP.


#### Using fixture of CakePHP on context of Behat

In the example case, Fixtures define on FeatureContext as previously described.

```php
$this->fixtureInjector = new FixtureInjector(new FixtureManager()); //(4)
$this->fixture = new BddAllFixture();
```

Behat provides some hook points.
Fixtures are loaded and unloaded with this.

```php
    /** @BeforeScenario */
     public function beforeScenario(BeforeScenarioScope $scope)
     {
        $this->fixtureInjector->startTest($this->fixture);
     }

     /** @AfterScenario */
     public function afterScenario(AfterScenarioScope $scope)
     {
        $this->fixtureInjector->endTest($this->fixture, time());
     }
```

`@BeforeScenario` hook is run before a specific scenario will run.
`@AfterScenario` hook is run after Behat finishes executing a scenario. 

Inside CakePHP, `FixtureInjector` takes a role as PHPUnit_Framework_TestListener.

```php
class FixtureInjector implements PHPUnit_Framework_TestListener
{

    /**
     * Adds fixtures to a test case when it starts.
     *
     * @param \PHPUnit_Framework_Test $test The test case
     * @return void
     */
    public function startTest(PHPUnit_Framework_Test $test)
    {
        $test->fixtureManager = $this->_fixtureManager;
        if ($test instanceof TestCase) {
            $this->_fixtureManager->fixturize($test);
            $this->_fixtureManager->load($test);
        }
    }

    /**
     * Unloads fixtures from the test case.
     *
     * @param \PHPUnit_Framework_Test $test The test case
     * @param float $time current time
     * @return void
     */
    public function endTest(PHPUnit_Framework_Test $test, $time)
    {
        if ($test instanceof TestCase) {
            $this->_fixtureManager->unload($test);
        }
    }

}
```

Without PHPUnit, it should call these hook functions.
FeatureContext hooks simulate the listener functions.

At last, FixtureInjector startTest and endTest functions are required of arguments as PHPUnit_Framework_Test.
So, it should create class extends TestCase.
In the example, I create BddAllFixture class into `FeatureContext.php`.
It only has `$fixtures` array for FixtureInjector.

```php
class BddAllFixture extends TestCase {
    public $fixtures = [
        'Categories' => 'app.categories',
        'Articles'   => 'app.articles',
        'Users'      => 'app.users',
        'Categories' => 'app.categories'
    ];
}
```

#### Using any CakePHP3 feature in contexts of Behat

You can use any CakePHP3 feature in contexts of Behat.
In the example, it calls CakePHP Router::url() at `WebContext.php`.

```php
use Behat\MinkExtension\Context\MinkContext;
use Cake\Routing\Router;

class WebContext extends MinkContext
{
    public function locatePath($path)
    {
        return parent::locatePath($this->getPathTo($path));
    }

    private function getPathTo($path)
    {
        switch ($path) {
            case 'TopPage': return Router::url(['controller' => 'articles', 'action' => 'index']);
            case 'トップページ': return Router::url(['controller' => 'articles', 'action' => 'index']);
            default: return $path;
        }
    }
}
```

CakePHP3 feature can use in any context of Behat.
Because [CakePHP3 is fully adopt PSR-2](http://bakery.cakephp.org/2014/12/16/CakePHP-3-to-fully-adopt-PSR-2.html), it is awesome and take it easy.

## Installing and Runnning the example

TODO...

## TODO

- PHPSpec

