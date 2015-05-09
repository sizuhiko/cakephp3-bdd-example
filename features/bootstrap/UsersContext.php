<?php

use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Gherkin\Node\TableNode;

use Fabricate\Fabricate;

class UsersContext extends RawMinkContext
{
    /**
     * @Given there is a user:
     */
    public function thereIsAUser(TableNode $table)
    {
        $users = $table->getHash();
        Fabricate::create('Users', count($users), function($data, $world) use($users) {
            $index = $world->sequence('index', 0);
            return [
                'username' => $users[$index]['Username'], 
                'password' => $users[$index]['Password'], 
                'firstname' => $users[$index]['FirstName'], 
                'lastname' => $users[$index]['LastName']];
        });
    }

    /**
     * @Given I login :username :password
     */
    public function iLogin($username, $password)
    {
        $page = $this->getSession()->getPage();

        $page->findField('Username')->setValue($username);
        $page->findField('Password')->setValue($password);
        $page->findButton("Login")->press();
    }

}