<?php

use Phinx\Migration\AbstractMigration;

class CreateCategories extends AbstractMigration
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
        $categories = $this->table('categories');
        $categories->addColumn('parent_id', 'integer', ['null' => true, 'default' => null])
            ->addColumn('lft', 'integer', ['null' => true, 'default' => null])
            ->addColumn('rght', 'integer', ['null' => true, 'default' => null])
            ->addColumn('name', 'string', ['limit' => 255])
            ->addColumn('description', 'string', ['limit' => 255, 'null' => true, 'default' => null])
            ->addColumn('created', 'datetime')
            ->addColumn('modified', 'datetime', ['null' => true, 'default' => null])
            ->create();

        $articles = $this->table('articles');
        $articles->changeColumn('body', 'text', ['null' => true, 'default' => null])
            ->addColumn('category_id', 'integer', ['null' => true, 'default' => null, 'after' => 'body'])
            ->save();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $articles = $this->table('articles');
        $articles->changeColumn('body', 'text')
            ->removeColumn('category_id')
            ->save();

        $this->dropTable('categories');
    }
}