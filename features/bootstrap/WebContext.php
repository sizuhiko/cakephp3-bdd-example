<?php

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
