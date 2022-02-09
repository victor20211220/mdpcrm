<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_email_body_table extends CI_Migration
{
    public function up()
    {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS ip_email_messages_body(
                id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                email_message_id INT UNSIGNED NOT NULL,
                is_html TINYINT NOT NULL,
                content TEXT NOT NULL,
                PRIMARY KEY (id)
            ) ENGINE = innodb CHARSET 'UTF8'
        ");

        $this->db->query("ALTER TABLE ip_email_messages DROP COLUMN `content`");
    }

    public function down()
    {
        $this->db->query("DROP TABLE ip_email_messages_body");
        $this->db->query("ALTeR TABLE ip_email_messages ADD COLUMN `content` TEXT AFTER `subject`");
    }
}

