<?php

namespace app\base;

use app\base\helpers\SecurityHelper;

class Form
{
    public static function getCsrfToken()
    {
        $csrfToken = SecurityHelper::randomString(32);
        /** @var RequestWeb $request */
        $request = Application::$request;
        $request->session->set('csrf_token', $csrfToken);

        return $csrfToken;
    }

    public static function checkCsrfToken()
    {
        /** @var RequestWeb $request */
        $request = Application::$request;
        if (!$request->session->has('csrf_token')) {
            return false;
        }

        if (empty($_POST['csrf_token'])) {
            return false;
        }

        return ($_POST['csrf_token'] == $request->session->get('csrf_token'));
    }
}