<?php

namespace app\base\db\drivers;

use app\base\DbException;
use PDO;
use PDOException;

class mysql implements DbDriverInterface
{
    /** @var PDO $connect */
    protected $connect;

    /**
     * MySQL constructor.
     * @param $config
     * @throws DbException
     */
    public function __construct($config)
    {
        $this->connect($config);
    }

    /**
     * @return PDO
     */
    public function getConnect(): PDO
    {
        return $this->connect;
    }

    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->getConnect(), $name], $arguments);
    }

    /**
     * @param array $config
     * @return PDO
     * @throws DbException
     */
    protected function connect($config)
    {
        try {
            $dsn = $config['driver']. ':'
                . 'host=' . $config['host'] . ';'
                . 'port=' . $config['port'] . ';'
                . 'dbname=' . $config['dbname'];

            $this->connect = new PDO(
                $dsn,
                $config['user'],
                $config['pass'],
                [
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8;" . (!empty($config['timezone']) ? "SET time_zone = '{$config['timezone']}'" : ''),
                ]
            );
        } catch (PDOException $e) {
            throw new DbException('Could not connect to DB: ' . $e->getMessage());
        }

        return $this->connect;
    }
}