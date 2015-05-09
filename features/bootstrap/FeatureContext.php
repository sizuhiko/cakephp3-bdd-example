<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Hook\Scope\AfterScenarioScope;

use Cake\Datasource\ConnectionManager;
use Fabricate\Fabricate;
use CakeFabricate\Adaptor\CakeFabricateAdaptor;

use Cake\TestSuite\TestCase;
use Cake\TestSuite\Fixture\FixtureInjector;
use Cake\TestSuite\Fixture\FixtureManager;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context, SnippetAcceptingContext
{

    /** @var FixtureInjector */
    private $fixtureInjector;

    /** @var BddAllFixture */
    private $fixture;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
        require_once dirname(dirname(__DIR__)) . '/tests/bootstrap.php';

        // Always connect test database
        ConnectionManager::alias('test', 'default');

        Fabricate::config(function($config) {
            $config->adaptor = new CakeFabricateAdaptor([
                CakeFabricateAdaptor::OPTION_FILTER_KEY => true,
                CakeFabricateAdaptor::OPTION_VALIDATE   => false
            ]);
        });

        $this->fixtureInjector = new FixtureInjector(new FixtureManager());
        $this->fixture = new BddAllFixture();
    }

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


}

class BddAllFixture extends TestCase {
    public $fixtures = [
        'Categories' => 'app.categories',
        'Articles'   => 'app.articles',
        'Users'      => 'app.users',
        'Categories' => 'app.categories'
    ];
}
