<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_suppliers_clients_create_user extends CI_Migration
{
    public function up()
    {
        $this->db->query("ALTER TABLE ip_suppliers ADD supplier_created_by INT UNSIGNED");
        $this->db->query("
            UPDATE ip_suppliers SET
            ip_suppliers.supplier_created_by = (
                SELECT user_id
                FROM ip_users
                WHERE ip_users.company_id = ip_suppliers.company_id
                LIMIT 1
            )
        ");

        $this->db->query("ALTER TABLE ip_clients ADD client_created_by INT UNSIGNED");
        $this->db->query("
            UPDATE ip_clients SET
            ip_clients.client_created_by = (
                SELECT user_id
                FROM ip_users
                WHERE ip_users.company_id = ip_clients.company_id
                LIMIT 1
            )
        ");
    }

    public function down()
    {
        $this->db->query("ALTER TABLE ip_suppliers DROP COLUMN supplier_created_by");
        $this->db->query("ALTER TABLE ip_clients DROP COLUMN client_created_by");
    }
}

