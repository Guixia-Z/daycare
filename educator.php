<?php

require_once 'vendor/autoload.php';

require_once 'init.php';

$app->get('/educatoraccount', function ($request, $response, $args) {
    return $this->view->render($response, 'educatoraccount.html.twig');
});


$app->get('/educator/childlist', function ($request, $response, $args) {
    $educatorId = $_SESSION["user"]["id"];
    $childList = DB::query("SELECT * FROM children WHERE educatorId=%d", $educatorId);
    return $this->view->render($response, '/educator/childlist.html.twig',
        ['list' => $childList]);
});
$app->get('/educator/child_attendance', function ($request, $response, $args) {
    $today = date("Y/m/d");
    $id = $request->getParam('id');
    $childList = DB::query("select a.id, a.firstName, a.lastName from children a where a.id=%d", $id);
    return $this->view->render($response, '/educator/child_attendance.html.twig',
        ['id'=>$id, 'today' => $today,'child'=>$childList[0]]);
});

$app->get('/educator/addchildnotes', function ($request, $response, $args) {
    $id = $request->getParam('id');
$childList = DB::query("select a.id, a.firstName, a.lastName from children a where a.id=%d", $id);
return $this->view->render($response, '/educator/addchildnotes.html.twig',
    ['id'=>$id, 'child'=>$childList[0]]);
});
$app->get('/educator/childnotes_detail', function ($request, $response, $args) {
    $educatorId = $_SESSION["user"]["id"];
    $noteList = DB::query("SELECT a.*, b.firstName,b.lastName FROM childnotes a,children b WHERE a.childId= b.id and a.educatorId =%d", $educatorId);
    return $this->view->render($response, '/educator/childnotes_detail.html.twig',
    ['list' => $noteList]);});

$app->get('/educator/child_attendance_detail', function ($request, $response, $args) {
    $today = date("Y/m/d");
    $educatorId = $_SESSION["user"]["id"];
    $attendanceList = DB::query("SELECT a.*, b.firstName,b.lastName FROM attendance a,children b WHERE a.childId = b.id and b.educatorId =%d", $educatorId);
    return $this->view->render($response, '/educator/child_attendance_detail.html.twig',
    ['list' => $attendanceList, 'today' => $today]);

});


$app->post('/educator/addchildnotes', function ($request, $response, $args) use ($log) {
    $id = $request->getParam('id');
    $firstName = $request->getParam('firstName');
    $lastName = $request->getParam('lastName');
    $height =floatval ($request->getParam('height'));
    $weight = floatval($request->getParam('weight'));
    $skills = ($request->getParam('skills'));
    $note = $request->getParam('note');
    $noteCreatedTS= $request->getParam('noteCreatedTS');
    
    $errorList = [];
    if (strlen($note) < 2 || strlen($note) > 1000) {
        $errorList[] = "Item description must be 2-1000 characters long";
    }
    if (!is_numeric($weight)||$weight  < 0 ) {
        $errorList[] = "height must be a number positive";
    }
    if (!is_numeric($height)||$height  < 0 ) {
        $errorList[] = "height must be a number positive";
    }
    if ($errorList) { 
        $valuesList = ['id' => $id,'height' => $height,'weight' => $weight,'skills' => $skills,'note' => $note,'noteCreatedTS'=>$noteCreatedTS];
        return $this->view->render($response, '/educator/addchildnotes',
         ['errorList' => $errorList, 'v' => $valuesList]);

    } else { 
        $valuesList = ['id' => $id,'height' => $height,'weight' => $weight,'skills' => $skills,'note' => $note,'noteCreatedTS'=>$noteCreatedTS];
    DB::update('childnotes', $valuesList, 'childId=%i', $id);
    return $this->view->render($response, '/educator/notesave_success.html.twig');}
    
});
