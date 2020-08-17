<?php

namespace app\migrations;

use app\base\Migration;

class m_20200816_152500_create_tasks_table extends Migration
{
    public function up()
    {
        $this->db->prepare("
            CREATE TABLE IF NOT EXISTS `tasks` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `parent_id` int(11) NOT NULL DEFAULT 0,
                `user_id` int(11) NOT NULL,
                `date` datetime NOT NULL,
                `title` varchar(255) NOT NULL,
                `body` text NOT NULL,
                `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 - open, 1 - done',
                `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `parent_id` (`parent_id`)
            )
        ")->execute();
    }
}