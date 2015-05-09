# CakePHP3 + Behat and PHPSpec

This is CakePHP3 Blog Tutorial Application Example For Testing by Behat and PHPSpec

## Create CakePHP3 Blog Tutorial Application

I use Cakebox for creating application.

1. Install Vagrant and Virtualbox.
2. Install Cakebox

```bash
localhost:any     $ git clone https://github.com/alt3/cakebox.git
localhost:any     $ cd cakebox
localhost:cakebox $ cp Cakebox.yaml.default Cakebox.yaml
localhost:cakebox $ vagrant up
```

3. Generate CakePHP3 application skelton by cakebox

```bash
localhost:cakebox $ vagrant ssh
Welcome to Ubuntu 14.04.1 LTS (GNU/Linux 3.13.0-24-generic x86_64)

vagrant@cakebox:~$ cakebox application add blog-tutorial.app
```

4. Add host name to your host computer

```bash
localhost:cakebox $ sudo vi /etc/hosts
```

Add `10.33.10.10 blog-tutorial.app`.

5. Read tutorial docs

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

1. Up box memory to 2048M.

```yaml
# Cakebox.yml
vm:
  hostname: cakebox
  ip: 10.33.10.10
  memory: 2048
  cpus: 1
```

2. Set xdebug.max_nesting_level

```bash
localhost:cakebox $ sudo vi /etc/php5/mods-available/xdebug.ini
```

```ini
# /etc/php5/mods-available/xdebug.ini
xdebug.max_nesting_level=500
```

## Generate behat skelton

```bash
vagrant@cakebox:~$ cd Apps/blog-tutorial.app/
vagrant@cakebox:~/Apps/blog-tutorial.app$ vendor/bin/behat --init
```

## Add behat.yml

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

## Add posts.feature

Add `posts.feature` onto `features` directory.
See example code.

## For switching environment

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

## Integrate CakePHP3 and Behat

`features/bootstrap/FeatureContext.php` is bootstrap of Behat test.

```php
<?php
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
?>
```

TODO...

## TODO

- Integrate CakePHP3 and Behat
- PHPSpec

