<?php

namespace app\models;

use app\base\Model;

class Task extends Model
{
    public $table = 'tasks';


    public function create($params)
    {
        if (empty($params['date']) || empty($params['title']) || empty($params['body']) || empty($params['user_id'])) {
            return false;
        }

        $parentTask = null;
        if (!empty($params['parent_id']) && !$parentTask = $this->getTask($params['parent_id'], $params['user_id'])) {
            return false;
        }

        if ($parentTask && $parentTask['parent_id'] != 0) {
            return false;
        }

        if ($parentTask && $parentTask['status'] == 1) {
            return false;
        }

        return $this->db->prepare("
                INSERT INTO `$this->table`
                SET `user_id` = :user_id, `date` = :date, `title` = :title, `body` = :body, `parent_id` = :parent_id
            ")
            ->execute([
                ':user_id'   => $params['user_id'],
                ':date'      => (new \DateTime($params['date']))->format('Y-m-d H:i:s'),
                ':title'     => $params['title'],
                ':body'      => $params['body'],
                ':parent_id' => (!empty($params['parent_id']) ? $params['parent_id'] : 0),
            ]);
    }

    public function update($params)
    {
        if (empty($params['id']) || empty($params['user_id'])) {
            return false;
        }

        if (!$task = $this->getTask($params['id'], $params['user_id'])) {
            return false;
        }

        if ($task['status'] == 1) {
            return false;
        }

        return $this->db->prepare("
                UPDATE `$this->table`
                SET `date` = :date, `title` = :title, `body` = :body
                WHERE `id` = :id
                LIMIT 1
            ")
            ->execute([
                ':id'    => $task['id'],
                ':date'  => (new \DateTime((!empty($params['date']) ? $params['date'] : $task['date'])))->format('Y-m-d H:i:s'),
                ':title' => (!empty($params['title']) ? $params['title'] : $task['title']),
                ':body'  => (!empty($params['body']) ? $params['body'] : $task['body']),
            ]);
    }

    public function getTask($id, $user_id)
    {
        $sth = $this->db->prepare("
            SELECT * FROM `$this->table`
            WHERE `user_id` = :user_id
                AND `id` = :id
            LIMIT 1
        ");
        $sth->execute([':user_id' => $user_id, ':id' => $id]);

        return $sth->fetch();
    }

    public function getTasks($parent_id, $user_id)
    {
        $sth = $this->db->prepare("
            SELECT * FROM `$this->table`
            WHERE  `user_id` = :user_id
                AND `parent_id` = :parent_id
            ORDER BY `date` DESC, `id` DESC
        ");
        $sth->execute([':user_id' => $user_id, ':parent_id' => $parent_id]);

        return $sth->fetchAll();
    }

    public function markAsDone($id, $user_id)
    {
        if (!$task = $this->getTask($id, $user_id)) {
            return false;
        }
        if ($task['status'] == 1) {
            return false;
        }
        $parent = null;
        if ($task['parent_id'] != 0 && $parent = $this->getTask($task['parent_id'], $user_id)) {
            if ($parent['status'] == 1) {
                return false;
            }
        }

        $this->db->prepare("
                    UPDATE `$this->table`
                    SET `status` = :status
                    WHERE `id` = :id
                    LIMIT 1
                ")
            ->execute([':id' => $task['id'], ':status' => 1]);

        if (!$parent) {
            $this->db->prepare("
                    UPDATE `$this->table`
                    SET `status` = :status
                    WHERE `parent_id` = :parent_id
                        AND `status` = 0
                ")
                ->execute([':parent_id' => $task['id'], ':status' => 1]);
        }
        else {
            $parentTasks = $this->getTasks($parent['id'], $user_id);
            $allDone = true;
            foreach ($parentTasks as $parentTask) {
                if ($parentTask['status'] != 1) {
                    $allDone = false;
                    break;
                }
            }

            if ($allDone) {
                $this->db->prepare("
                    UPDATE `$this->table`
                    SET `status` = :status
                    WHERE `id` = :id
                    LIMIT 1
                ")->execute([':id' => $parent['id'], ':status' => 1]);
            }
        }

        return true;
    }

    public function remove($id, $user_id)
    {
        if (!$task = $this->getTask($id, $user_id)) {
            return false;
        }

        if ($task['parent_id'] == 0) {
            $this->db->prepare("
                DELETE FROM `$this->table`
                WHERE `parent_id` = :parent_id
            ")->execute([':parent_id' => $task['id']]);
        }

        $this->db->prepare("
                DELETE FROM `$this->table`
                WHERE `id` = :id
                LIMIT 1
            ")->execute([':id' => $task['id']]);

        return true;
    }

    public function removeUserTasks($user_id)
    {
        return (bool) $this->db->prepare("
                DELETE FROM `$this->table`
                WHERE `user_id` = :user_id
            ")->execute([':user_id' => $user_id]);
    }
}