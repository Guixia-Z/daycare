<?php

require_once 'vendor/autoload.php';

require_once 'init.php';

$app->get('/parent/parent_board', function ($request, $response, $args) {
    return $this->view->render($response, '/parent/parent_board.html.twig');
});

$app->get('/parent/viewchildnote', function ($request, $response, $args) {
    $parentId = $_SESSION["user"]["id"];
    $childNoteList = DB::query("SELECT cl.firstName, cl.lastName, cl.gender, g.groupName, cn.noteCreatedTS, u.firstName educatorName, cn.weight, cn.height, cn.skills, cn.note, cn.photoFilePath " 
    . "FROM children cl, childnotes cn, groups g, users u WHERE cl.id=cn.childId AND g.id=cl.groupId AND u.id=cn.educatorId AND cl.parentId=%i ORDER BY cn.noteCreatedTS DESC", $parentId);
    foreach ($childNoteList as &$childNote) {
        $datetime = strtotime($childNote['noteCreatedTS']);
        $postedDate = date('M d, Y', $datetime);
        $childNote['noteCreatedTS'] = $postedDate;

        $fullBodyNoTags = strip_tags($childNote['note']);
        //$bodyPreview = substr(strip_tags($fullBodyNoTags),0,100);
        //$bodyPreview .= (strlen($fullBodyNoTags) > strlen($bodyPreview)) ? "..." : "";
        $childNote['note'] = $fullBodyNoTags;
    }
    return $this->view->render($response, '/parent/viewchildnote.html.twig', ['list' => $childNoteList]);
});

$app->get('/parent/viewwaitinglist', function ($request, $response, $args) {
    $parentId = $_SESSION["user"]["id"];
    $waitinglist = DB::query("SELECT firstName, lastName, gender, dateOfBirth, hasSibling FROM waitinglist WHERE parentId=%i", $parentId);
    return $this->view->render($response, '/parent/viewwaitinglist.html.twig', ['list' => $waitinglist]);
});

$app->get('/parent/modifychildinfo', function ($request, $response, $args) use ($log){
    $parentId = $_SESSION["user"]["id"];
    $childrenlist = DB::query("SELECT firstName, lastName, gender, dateOfBirth, emergencyContact, emergencyPhone FROM children WHERE parentId=%i", $parentId);
  
    return $this->view->render($response, '/parent/modifychildinfo.html.twig', ['list' => $childrenlist]);
});