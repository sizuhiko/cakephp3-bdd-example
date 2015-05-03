<?php

use Phinx\Migration\AbstractMigration;

class CreateArticles extends AbstractMigration
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
        $table = $this->table('articles');
        $table->addColumn('title', 'string', ['limit' => 50])
              ->addColumn('body', 'text')
              ->addColumn('created', 'datetime')
              ->addColumn('modified', 'datetime')
              ->create();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->dropTable('articles');
    }
}