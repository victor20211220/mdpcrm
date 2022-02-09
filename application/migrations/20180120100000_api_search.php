<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Api_search extends CI_Migration
{
    public function up()
    {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS ip_api_search(
                apis_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                apis_date DATETIME NOT NULL,
                apis_client_id INT UNSIGNED NOT NULL,
                apis_req_title VARCHAR(128),
                apis_req_number VARCHAR(128),
                apis_res_title VARCHAR(128),
                apis_res_number VARCHAR(128),
                apis_response_raw TEXT,
                PRIMARY KEY (apis_id)
            ) ENGINE = innodb CHARSET 'UTF8'
        ");
    }

    public function down()
    {
        $this->db->query("DROP TABLE ip_api_search");
    }
}

