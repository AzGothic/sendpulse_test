<?php
namespace app\controllers\cli;

use app\base\ControllerCli;
use app\base\helpers\CliHelper;
use app\models\User;

class UserController extends ControllerCli
{
    public function CreateAction()
    {
        $userModel = new User();
        $result = $userModel->create($this->request->_GET);

        CliHelper::e('User Created: ' . (int) $result);
    }

    public function RemoveAction()
    {
        $userModel = new User();
        $result = $userModel->remove($this->request->_GET['email']);

        CliHelper::e('User Deleted: ' . (int) $result);
    }
}