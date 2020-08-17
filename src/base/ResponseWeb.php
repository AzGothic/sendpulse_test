<?php

namespace app\base;

class ResponseWeb extends Response
{
    /**
     * @var RendererWeb $renderer
     */
    public $renderer;

    /**
     * @var array $view;
     */
    public $view = [];

    /**
     * @var array $layout;
     */
    public $layout = [];

    public function __construct()
    {
        $this->renderer = new RendererWeb();
    }

    public function redirect($location, $code = 302)
    {
        http_response_code($code);
        header("Location: $location", true);
        exit;
    }

    public function render()
    {
        parent::render();

        $view = $this->layout;
        $view['content'] = $this->renderer->partial(Application::$route['controller'] . '/' . Application::$route['action'], $this->view);
        $this->renderer->render('layout', $view);
    }
}