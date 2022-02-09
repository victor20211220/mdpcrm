<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Invoice_item_quantity_field extends CI_Migration
{
    public function up()
    {
        $this->db->query("
            ALTER TABLE ip_invoice_items
                MODIFY item_quantity DECIMAL(20,8) NOT NULL
        ");
    }

    public function down()
    {
        $this->db->query("
            ALTER TABLE ip_invoice_items
                MODIFY item_quantity INT(10) UNSIGNED NOT NULL
        ");
    }
}

