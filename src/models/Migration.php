<?php

namespace app\models;

use app\base\Application;
use app\base\helpers\CliHelper;
use app\base\helpers\DirHelper;
use app\base\Model;

class Migration extends Model
{
    public $table = 'migrations';

    public function run()
    {
        if (APP_TYPE == 'web') {
            return false;
        }

        CliHelper::e('Migrating...');

        $toMigrate = $this->searchFiles();
        $migrated = $this->getMigrated();

        $newMigrated = 0;
        foreach ($toMigrate as $migrationClassName) {
            if (in_array($migrationClassName, $migrated)) {
                continue;
            }

            $this->up($migrationClassName);
            $newMigrated++;
        }

        CliHelper::e((!$newMigrated ? 'Nothing found.' : 'Finished migrations: ' . $newMigrated));
    }

    protected function searchFiles()
    {
        $files = DirHelper::ls(APP_PATH . 'migrations', DirHelper::TYPE_FILE);
        if (!$files) {
            return false;
        }
        ksort($files);

        $toMigrate = [];
        foreach ($files as $file) {
            $filename = $file['info']['filename'];
            if (!preg_match('~m_[0-9]{8}_[0-9]{6}_.+~', $filename)) {
                continue;
            }
            $toMigrate[] = $filename;
        }

        return array_unique($toMigrate);
    }

    protected function getMigrated()
    {
        $sth = $this->db->prepare("
            SELECT * FROM `information_schema`.`tables`
            WHERE `table_schema` = '" . Application::$config['db']['dbname'] . "'
                AND `table_name` = '$this->table'
            LIMIT 1
        ");
        $sth->execute();
        $tableMigrationExists = (bool) $sth->fetch();

        if (!$tableMigrationExists) {
            return [];
        }

        $sth = $this->db->prepare("SELECT `key` FROM `$this->table`");
        $sth->execute();
        $migrations = $sth->fetchAll();

        if (!$migrations) {
            return [];
        }

        $migrated = [];
        foreach ($migrations as $migration) {
            $migrated[] = $migration['key'];
        }

        return array_unique($migrated);
    }

    protected function up($migrationClassName)
    {
        $migrationClass = 'app\\migrations\\' . $migrationClassName;
        $migration = new $migrationClass();
        $migration->up();

        CliHelper::e($migrationClassName);
        CliHelper::e($migrationClass);

        $this->db
            ->prepare("INSERT INTO `$this->table` SET `key` = :key")
            ->execute([':key' => $migrationClassName]);

        CliHelper::e('Done');
    }
}