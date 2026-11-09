<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateGuestbook2Table extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        $this->table('guestbook_2')
            ->addColumn('posted_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'null' => false])
            ->addColumn('name', 'string', ['limit' => 32, 'null' => false])
            ->addColumn('email', 'string', ['limit' => 63, 'null' => false])
            ->addColumn('message', 'string', ['limit' => 256, 'null' => false])
            ->addIndex(['posted_at'])
            ->create();
    }
}
