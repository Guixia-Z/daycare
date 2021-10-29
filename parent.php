<?php

require_once 'vendor/autoload.php';

require_once 'init.php';

$app->get('/parentaccount', function ($request, $response, $args) {
    return $this->view->render($response, 'parentaccount.html.twig');
});