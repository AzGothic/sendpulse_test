<?php

namespace app\controllers\web;

class IndexController extends Controller
{
    public function IndexAction()
    {
        if ($this->user->isGuest()) {
            $this->response->redirect('/user/login');
        }

        $this->response->redirect('/todo');
    }
}