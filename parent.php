<?php

require_once 'vendor/autoload.php';

require_once 'init.php';

$app->get('/parent/parent_board', function ($request, $response, $args) {
    return $this->view->render($response, '/parent/parent_board.html.twig');
});