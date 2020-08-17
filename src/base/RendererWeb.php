<?php

namespace app\base;

class RendererWeb
{
    public function partial($viewPath, array $view = [])
    {
        ob_start();
        require APP_PATH . 'views/' . $viewPath . '.php';
        $partial = ob_get_contents();
        ob_end_clean();

        return $partial;
    }

    public function render($layoutPath, array $view = [])
    {
        echo $this->partial($layoutPath, $view);
    }
}