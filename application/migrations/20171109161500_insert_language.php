<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Insert_language extends CI_Migration
{
    public function up()
    {
        $this->db->insert('ip_languages', [
            'language_id'        => 16,
            'language_name'      => 'Lithuanian',
            'language_directory' => 'lithuanian'
        ]);
    }

    public function down()
    {
    }
}

