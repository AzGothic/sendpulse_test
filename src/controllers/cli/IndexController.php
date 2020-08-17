<?php
namespace app\controllers\cli;

use app\base\ControllerCli;
use app\base\helpers\CliHelper;

class IndexController extends ControllerCli
{
    public function IndexAction()
    {
        CliHelper::e('CLI successful');
    }
}