<?php

namespace app\base;

use app\base\db\drivers\DbDriverInterface;

class Model
{
    /**
     * @var DbDriverInterface $db
     */
    public $db;

    public function __construct()
    {
        $this->db = Application::$db;
    }
}