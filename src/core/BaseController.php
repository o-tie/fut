<?php

namespace core;

class BaseController
{
    /**
     * @param $view
     * @param array $data
     * @return void
     */
    protected function render($view, array $data = []): void
    {
        $view = str_replace('.', '/', $view);
        $content = $this->renderView(__DIR__ . "/../views/$view.php", $data);

        include(__DIR__ . '/../views/layouts/index.php');
    }

    /**
     * View rendering
     * @param $viewFile
     * @param $data
     * @return false|string
     */
    private function renderView($viewFile, $data): false|string
    {
        ob_start();
        extract($data);
        include($viewFile);

        return ob_get_clean();
    }

    /**
     * @param $data
     */
    protected function jsonResponse($data): void
    {
        header('Content-Type: application/json');
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
        echo json_encode($data);
    }
}
