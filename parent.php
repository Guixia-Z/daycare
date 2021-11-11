<?php

require_once 'vendor/autoload.php';

require_once 'init.php';

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

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

$app->get('/parent/modifychildinfo/{id:[0-9]+}', function ($request, $response, $args) use ($log) {
    $id = $args['id'];
    $child = DB::queryFirstRow("SELECT firstName, lastName, gender, dateOfBirth, emergencyContact, emergencyPhone FROM children WHERE id=%i", $id);
    $json = json_encode($child, JSON_PRETTY_PRINT);
    $response->getBody()->write($json);
    return $response;
});

$app->map(['PUT','PATCH'], '/parent/modifychildinfo/{id:[0-9]+}', function (Request $request, Response $response, array $args) use ($log) {
    $id = $args['id'];
    $json = $request->getBody();
    $child = json_decode($json, TRUE); // true makes it return an associative array instead of an object
    // validate
    $method = $request->getMethod();
    if ( ($result = validateChildren($child, $method == 'PATCH')) !== TRUE) {
        $response = $response->withStatus(400);
        $response->getBody()->write(json_encode("400 - " . $result));
        return $response;
    }
    $origItem = DB::queryFirstRow("SELECT * FROM children WHERE id=%i", $args['id']);
    if (!$origItem) { // record not found
        $response = $response->withStatus(404);
        $response->getBody()->write(json_encode("404 - not found"));
        return $response;
    }
    // unfortunately using affectedRows() won't work here b/c if data is the same as before
    // it returns 0 even if record exists and belongs to current user
    DB::update('children', $child, "id=%i", $args['id']);
    $log->debug("Record child updated, id=" . $id);
    $count = DB::affectedRows();
    $json = json_encode($count != 0, JSON_PRETTY_PRINT); // true or false
    return $response->getBody()->write($json);
});

function validateChildren($child, $forPatch = false) {
    if ($child === NULL) { // probably json_decode failed due to JSON syntax errors
        return "Invalid JSON data provided";
    }
    // - only allow the fields that must/can be present
    $expectedFields = ["id","email","role","createdTS","firstName","lastName","gender","phoneNumber","address"];
    $userFields = array_keys($child); // get names of fields as an array
    // check if there are any fields that should not be there
    if ($diff = array_diff($userFields, $expectedFields)) {
        return "Invalid fields in User: [". implode(',', $diff). "]";
    }
    //
    if (!$forPatch) { // is it PUT or POST
        // - check if any fields are missing that must be there
        if ($diff = array_diff($expectedFields, $userFields)) {
            return "Missing fields in Todo: [". implode(',', $diff). "]";
        }
    }
    // do not allow any fields to be null - database would not accept it
    $nullableFields = []; // put list of nullable fields here
    foreach($child as $key => $value) {
        if (!in_array($key, $nullableFields)) {
            if (@is_null($value)) { // @ is to suppress a warning (which would be printed out)
                return "$key must not be null";
            }
        }
    }
    // - task 1-100 characters long
    if (isset($child['email'])) {
        $email = $child['email'];
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            return "Email must look like an email";
        }
    }
    // - dueDate a valid date from 1900 to 2099 years
    if (isset($child['phoneNumber'])) {
        if (preg_match('/^([1-9]{3})(-)([0-9]{3})(-)([0-9]{4})$/', $child['phoneNumber']) !== 1) {
            return "phone need to be 888-888-8888";
        }
    }
    // - status must be pending or done
    if (isset($child['address'])) {
        if(strlen($child['address']) < 2){
            return "Please check your address.";
        }
    }
    // if we passed all tests return TRUE
    return TRUE;
}