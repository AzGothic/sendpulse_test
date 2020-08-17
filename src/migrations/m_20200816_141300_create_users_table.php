<?php

namespace app\migrations;

use app\base\Migration;

class m_20200816_141300_create_users_table extends Migration
{
    public function up()
    {
        $this->db->prepare("
            CREATE TABLE IF NOT EXISTS `users` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `name` varchar(255) DEFAULT '',
                `email` varchar(255) NOT NULL,
                `passhash` varchar(32) NOT NULL,
                `token` varchar(64) DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `email` (`email`),
                KEY `token` (`token`)
            )
        ")->execute();
    }
}