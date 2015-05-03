<?php

use Phinx\Migration\AbstractMigration;

class AddUserIdToArticles extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     *
     * Uncomment this method if you would like to use it.
     *
    public function change()
    {
    }
    */
    
    /**
     * Migrate Up.
     */
    public function up()
    {
        $articles = $this->table('articles');
        $articles->addColumn('user_id', 'integer', ['after' => 'category_id'])
            ->save();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $articles = $this->table('articles');
        $articles->removeColumn('user_id')
            ->save();
    }
}