<?php

require_once 'vendor/autoload.php';

require_once 'init.php';

$app->get('/educatoraccount', function ($request, $response, $args) {
    return $this->view->render($response, 'educatoraccount.html.twig');
});

$app->get('/childlist', function ($request, $response, $args) {
    $educatorId = $_SESSION["user"]["id"];
    $childList = DB::query("SELECT * FROM children WHERE educatorId=%i", $educatorId);
    return $this->view->render($response, 'childlist.html.twig',
        ['list' => $childList]);
});

$app->get('/children_attenance', function ($request, $response, $args) {
    $today = date("Y/m/d");
    $educatorId = $_SESSION["user"]["id"];
    $childList = DB::query("SELECT * FROM children WHERE educatorId=%i", $educatorId);
    return $this->view->render($response, 'children_attenance.html.twig',
        ['list' => $childList, 'today' => $today]);
});

