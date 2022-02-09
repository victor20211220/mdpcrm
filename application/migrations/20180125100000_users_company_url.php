<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_users_company_url extends CI_Migration
{
    public function up()
    {
        $this->load->model('users/Mdl_users');

        $this->db->query("
            ALTER TABLE ip_users
                ADD user_company_url VARCHAR(100) NOT NULL AFTER user_company
        ");

        $query = $this->db->query("SELECT * FROM ip_users");
        foreach ($query->result() as $row) {
            $companyUrl = $this->Mdl_users->generateCompanyUrl($row->user_company);
            $this->db->where(['user_id' => $row->user_id]);
            $this->db->update('ip_users', ['user_company_url' => $companyUrl]);
        }
    }

    public function down()
    {
        $this->db->query("ALTER TABLE ip_users DROP COLUMN user_company_url");
    }
}

