<?php

namespace app\controllers\web;

use app\base\ControllerWeb;
use app\base\Form;
use app\models\User;

class Controller extends ControllerWeb
{
    /**
     * @var User $user;
     */
    public $user;

    public function __construct()
    {
        parent::__construct();

        if ($this->request->_POST) {
            if (!Form::checkCsrfToken()) {
                $this->request->session->setFlash('error_message', 'Wrong CSRF Token given!');
                $this->response->redirect('/');
            }
        }

        $this->user = new User();
        $this->response->layout['user'] = $this->user;
    }
}