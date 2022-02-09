<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Quotes_item_table extends CI_Migration
{
    public function up()
    {
        $this->db->query("
            ALTER TABLE ip_quote_items
                MODIFY item_quantity DECIMAL(20,8) NOT NULL
        ");

        $this->db->query("
            ALTER TABLE ip_quote_items
                MODIFY item_price DECIMAL(20,8) NOT NULL
        ");
    }

    public function down()
    {
        $this->db->query("
            ALTER TABLE ip_quote_items
                MODIFY item_quantity INT(10) UNSIGNED NOT NULL
        ");

        $this->db->query("
            ALTER TABLE ip_quote_items
                MODIFY item_price DECIMAL(12, 2) UNSIGNED NOT NULL
        ");
    }
}

