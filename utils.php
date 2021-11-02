<?php
require_once 'vendor/autoload.php';
require_once 'init.php';

$app->post('/educator/addchildnotes', function ($request, $response, $args) use ($log) {
    $id = $request->getParam('id');
    $firstName = $request->getParam('firstName');
    $lastName = $request->getParam('lastName');
    $height =floatval ($request->getParam('height'));
    $weight = floatval($request->getParam('weight'));
    $skills = ($request->getParam('skills'));
    $note = $request->getParam('note');
   
    $errorList = [];
    if (strlen($note) < 2 || strlen($note) > 1000) {
        $errorList[] = "note must be 2-1000 characters long";
    }
    if (!is_numeric($weight)||$weight  < 0 ) {
        $errorList[] = "height must be a number positive";
    }
    if (!is_numeric($height)||$height  < 0 ) {
        $errorList[] = "height must be a number positive";
    }
// image validation
$uploadedImage = $request->getUploadedFiles()['image'];
$destImageFilePath = null;
$result = verifyUploadedPhoto($uploadedImage, $destImageFilePath);
if ($result !== TRUE) {
    $errorList []= $result;
}
$valuesList = ['id' => $id,'height' => $height,'weight' => $weight,'skills' => $skills,'note' => $note];
    if ($errorList) { 
        return $this->view->render($response, '/educator/addchildnotes.html.twig',
         ['errorList' => $errorList, 'v' => $valuesList]);

    } else { 
        $uploadedImage->moveTo($destImageFilePath); // FIXME: check if it failed !
        $valuesList['photoFilePath'] = $destImageFilePath;

       DB::update('childnotes', $valuesList, 'childId=%i', $id);
    return $this->view->render($response, '/educator/notesave_success.html.twig');}
    
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