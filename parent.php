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