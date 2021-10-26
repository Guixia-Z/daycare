<?php

require_once 'vendor/autoload.php';

require_once 'init.php';


$app->get('/admin/waitinglist', function ($request, $response, $args) {
    $waitingList = DB::query("SELECT * FROM waitinglist");
    return $this->view->render($response, '/admin/waitinglist.html.twig', ['list' => $waitingList]);
});

// $app->get('/admin/user/list', function .....);