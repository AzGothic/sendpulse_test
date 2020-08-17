<?php

namespace app\migrations;

use app\base\Migration;

class m_00000000_000000_init_migrations extends Migration
{
    public function up()
    {
        $this->db->prepare("
            CREATE TABLE IF NOT EXISTS `migrations` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `key` varchar(255) NOT NULL,
                PRIMARY KEY (`id`),
                KEY `key` (`key`)
            )
        ")->execute();
    }
}