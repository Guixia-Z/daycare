<?php

require_once 'vendor/autoload.php';

require_once 'init.php';

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