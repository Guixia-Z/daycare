<?php

require_once 'vendor/autoload.php';

require_once 'init.php';


$app->get('/admin/waitinglist', function ($request, $response, $args) {
    $waitingList = DB::query("SELECT * FROM waitinglist");
    return $this->view->render($response, '/admin/waitinglist.html.twig', ['list' => $waitingList]);
});

$app->get('/admin/child/delete/{id:[0-9]+}', function ($request, $response, $args) {
    $child = DB::queryFirstRow("SELECT * FROM waitinglist WHERE id=%i", $args["id"]);
    if(!$child){
        throw new \Slim\Exception\NotFoundException($request, $response);
    }
    DB::delete("waitinglist", "id=%i", $args["id"]);
    $waitingList = DB::query("SELECT * FROM waitinglist");
    return $this->view->render($response, '/admin/waitinglist.html.twig', ['list' => $waitingList]);
});

$app->get('/admin/educatorlist', function ($request, $response, $args) {
    $educatorList = DB::query("SELECT * FROM users WHERE role='educator'");
    return $this->view->render($response, '/admin/educatorlist.html.twig', ['list' => $educatorList]);
});

// $app->get('/admin/user/list', function .....);