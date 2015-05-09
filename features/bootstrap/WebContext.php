<?php

use Behat\MinkExtension\Context\MinkContext;

class WebContext extends MinkContext
{
    public function locatePath($path)
    {
        return parent::locatePath($this->getPathTo($path));
    }

    private function getPathTo($path)
    {
        switch ($path) {
            case 'TopPage': return '/articles/';
            case 'トップページ': return '/articles/';
            default: return $path;
        }
    }
}
