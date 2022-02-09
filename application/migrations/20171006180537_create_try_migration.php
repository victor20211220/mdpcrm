<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_try_migration extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_field([
            'try_migration_id'             => [
                'type'           => 'INT',
                'constraint'     => 5,
                'unsigned'       => true,
                'auto_increment' => true
            ],
            'try_migration_title'       => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'try_migration_description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ]);

        $this->dbforge->add_key('try_migration_id', true);
        $this->dbforge->create_table('ip_try_migration');
    }

    public function down()
    {
        $this->dbforge->drop_table('ip_try_migration');
    }
}

