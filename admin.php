<?php

require_once 'vendor/autoload.php';

require_once 'init.php';


$app->get('/admin/waitinglist', function ($request, $response, $args) {
    $waitingList = DB::query("SELECT * FROM waitinglist");
    return $this->view->render($response, '/admin/waitinglist.html.twig', ['list' => $waitingList]);
});

/*$app->get('/admin/waitinglist/{op:add|delete}[/{id:[0-9]+}]', function ($request, $response, $args) {
    $waitingList = DB::query("SELECT * FROM waitinglist");
    return $this->view->render($response, '/admin/waitinglist.html.twig', ['list' => $waitingList]);
});*/

$app->post('/admin/waitinglist/delete/{id:[0-9]+}', function ($request, $response, $args) {
    $child = DB::queryFirstRow("SELECT * FROM waitinglist WHERE id=%i", $args["id"]);
    if(!$child){
        throw new \Slim\Exception\NotFoundException($request, $response);
    }
    DB::delete("waitinglist", "id=%i", $args["id"]);
    $waitingList = DB::query("SELECT * FROM waitinglist");
    return $this->view->render($response, '/admin/waitinglist.html.twig', ['list' => $waitingList]);
});

$app->post('/admin/waitinglist/add/{id:[0-9]+}', function ($request, $response, $args) {
    $child = DB::queryFirstRow("SELECT * FROM waitinglist WHERE id=%i", $args["id"]);
    if(!$child){
        throw new \Slim\Exception\NotFoundException($request, $response);
    }
    $level1 = "2017-09-30";
    $level2 = "2018-09-30";
    $level3 = "2019-09-30";
    if(strtotime($child["dateOfBirth"] > strtotime($level3))){
        $groupid = 1;
    }else if(strtotime($child["dateOfBirth"] > strtotime($level2))){
        $groupid = 3;
    }else{
        $groupid = 2;
    }
    $teacherid = DB::queryFirstField("SELECT educatorId FROM groups WHERE id=%i", $groupid);

    DB::insert("children", ["firstName" => $child["firstName"], "lastName" => $child["lastName"], "gender" => $child["gender"], "dateOfBirth" => $child["dateOfBirth"], "firstSchoolDay" => date("Y-m-d"),"parentId" => $child["parentId"], "educatorId" => $teacherid, "groupId" => $groupid]);
    DB::delete("waitinglist", "id=%i", $args["id"]);
    //$waitingList = DB::query("SELECT * FROM waitinglist");
    return $this->view->render($response, '/admin/waitinglist.html.twig');
});

$app->get('/admin/educatorlist', function ($request, $response, $args) {
    $educatorList = DB::query("SELECT * FROM users WHERE role='educator'");
    return $this->view->render($response, '/admin/educatorlist.html.twig', ['list' => $educatorList]);
});

// $app->get('/admin/user/list', function .....);