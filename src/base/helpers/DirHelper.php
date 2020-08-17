<?php

namespace app\base\helpers;

class DirHelper
{
    const TYPE_UNDEFINED = null;
    const TYPE_DIR       = 'dir';
    const TYPE_FILE      = 'file';

    const FILTER_ALL     = null;
    const FILTER_DIRS    = 'dirs';
    const FILTER_FILES   = 'files';

    public static function ls($dir, $filter = null)
    {
        $list = [];

        if (!file_exists($dir) || !is_dir($dir)) {
            return $list;
        }

        $dr = opendir($dir);
        while (false !== ($name = readdir($dr))) {
            if ($name != '.' && $name != '..') {
                $path = "$dir/$name";
                $type = is_dir($path) ? static::TYPE_DIR : (is_file($path) ? static::TYPE_FILE : static::TYPE_UNDEFINED);
                if ($filter) {
                    if ($filter == static::FILTER_DIRS && $type != static::TYPE_DIR) {
                        continue;
                    }
                    elseif ($filter == static::FILTER_FILES && $type != static::TYPE_FILE) {
                        continue;
                    }
                }
                $list["$type::$name"] = [
                    'name' => $name,
                    'path' => $path . ($type == static::TYPE_DIR ? '/' : ''),
                    'type' => $type,
                    'info' => pathinfo($path),
                ];
            }
        }
        closedir($dr);


        return $list;
    }
}