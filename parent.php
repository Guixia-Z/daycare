<?php

require_once 'vendor/autoload.php';

require_once 'init.php';

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

$app->get('/parent/parent_board', function ($request, $response, $args) {
    return $this->view->render($response, '/parent/parent_board.html.twig');
});

$app->get('/addchild_info[/]', function ($request, $response, $args) {
    return $this->view->render($response, 'addchild_info.html.twig');
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

$app->get('/parent/modifychildinfo', function ($request, $response, $args) use ($log){
    $parentId = $_SESSION["user"]["id"];
    $childrenlist = DB::query("SELECT id, firstName, lastName, gender, dateOfBirth, emergencyContact, emergencyPhone FROM children WHERE parentId=%i", $parentId);
  
    return $this->view->render($response, '/parent/modifychildinfo.html.twig', ['list' => $childrenlist]);
});

$app->get('/parent/modifychildinfoJson', function ($request, $response, $args) use ($log){
    $parentId = $_SESSION["user"]["id"];
    $childrenlist = DB::query("SELECT id, firstName, lastName, gender, dateOfBirth, emergencyContact, emergencyPhone FROM children WHERE parentId=%i", $parentId);
    $json = json_encode($childrenlist, JSON_PRETTY_PRINT);
    $response->getBody()->write($json);
    return $response;
});

$app->get('/parent/modifychildinfo/{id:[0-9]+}', function ($request, $response, $args) use ($log) {
    $id = $args['id'];
    $child = DB::queryFirstRow("SELECT id, firstName, lastName, gender, dateOfBirth, emergencyContact, emergencyPhone FROM children WHERE id=%i", $id);
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
    $expectedFields = ["id","firstName","lastName","gender","dateOfBirth","emergencyContact","emergencyPhone"];
    $childFields = array_keys($child); // get names of fields as an array
    // check if there are any fields that should not be there
    if ($diff = array_diff($childFields, $expectedFields)) {
        return "Invalid fields in User: [". implode(',', $diff). "]";
    }
    //
    if (!$forPatch) { // is it PUT or POST
        // - check if any fields are missing that must be there
        if ($diff = array_diff($expectedFields, $childFields)) {
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

      // - status must be pending or done
      if (isset($child['emergencyContact'])) {
        if(strlen($child['emergencyContact']) < 2){
            return "Please check your address.";
        }
    }

    // - dueDate a valid date from 1900 to 2099 years
    if (isset($child['emergencyPhone'])) {
        if (preg_match('/^([1-9]{3})(-)([0-9]{3})(-)([0-9]{4})$/', $child['emergencyPhone']) !== 1) {
            return "phone need to be 888-888-8888";
        }
    }
  
    // if we passed all tests return TRUE
    return TRUE;
}

$app->group('/parent', function (App $app) use ($log) {

    $app->get('/viewwaitinglist', function (Request $request, Response $response, array $args) {
        $parentId = $_SESSION["user"]["id"];
        $waitinglist = DB::query("SELECT id, firstName, lastName, gender, dateOfBirth, hasSibling FROM waitinglist WHERE parentId=%i", $parentId);
        return $this->view->render($response, '/parent/viewwaitinglist.html.twig', ['list' => $waitinglist]);
    });

    $app->get('/viewwaitinglistJson', function (Request $request, Response $response, array $args) {
        $parentId = $_SESSION["user"]["id"];
        $waitinglist = DB::query("SELECT id, firstName, lastName, gender, dateOfBirth, hasSibling FROM waitinglist WHERE parentId=%i", $parentId);
        $json = json_encode($waitinglist, JSON_PRETTY_PRINT);
        $response->getBody()->write($json);
        return $response;
    });

    $app->get('/viewwaitinglist/{id:[0-9]+}', function (Request $request, Response $response, array $args) {
        $id = $args['id'];
        $waitinglist = DB::queryFirstRow("SELECT id, firstName, lastName, gender, dateOfBirth, hasSibling FROM waitinglist WHERE id=%i", $id);
        // 404 if item is null (not found or does not belong to this user)
        $json = json_encode($waitinglist, JSON_PRETTY_PRINT);
        $response->getBody()->write($json);
        return $response;
        //return $this->view->render($response, '/admin/userlist.html.twig');
    });

    $app->map(['PUT','PATCH'], '/viewwaitinglist/{id:[0-9]+}', function (Request $request, Response $response, array $args) use ($log) {
        $id = $args['id'];
        $json = $request->getBody();
        $waitinglist = json_decode($json, TRUE); // true makes it return an associative array instead of an object
        // validate
        $method = $request->getMethod();
        if ( ($result = validateWaitlist($waitinglist, $method == 'PATCH')) !== TRUE) {
            $response = $response->withStatus(400);
            $response->getBody()->write(json_encode("400 - " . $result));
            return $response;
        }
        $origItem = DB::queryFirstRow("SELECT * FROM waitinglist WHERE id=%i", $args['id']);
        if (!$origItem) { // record not found
            $response = $response->withStatus(404);
            $response->getBody()->write(json_encode("404 - not found"));
            return $response;
        }
        // unfortunately using affectedRows() won't work here b/c if data is the same as before
        // it returns 0 even if record exists and belongs to current user
        DB::update('waitinglist', $waitinglist, "id=%i", $args['id']);
        $log->debug("Record waitinglist updated, id=" . $id);
        $count = DB::affectedRows();
        $json = json_encode($count != 0, JSON_PRETTY_PRINT); // true or false
        return $response->getBody()->write($json);
    });

    $app->delete('/viewwaitinglist/{id:[0-9]+}', function (Request $request, Response $response, array $args) use ($log) {
        $id = $args['id'];
        // FIXME: make sure body is empty
        DB::delete('waitinglist', "id=%i", $args['id']);
        $log->debug("Record waitinglist deleted id=" . $id);
        // code is always 200
        // return true if record actually deleted, false if it did not exist in the first place
        $count = DB::affectedRows();
        $json = json_encode($count != 0, JSON_PRETTY_PRINT); // true or false
        return $response->getBody()->write($json);
    });
});

function validateWaitlist($waitinglist, $forPatch = false) {
    if ($waitinglist === NULL) { // probably json_decode failed due to JSON syntax errors
        return "Invalid JSON data provided";
    }
    // - only allow the fields that must/can be present
    $expectedFields = ["id","firstName","lastName","gender","dateOfBirth","hasSibling"];
    $waitinglistFields = array_keys($waitinglist); // get names of fields as an array
    // check if there are any fields that should not be there
    if ($diff = array_diff($waitinglistFields, $expectedFields)) {
        return "Invalid fields in Waitinglist: [". implode(',', $diff). "]";
    }
    //
    if (!$forPatch) { // is it PUT or POST
        // - check if any fields are missing that must be there
        if ($diff = array_diff($expectedFields, $waitinglistFields)) {
            return "Missing fields in Todo: [". implode(',', $diff). "]";
        }
    }
    // do not allow any fields to be null - database would not accept it
    $nullableFields = []; // put list of nullable fields here
    foreach($waitinglist as $key => $value) {
        if (!in_array($key, $nullableFields)) {
            if (@is_null($value)) { // @ is to suppress a warning (which would be printed out)
                return "$key must not be null";
            }
        }
    }

    if (isset($waitinglist['firstName'])) {
        $firstName = $waitinglist['firstName'];
        if (preg_match('/^[A-Za-z]+$/', $waitinglist['firstName']) !== 1) {
            return "Please check your fisrt name";
        }
    }
    
    if (isset($waitinglist['lastName'])) {
        $lastName = $waitinglist['lastName'];
        if (preg_match('/^[A-Za-z]+$/', $waitinglist['lastName']) !== 1) {
            return "Please check your last name";
        }
    }
    // if we passed all tests return TRUE
    return TRUE;
}