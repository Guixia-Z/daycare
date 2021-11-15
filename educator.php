<?php

require_once 'vendor/autoload.php';

require_once 'init.php';

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

$app->get('/educator/educator_board', function ($request, $response, $args) {
    return $this->view->render($response, '/educator/educator_board.html.twig');
});
$app->get('/educator/childlist', function ($request, $response, $args) {
    $educatorId = $_SESSION["user"]["id"];
    $educator = DB::queryFirstRow("SELECT concat(firstName,' ', lastName) AS fullName, concat(b.groupName,'-',b.groupDescription) AS groupAll FROM users a, groups b WHERE a.id=b.educatorId and a.id=%d", $educatorId);
    $childList = DB::query("SELECT * FROM children WHERE educatorId=%d", $educatorId);

    return $this->view->render($response, '/educator/childlist.html.twig',
['list' => $childList, 'educator'=>$educator]);
});

  $app->get('/educator/addchildnotes', function ($request, $response, $args) {
    $childId = $request->getParam('childId');
    $child = DB::queryFirstRow("select a.* from children a where a.id=%d", $childId);
   return $this->view->render($response, '/educator/addchildnotes.html.twig',
    ['child'=>$child]);
  });

    $app->get('/educator/note_edit', function ($request, $response, $args) {
        $noteId = $request->getParam('noteId');
        $note = DB::queryFirstRow("select a.*,b.firstName,b.lastName,b.id childId  from childnotes a, children b where a.childId=b.id and a.id=%d", $noteId);
        return $this->view->render($response, '/educator/note_edit.html.twig',
        ['note'=>$note]);
    });

    $app->get('/educator/note_remove', function ($request, $response, $args) {
        $noteId = $request->getParam('noteId');
        $childId = $request->getParam('childId');
        $today = date("Y-m-d");
        DB::delete("childnotes", "id=%i", $noteId);
        $List = DB::query("SELECT * FROM childnotes");
        $educatorId = $_SESSION["user"]["id"];
        $noteList = DB::query("SELECT a.id,noteCreatedTS, a.weight, a.height, a.skills, a.note, a.photoFilePath, a.educatorId, a.childId, b.firstName,b.lastName FROM childnotes a,children b WHERE a.childId= b.id and a.educatorId =%d and a.childId=%d order by a.id desc" , $educatorId, $childId);
        return $this->view->render($response, '/educator/noteHistory.html.twig',
        ['list' => $noteList,'today' => $today, 'childId'=>$childId]);
        });

    $app->get('/educator/noteHistory', function ($request, $response, $args) {
        $childId = $request->getParam('id');
        $educatorId = $_SESSION["user"]["id"];
        $today = date("M d, Y");
        $child = DB::queryFirstRow("SELECT a.firstName,  a.lastName FROM children a WHERE a.id=%d order by a.id desc" , $childId);
        $noteList = DB::query("SELECT a.id,  noteCreatedTS, a.weight, a.height, a.skills, a.note, a.photoFilePath, a.educatorId, a.childId, b.firstName,b.lastName FROM childnotes a,children b WHERE a.childId= b.id and a.educatorId =%d and a.childId=%d order by a.id desc" , $educatorId, $childId);
        foreach ($noteList as &$note) {
           // $datetime = strtotime($note['noteCreatedTS']);
           // $postedDate = date('M d, Y', $datetime);
           // $note['noteCreatedTS'] = $postedDate;
            $fullBodyNoTags = strip_tags($note['note']);
            $note['note'] = $fullBodyNoTags;
        }
        return $this->view->render($response, '/educator/noteHistory.html.twig',
        ['list' => $noteList,'today' => $today, 'childId'=>$childId,'child'=>$child]);
    });

    $app->get('/educator/attendanceHistory', function ($request, $response, $args) {
        $today = date("M d, Y");
        $childId = $request->getParam('id');
        $child = DB::queryFirstRow("SELECT a.firstName,  a.lastName FROM children a WHERE a.id=%d order by a.id desc" , $childId);
        $attendanceList = DB::query("select c.id childId, a.id attendanceId, c.firstName, c.lastName, a.date, (CASE a.startTime WHEN '00:00:00' THEN '' ELSE a.startTime END) AS startTime,( CASE a.endTime WHEN '00:00:00' THEN '' ELSE a.endTime END ) AS endTime, a.note, a.status FROM children c, attendance a where a.childId=c.id and c.id=%d order by a.date desc", $childId);
       foreach ($attendanceList as &$note) {
            $fullBodyNoTags = strip_tags($note['note']);
            $note['note'] = $fullBodyNoTags;
        }
        return $this->view->render($response, '/educator/attendanceHistory.html.twig',
        [ 'today' => $today,'list'=>$attendanceList,'childId'=>$childId,'child'=>$child]);

    });

    $app->get('/educator/attendance_remove', function ($request, $response, $args) {
        $today = date("M d, Y");
        $attendanceId = $request->getParam('attendanceId');
        $childId = $request->getParam('childId');
        DB::delete("attendance", "id=%i", $attendanceId);
        $attendanceList = DB::query("select c.id childId, a.id attendanceId, c.firstName, c.lastName, a.date, a.startTime,a.endTime, a.note, a.status FROM children c, attendance a where a.childId=c.id and c.id=%d", $childId);
        return $this->view->render($response, '/educator/attendanceHistory.html.twig',
        [ 'today' => $today,'list'=>$attendanceList, 'childId'=>$childId]);
    });

