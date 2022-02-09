<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Companies_url extends CI_Migration
{
    public function up()
    {
        $this->db->query("ALTER TABLE ip_companies ADD company_url VARCHAR(64) NOT NULL");
    }

    public function down()
    {
        $this->db->query("ALTER TABLE ip_companies DROP COLUMN company_url");
    }
}

