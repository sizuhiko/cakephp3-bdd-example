# CakePHP3 + Behat and PHPSpec

This is CakePHP3 Blog Tutorial Application Example For Testing by Behat and PHPSpec

## Installing and Runnning the example

### Install

I use Cakebox for creating application.
Using it, easy to run the example application.

1: Install Vagrant and Virtualbox

- [Vagrant](https://www.vagrantup.com/)
- [Virtualbox](https://www.virtualbox.org/)

2: Install Cakebox

```
localhost:any     $ git clone https://github.com/alt3/cakebox.git
localhost:any     $ cd cakebox
localhost:cakebox $ cp Cakebox.yaml.default Cakebox.yaml
localhost:cakebox $ vagrant up
```

upgrade your box to Ubuntu 16.04 with PHP7.1 by running:

```
localhost:cakebox $ vagrant ssh
vagrant@cakebox $ sudo apt-get update
vagrant@cakebox $ sudo apt-get install software-properties-common python-software-properties
vagrant@cakebox $ /cakebox/bash/ubuntu-16.sh
vagrant@cakebox $ exit
localhost:cakebox $ vagrant reload
```

Check PHP version:

```
localhost:cakebox $ vagrant ssh
vagrant@cakebox:~$ php -v
PHP 7.1.12-2+ubuntu16.04.1+deb.sury.org+2 (cli) (built: Dec  7 2017 20:12:04) ( NTS )
```

3: Install example application

```
localhost:cakebox $ vagrant ssh
Welcome to Ubuntu 14.04.1 LTS (GNU/Linux 3.13.0-24-generic x86_64)

vagrant@cakebox $ cakebox application add blog-tutorial.app --source https://github.com/sizuhiko/cakephp3-bdd-example.git --webroot /home/vagrant/Apps/blog-tutorial.app/webroot
```

It will print out logs of installation followings:

```
Creating application http://blog-tutorial.app

Configuring installer
Creating installation directory
Git installing user specified application sources
Creating virtual host
* Successfully created PHP-FPM virtual host
Creating databases
* Successfully created main database
* Successfully created test database
Configuring permissions
Updating configuration files
Application created using:
  database => blog-tutorial_app
  framework_human => user specified
  framework_short => custom
  installation_method => git
  path => /home/vagrant/Apps/blog-tutorial.app
  source => https://github.com/sizuhiko/cakephp3-bdd-example.git
  url => blog-tutorial.app
  webroot => /home/vagrant/Apps/blog-tutorial.app/webroot
Please note:
  => Configuration files are not automatically updated for user specified applications.
  => Make sure to manually update your database credentials, plugins, etc.

Remember to update your hosts file with: 10.33.10.10 http://blog-tutorial.app

Installation completed successfully
```

After installation completed successfully, create directories and install dependencies.

```
vagrant@cakebox $ cd Apps/blog-tutorial.app
vagrant@cakebox:~/Apps/blog-tutorial.app$ mkdir tmp 
vagrant@cakebox:~/Apps/blog-tutorial.app$ mkdir logs
vagrant@cakebox:~/Apps/blog-tutorial.app$ cp config/app.default.php config/app.php
vagrant@cakebox:~/Apps/blog-tutorial.app$ composer install 

Generating autoload files
Set Folder Permissions ? (Default to Y) [Y,n]? 
Updated Security.salt value in config/app.php
```

### Configuration

#### Example Application Config

Edit section of database connection in `config/app.php`.

```php
    'Datasources' => [
        'default' => [
            'className' => 'Cake\Database\Connection',
            'driver' => 'Cake\Database\Driver\Mysql',
            'persistent' => false,
            'host' => 'localhost',
            'username' => 'cakebox',    // CHANGE
            'password' => 'secret',
            'database' => 'blog-tutorial_app', // CHANGE
            'encoding' => 'utf8',
            'timezone' => 'UTC',
            'cacheMetadata' => true,
            'quoteIdentifiers' => false,
        ],
        'test' => [
            'className' => 'Cake\Database\Connection',
            'driver' => 'Cake\Database\Driver\Mysql',
            'persistent' => false,
            'host' => 'localhost',
            'username' => 'cakebox',    // CHANGE
            'password' => 'secret',
            'database' => 'test_blog-tutorial_app',    // CHANGE
            'encoding' => 'utf8',
            'timezone' => 'UTC',
            'cacheMetadata' => true,
            'quoteIdentifiers' => false,
        ],
```

#### Host Computer Config

Add host name to your host computer
For example followings:

```
localhost:cakebox $ sudo vi /etc/hosts
```

Add `10.33.10.10 blog-tutorial.app`.

#### Cakebox Config

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

```
localhost:cakebox $ sudo vi /etc/php/7.1/fpm/conf.d/20-xdebug.ini  
```

```ini
# /etc/php/7.1/fpm/conf.d/20-xdebug.ini
xdebug.max_nesting_level=500
```

#### Webserver Config On Cakebox

I provided configuration of nginx for testing.
If you use Cakebox, then copy the config file like followings:

```
vagrant@cakebox $ cd ~/Apps/blog-tutorial.app
vagrant@cakebox:~/Apps/blog-tutorial.app$ sudo cp blog-tutorial.app.test /etc/nginx/sites-available/
vagrant@cakebox:~/Apps/blog-tutorial.app$ sudo ln -s /etc/nginx/sites-available/blog-tutorial.app.test /etc/nginx/sites-enabled/
vagrant@cakebox:~/Apps/blog-tutorial.app$ sudo service nginx restart
```

And add `/etc/hosts` on Cakebox vm to hostname for testing.

```
localhost:cakebox $ sudo vi /etc/hosts

# Add host
10.33.10.10 blog-tutorial.app.test
```

### Migrate Database

Create all tables for application by migration command.

```
bin/cake migrations migrate
```

### Run Test

After installations and configurations completed successfully, run test using Behat.

```
vagrant@cakebox:~/Apps/blog-tutorial.app$ vendor/bin/behat
```

It will print out test results followings:

```
Feature:
  In order to tell the masses what's on my mind
  As a user
  I want to read articles on the site

  Background:                # features/articles.feature:7
    Given there is a post:   # ArticlesContext::thereIsAPost()
      | Title              | Body                          |
      | The title          | This is the post body.        |
      | A title once again | And the post body follows.    |
      | Title strikes back | This is really exciting! Not. |
    And there is a user:     # UsersContext::thereIsAUser()
      | Username | Password | FirstName | LastName |
      | alice    | ecila    | Alice     | Smith    |
      | bob      | obo      | Bob       | Johnson  |
    And there is a category: # CategoriesContext::thereIsACategory()
      | Name      |
      | Events    |
      | Computers |
      | Foods     |

  Scenario: Show articles                 # features/articles.feature:23
    When I am on "TopPage"                # WebContext::visit()
    Then I should see "The title"         # WebContext::assertPageContainsText()
    And I should see "A title once again" # WebContext::assertPageContainsText()
    And I should see "Title strikes back" # WebContext::assertPageContainsText()

  Scenario: Show the article                       # features/articles.feature:29
    Given I am on "TopPage"                        # WebContext::visit()
    When I follow "A title once again"             # WebContext::clickLink()
    Then I should see "And the post body follows." # WebContext::assertPageContainsText()

  Scenario: Add new article                         # features/articles.feature:34
    Given I am on "TopPage"                         # WebContext::visit()
    And I follow "Add"                              # WebContext::clickLink()
    And I login "bob" "obo"                         # UsersContext::iLogin()
    When I post article form :                      # ArticlesContext::iPostArticleForm()
      | Label      | Value                 |
      | Categories | Events                |
      | Title      | Today is Party        |
      | Body       | From 19:30 with Alice |
    And I should see "Your article has been saved." # WebContext::assertPageContainsText()
    And I should see "Today is party"               # WebContext::assertPageContainsText()

  Scenario: Remove article                     # features/articles.feature:46
    Given I am on "TopPage"                    # WebContext::visit()
    When I delete article "Title strikes back" # ArticlesContext::iDeleteArticle()
    Then I should not see "Title strikes back" # WebContext::assertPageNotContainsText()

4 scenarios (4 passed)
28 steps (28 passed)
0m10.65s (41.29Mb)
```


## How to made this example

This section explains about steps of creation for the example application.

### Create CakePHP3 Blog Tutorial Application

I use Cakebox for creating application.

1. Install Vagrant and Virtualbox.
2. Install Cakebox
3. Generate CakePHP3 application skelton by cakebox

```
localhost:cakebox $ vagrant ssh
Welcome to Ubuntu 14.04.1 LTS (GNU/Linux 3.13.0-24-generic x86_64)

vagrant@cakebox:~$ cakebox application add blog-tutorial.app
```

After generate application completed successfully, add host name to your host computer

```
localhost:cakebox $ sudo vi /etc/hosts
```

Add `10.33.10.10 blog-tutorial.app`.

Continue to read tutorial docs, bake and write codes.

[Blog tutorial](http://book.cakephp.org/3.0/en/tutorials-and-examples/blog/blog.html)

Any configuration was finished by Cakebox.

### Install Behat/Mink and any components by composer

For testing, use following components:

- Mink Extension (dependent Mink and Behat)
- Mink goutte driver (Headless web driver)
- PHPUnit (for using CakePHP fixture manager)
- PHP dotenv (for switching environment)
- Cake Fabricate (Testdata generator)

```
vagrant@cakebox:~$ cd Apps/blog-tutorial.app/
vagrant@cakebox:~/Apps/blog-tutorial.app$ composer require --dev behat/mink-extension
vagrant@cakebox:~/Apps/blog-tutorial.app$ composer require --dev behat/mink-goutte-driver
vagrant@cakebox:~/Apps/blog-tutorial.app$ composer require --dev phpunit/phpunit
vagrant@cakebox:~/Apps/blog-tutorial.app$ composer require --dev sizuhiko/cake_fabricate
vagrant@cakebox:~/Apps/blog-tutorial.app$ composer require vlucas/phpdotenv
```

#### Generate behat skelton

Behat has feature for generation skelton.
Run `behat --init` on application root directory.

```
vagrant@cakebox:~$ cd Apps/blog-tutorial.app/
vagrant@cakebox:~/Apps/blog-tutorial.app$ vendor/bin/behat --init
```

#### Add behat.yml

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

#### Add posts.feature

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


### Using fixture of CakePHP on context of Behat

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

### Using any CakePHP3 feature in contexts of Behat

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

### Write Step to Contexts

The example app has 5 context classes.

- FeatureContext is bootstrap. It constructor integrates to CakePHP.
- WebContext extends MinkContext to testing for web application. Overwrite locatePath for using alias of url.
- ArticlesContext has steps about Articles model and the pages.
- UsersContext has steps about Users model, the pages and authentication.
- CategoriesContext has steps about Category model.

The steps list to `behat.yml`.

```yaml
  suites:
    default:
      contexts:
        - FeatureContext
        - WebContext
        - ArticlesContext
        - UsersContext
        - CategoriesContext
```


## TODO

- PHPSpec integration documentation and example test code.

