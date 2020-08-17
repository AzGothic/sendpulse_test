<?php
namespace app\controllers\cli;

use app\base\ControllerCli;
use app\models\Migration;

class MigrateController extends ControllerCli
{
    public function IndexAction()
    {
        (new Migration())->run();
    }
}