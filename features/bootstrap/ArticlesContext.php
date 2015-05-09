<?php

use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Exception\ElementNotFoundException;

use Fabricate\Fabricate;

class ArticlesContext extends RawMinkContext
{
    /**
     * @Given there is a post:
     */
    public function thereIsAPost(TableNode $table)
    {
        $posts = $table->getHash();
        Fabricate::create('Articles', count($posts), function($data, $world) use($posts) {
            $index = $world->sequence('index', 0);
            return ['title'=>$posts[$index]['Title'], 'body'=>$posts[$index]['Body']];
        });
    }

    /**
     * @When I post article form :
     */
    public function iPostArticleForm(TableNode $table)
    {
        $hash = $table->getHash();
        $page = $this->getSession()->getPage();

        foreach ($hash as $field) {
            $element = $page->findField($field['Label']);
            if ('select' == $element->getTagName()) {
                $element->selectOption($field['Value']);
            } else {
                $element->setValue($field['Value']);
            }
        }
        $page->findButton("Save Article")->press();
    }

    /**
     * @When I delete article :title
     */
    public function iDeleteArticle($title)
    {
        $page = $this->getSession()->getPage();
        $table = $page->find('css', 'table#articles');
        foreach ($table->findAll('css', 'tr') as $tr) {
            if (!$tr->has('css', 'td')) {
                continue; // skip title row
            }
            if ($tr->findAll('css', 'td')[1]->getText() == $title) {
                $tr->find('css', 'form')->submit(); // submit delete form
                return;
            }
        }
        throw new ElementNotFoundException($this->getSession(), 'article title', $title, null);
    }
}