$app->get('/educator/childnotes_detail', function ($request, $response, $args) {
    $educatorId = $_SESSION["user"]["id"];
    $noteList = DB::query("SELECT a.id, a.noteCreatedTS, a.weight, a.height, a.skills, a.note, a.photoFilePath, a.educatorId, a.childId, b.firstName,b.lastName FROM childnotes a,children b WHERE a.childId= b.id and a.educatorId =%d order by a.id desc ", $educatorId);
     foreach ($noteList as &$note) {
       // $datetime = strtotime($note['noteCreatedTS']);
       //$postedDate = date('M d, Y', $datetime);
       // $note['noteCreatedTS'] = $postedDate;
        $fullBodyNoTags = strip_tags($note['note']);
        $note['note'] = $fullBodyNoTags;
    }
    return $this->view->render($response, '/educator/childnotes_detail.html.twig',
    ['list' => $noteList]);});
    
    $app->get('/educator/child_attendance', function ($request, $response, $args) {
        $today = date("M d, Y");
        $id = $request->getParam('childId');
        $child = DB::queryFirstRow("select a.id, a.firstName, a.lastName from children a where a.id=%d", $id);
        return $this->view->render($response, '/educator/child_attendance.html.twig',
            ['id'=>$id, 'today' => $today,'child'=>$child]);
    });
    
$app->get('/educator/child_attendance_detail', function ($request, $response, $args) {
   
    $educatorId = $_SESSION["user"]["id"];
    //$attendanceList = DB::query("SELECT a.id,a.date, (CASE a.startTime WHEN '00:00:00' THEN '' ELSE a.startTime END) AS startTime,( CASE a.endTime WHEN '00:00:00' THEN '' ELSE a.endTime END ) AS endTime, a.status,a.note,a.childId, b.firstName,b.lastName FROM attendance a,children b WHERE a.childId = b.id and b.educatorId =%d order by a.date desc", $educatorId);
    return $this->view->render($response, '/educator/child_attendance_detail.html.twig');
    //['list' => $attendanceList]);
});

$app->get('/educator/child_attendance_detail_json', function (Request $request, Response $response, array $args) {
    $educatorId = $_SESSION["user"]["id"];
    $attendanceList = DB::query("SELECT a.id,a.date, (CASE a.startTime WHEN '00:00:00' THEN '' ELSE a.startTime END) AS startTime,( CASE a.endTime WHEN '00:00:00' THEN '' ELSE a.endTime END ) AS endTime, a.status,a.note,a.childId, b.firstName,b.lastName FROM attendance a,children b WHERE a.childId = b.id and b.educatorId =%d order by a.date desc", $educatorId);
    $json = json_encode($attendanceList, JSON_PRETTY_PRINT);
    $response->getBody()->write($json);
    return $response;
});


$app->post('/educator/child_attendance', function ($request, $response, $args) use ($log) {
    $childId = $request->getParam('childId');
    $firstName = $request->getParam('firstName');
    $lastName = $request->getParam('lastName');
    $date=date('Y-m-d', strtotime($request->getParam('date'))); 
    $startTS = date('h:i:sa',strtotime($request->getParam('startTime')));
    $endTS= date('h:i:sa',strtotime($request->getParam('endTime')));
    $status= $request->getParam('status');
    $note = $request->getParam('note');
    $today = date("Y-m-d");
    $errorList = [];
    
    if ($date=='1969-12-31') {
        $errorList[] = "You must choose a date";
    }
    if ($startTS=='07:00:00pm'&&$endTS=='07:00:00pm'&&$status == "present") {
        $errorList[] = "You must choose one time";
    }
    if ($errorList) { 
        $child = DB::queryFirstRow("select a.id, a.firstName, a.lastName from children a where a.id=%d", $childId);
        return $this->view->render($response, 
        '/educator/child_attendance.html.twig', 
        ['errorList' => $errorList, 'id'=>$childId, 'today' => $today,'child'=>$child]);
    } else { 
        if ($startTS=='07:00:00pm'){
            $startTS='00:00:00'; 
        }
        if ($endTS=='07:00:00pm'){
            $endTS='00:00:00'; 
        }
        if($status == "absent"){
            $valuesList = ["date" =>$date,"startTime" => "", "endTime" => "", "status" => "absent", "note" => $note, "childId"=>intval($childId)];
            DB::insert('attendance', $valuesList);
        }else{
            $valuesList = ["date" =>$date,"startTime" => $startTS, "endTime" => $endTS, "status" => $status, "note" => $note, "childId"=>intval($childId)];
            DB::insert('attendance', $valuesList);
        }
        return $this->view->render($response, '/educator/child_check_success.html.twig',['childId'=>$childId]);
    }
});

$app->post('/educator/noteHistory/delete/{id:[0-9]+}', function ($request, $response, $args) {
    $child = DB::queryFirstRow("SELECT * FROM childnotes WHERE id=%i", $args["id"]);
    if(!$child){
        throw new \Slim\Exception\NotFoundException($request, $response);
    }
    DB::delete("childnotes", "id=%i", $args["id"]);
    $List = DB::query("SELECT * FROM childnotes");
    return $this->view->render($response, '/educaotr/noteHistory.html.twig', ['list' => $List]);
});


$app->get('/educator/child_attendance_check_success', function ($request, $response, $args) {
    $today = date("M d, Y");
    $id = $request->getParam('id');
    $childList = DB::query("select a.id, a.firstName, a.lastName from children a where a.id=%d", $id);
    return $this->view->render($response, '/educator/child_attendance_check_success.html.twig',
        ['id'=>$id, 'today' => $today,'child'=>$childList[0]]);
});

