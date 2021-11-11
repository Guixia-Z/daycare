<?php
require_once 'vendor/autoload.php';
require_once 'init.php';

$app->post('/educator/addchildnotes', function ($request, $response, $args) use ($log) {
    $childId = $request->getParam('childId');
    $educatorId = $_SESSION["user"]["id"];
    $height =$request->getParam('height');
    $weight =$request->getParam('weight');
    $skills = $request->getParam('skills');
    $note = $request->getParam('note');
    $today = date("Y/m/d");
    $noteCreatedTs = $today;
    $errorList = [];
    
    if (!is_numeric($weight)||$weight  < 0 ) {
        $errorList[] = "weight can not be negative number";
    }
    if (!is_numeric($height)||$height  < 0 ) {
        $errorList[] = "height can not be negative number";
    }

    // image validation
    $uploadedImage = $request->getUploadedFiles()['image'];
    if($_FILES['image']['name'] != "") {
        $destImageFilePath = null;
        $result = verifyUploadedPhoto($uploadedImage, $destImageFilePath);
        if ($result !== TRUE) {
            $errorList []= $result;
        }
    }
    if ($errorList) { 
        $child = $request->getParam('childId');
        $child = DB::queryFirstRow("select a.* from children a where a.id=%d", $childId);
        return $this->view->render($response, '/educator/addchildnotes.html.twig',
         ['errorList' => $errorList, 'child' => $child]);
    }
    $valuesList = ["noteCreatedTS" => $noteCreatedTs, 
        "weight" => floatval ($weight), 
        "height" => floatval ($height), 
        "skills" => $skills, 
        "note"=>$note,
        "educatorId" => intval($educatorId),
        "childId" => intval($childId)];
    if($_FILES['image']['name'] != "") {
        $uploadedImage->moveTo($destImageFilePath); // FIXME: check if it failed !
        $valuesList['photoFilePath'] = $destImageFilePath;
    }
    DB::insert('childnotes', $valuesList);
    return $this->view->render($response, '/educator/notesave_success.html.twig');
});


$app->post('/educator/note_edit', function ($request, $response, $args) use ($log) {
    $id = $request->getParam('id');
    $height =$request->getParam('height');
    $weight = $request->getParam('weight');
    $skills = ($request->getParam('skills'));
    $note = $request->getParam('note');
    $today = date("Y/m/d");
    $noteCreatedTs = $today;
    $errorList = [];
    
    if (!is_numeric($weight)||$weight  < 0 ) {
        $errorList[] = "height must be a number positive";
    }
    if (!is_numeric($height)||$height  < 0 ) {
        $errorList[] = "height must be a number positive";
    }
    // image validation
    $uploadedImage = $request->getUploadedFiles()['image'];
    if($_FILES['image']['name'] != "") {
        $destImageFilePath = null;
        $result = verifyUploadedPhoto($uploadedImage, $destImageFilePath);
        if ($result !== TRUE) {
            $errorList []= $result;
        }
    }
    if ($errorList) { 
        $noteId = $request->getParam('id');
        $note = DB::queryFirstRow("select a.*,b.firstName,b.lastName,b.id childId  from childnotes a, children b where a.childId=b.id and a.id=%d", $noteId);
        return $this->view->render($response, '/educator/note_edit.html.twig',
         ['errorList' => $errorList, 'note' => $note]);
    } 

    $valuesList = ['height' => $height,'weight' => $weight,
    'skills' => $skills,'note' => $note,"noteCreatedTS" => $noteCreatedTs,];
    
    if($_FILES['image']['name'] != "") {
        $uploadedImage->moveTo($destImageFilePath); // FIXME: check if it failed !
        $valuesList['photoFilePath'] = $destImageFilePath;
    }
    DB::update('childnotes', $valuesList, 'id=%d', intval($id));
    return $this->view->render($response, '/educator/editsave_success.html.twig');
});




function verifyUploadedPhoto($photo, &$newFilePath) {
    if ($photo->getError() !== UPLOAD_ERR_OK) {
        return "Error uploading photo " . $photo->getError();
    }
    if($photo->getSize() > 2*1024*1024){
        return "File too big. 2MB max is allowed.";
    }
    $info = getimagesize($photo->file);
    if ($info[0] < 100 || $info[0] > 1000 || $info[1] < 100 || $info[1] > 1000) {
        return "Width and height must be within 100-1000 pixels range";
    }
    $ext = "";
    switch ($info['mime']) {
        case 'image/jpeg': $ext = "jpg"; break;
        case 'image/gif': $ext = "gif"; break;
        case 'image/png': $ext = "png"; break;
        case 'image/bmp': $ext = "bmp"; break;
        default:
            return "Only JPG, GIF, and PNG file types are accepted";
    }
    $newFilePath = "uploads/" . substr($photo->getClientFilename(),0,strpos($photo->getClientFilename(), '.')) . "." . $ext;
    return TRUE;
}
