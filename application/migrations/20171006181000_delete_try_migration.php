<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Delete_try_migration extends CI_Migration
{
    public function up()
    {
        $this->dbforge->drop_table('ip_try_migration');
    }

    public function down()
    {
    }
}

