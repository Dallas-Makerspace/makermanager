<?php
use Migrations\AbstractMigration;

class AddWhmcsFields extends AbstractMigration
{

    public $autoId = false;

    public function change()
    {
        $table = $this->table('users');
        $table
            ->addColumn('whmcs_real_user_id', 'integer', [
                'default' => null,
                'limit' => 10,
                'null' => true,
                'signed' => false,
            ])
            ->addColumn('whmcs_user_id_password', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->update();
    }
}
