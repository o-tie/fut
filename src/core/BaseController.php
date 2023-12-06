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
        // Задайте данные для макета
        $pageTitle = 'Your Default Title';
        $view = str_replace('.', '/', $view);
        $content = $this->renderView(__DIR__ . "/../views/$view.php", $data);

        // Подключите общий макет
        include(__DIR__ . '/../views/layouts/index.php');
    }

    /**
     * @param $viewFile
     * @param $data
     * @return false|string
     */
    private function renderView($viewFile, $data): false|string
    {
        // Функция для рендеринга вида с данными
        ob_start();
        extract($data);
        include($viewFile);
        return ob_get_clean();
    }

    /**
     * @param $data
     */
    protected function jsonResponse($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
    }
}
