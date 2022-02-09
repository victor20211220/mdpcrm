<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_products_search extends CI_Migration
{
    public function up()
    {
        $this->db->query("ALTER TABLE ip_products ADD product_search VARCHAR(64) NOT NULL AFTER product_description");
    }

    public function down()
    {
        $this->db->query("ALTER TABLE ip_products DROP COLUMN product_search");
    }
}

