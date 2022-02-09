<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_calendar_color extends CI_Migration
{
    public function up()
    {
        $this->db->query("ALTER TABLE ip_calendar ADD color VARCHAR(8) NOT NULL AFTER fullday");
    }

    public function down()
    {
    }
}

