<?php

namespace app\models;

use app\base\Application;
use app\base\helpers\SecurityHelper;
use app\base\Model;
use app\base\RequestWeb;

class User extends Model
{
    public $table = 'users';

    /**
     * Current logged user's data
     * @var array $user
     */
    public $user;

    public function __construct()
    {
        parent::__construct();

        if (APP_TYPE == 'web') {
            /** @var RequestWeb $request */
            $request = Application::$request;
            $token = $request->session->get('token');
            if ($token) {
                $this->login(null, null, $token);
            }
        }
    }

    public function create($params)
    {
        if (empty($params['email']) || empty($params['password'])) {
            return false;
        }

        if (!$this->isValidEmail($params['email'])) {
            return false;
        }

        if (!$this->isUnique($params['email'])) {
            return false;
        }

        return $this->db->prepare("
                INSERT INTO `$this->table`
                SET `name` = :name, `email` = :email, `passhash` = :passhash, `token` = :token
            ")
            ->execute([
                ':name'     => (isset($params['name']) ? $params['name'] : ''),
                ':email'    => strtolower($params['email']),
                ':passhash' => SecurityHelper::passToHash($params['password']),
                ':token'    => '',
            ]);
    }

    public function remove($email)
    {
        if (!$user = $this->getUserByEmail($email)) {
            return false;
        }

        /**
         * Remove all user's records
         */
        $taskModel = new Task();
        $taskModel->removeUserTasks($user['id']);

        return (bool) $this->db->prepare("
                DELETE FROM `$this->table`
                WHERE `id` = :id
            ")->execute([':id' => $user['id']]);
    }

    public function login($emailOrParams, $password = null, $token = null)
    {
        $user = null;

        if ($token) {
            if (!$user = $this->getUserByToken($token)) {
                return false;
            }
        }
        else {
            if (is_array($emailOrParams)) {
                if (empty($emailOrParams['email']) || empty($emailOrParams['password'])) {
                    return false;
                }
                $email = $emailOrParams['email'];
                $password = $emailOrParams['password'];
            }
            else {
                $email = $emailOrParams;
            }

            if (!$password) {
                return false;
            }

            if (!$this->isValidEmail($email)) {
                return false;
            }

            if (!$user = $this->getUserByEmail($email)) {
                return false;
            }

            if ($user['passhash'] != SecurityHelper::passToHash($password)) {
                return false;
            }

            $user['token'] = SecurityHelper::randomString(64);

            $this->db
                ->prepare("
                    UPDATE `$this->table`
                    SET `token` = :token
                    WHERE `id` = :id
                    LIMIT 1
                ")
                ->execute([
                    ':id'    => $user['id'],
                    ':token' => $user['token'],
                ]);

            if (APP_TYPE == 'web') {
                /** @var RequestWeb $request */
                $request = Application::$request;
                $request->session->set('token', $user['token']);
            }
        }

        return $this->user = $user;
    }

    public function logout()
    {
        if (!$this->user) {
            return false;
        }

        if (APP_TYPE == 'web') {
            /** @var RequestWeb $request */
            $request = Application::$request;
            $request->session->rm('token');
        }

        return true;
    }

    public function registration($params, $autologin = true)
    {
        if (!$this->create($params)) {
            return false;
        }

        if (!$autologin) {
            return true;
        }

        return $this->login($params['email'], $params['password']);
    }

    public function getUserByEmail($email)
    {
        $email = strtolower($email);

        $sth = $this->db->prepare("
            SELECT * FROM `$this->table`
            WHERE `email` = :email
            LIMIT 1
        ");
        $sth->execute([':email' => $email]);

        return $sth->fetch();
    }

    public function getUserByToken($token)
    {
        $sth = $this->db->prepare("
            SELECT * FROM `$this->table`
            WHERE `token` = :token
            LIMIT 1
        ");
        $sth->execute([':token' => $token]);

        return $sth->fetch();
    }

    public function isValidEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public function isUnique($email)
    {
        return !((bool) $this->getUserByEmail($email));
    }

    public function isGuest()
    {
        return !((bool) $this->user);
    }
}