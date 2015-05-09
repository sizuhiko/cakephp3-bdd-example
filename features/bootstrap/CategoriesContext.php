<?php

use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Gherkin\Node\TableNode;

use Fabricate\Fabricate;

class CategoriesContext extends RawMinkContext
{
    /**
     * @Given there is a category:
     */
    public function thereIsACategory(TableNode $table)
    {
        $categories = $table->getHash();
        Fabricate::create('Categories', count($categories), function($data, $world) use($categories) {
            $index = $world->sequence('index', 0);
            return [
                'parent_id' => null,
                'lft'       => null,
                'rght'      => null,
                'name'      => $categories[$index]['Name'], 
            ];
        });

    }
}