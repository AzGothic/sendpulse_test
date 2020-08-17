<?php
namespace app\base\db;

use app\base\db\drivers\DbDriverInterface;
use app\base\DbException;

class Connector
{
    /**
     * @param $config
     * @return DbDriverInterface
     * @throws DbException
     */
    public static function getConnect($config): DbDriverInterface
    {
        if (empty($config['driver'])) {
            throw new DbException('DB Driver is required!');
        }

        $connectionClass = 'app\\base\\db\\drivers\\' . $config['driver'];

        return new $connectionClass($config);
    }
}