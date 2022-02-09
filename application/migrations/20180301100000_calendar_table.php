<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_calendar_table extends CI_Migration
{
    public function up()
    {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS ip_calendar (
                id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                user_id INT UNSIGNED NOT NULL,
                title VARCHAR(64) NOT NULL,
                description VARCHAR(128),
                fullday TINYINT UNSIGNED NOT NULL,
                date_start DATETIME NOT NULL,
                date_end DATETIME NOT NULL,
                PRIMARY KEY (id)
            ) ENGINE = innodb CHARSET 'UTF8'
        ");
    }

    public function down()
    {
        $this->db->query("DROP TABLE ip_calendar");
    }
}

