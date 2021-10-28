<?php

require_once 'vendor/autoload.php';

require_once 'init.php';

$app->get('/', function ($request, $response, $args) {
    return $this->view->render($response, 'index.html.twig');
});

$app->get('/createaccount', function ($request, $response, $args) {
    return $this->view->render($response, 'createaccount.html.twig');
});

$app->post('/createaccount', function ($request, $response, $args) use ($log) {
    $email = $request->getParam('email');
    $pass1 = $request->getParam('pass1');
    $pass2 = $request->getParam('pass2');

    $errorList = [];
    if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        $errorList[] = "Email must look like an email";
        $email = "";
    }else{
        $result = DB::queryFirstField("SELECT email FROM users WHERE email=%s",$email);
        if($result != null){
            $errorList[] = "This email is already registered";
            $email = "";
        }
    }
    if($pass1 != $pass2){
        $errorList []= "Passwords do not match";
    }else{
        if (strlen($pass1) < 6 || strlen($pass1) > 100
            || (preg_match("/[A-Z]/", $pass1) !== 1)
            || (preg_match("/[a-z]/", $pass1) !== 1)
            || (preg_match("/[0-9]/", $pass1) !== 1)) {
                 $errorList []= "Password must be 6-100 characters long and contain at least one "
                    . "uppercase letter, one lowercase, and one digit.";
            }
        }
    if ($errorList) {
        return $this->view->render($response, 'createaccount.html.twig', ["email" => $email,'errorList' => $errorList]);
    }else{
        $_SESSION["email"] = $email;
        $_SESSION["pass"] = $pass1;
        
        return $this->view->render($response, 'account_nextstep.html.twig');
    }

});

$app->get('/userinfo', function ($request, $response, $args) {
    return $this->view->render($response, 'userinfo.html.twig');
});

$app->post('/userinfo', function ($request, $response, $args) use ($log) {
    $fname = $request->getParam('fname');
    $lname = $request->getParam('lname');
    $gender = $request->getParam('gender');
    $phone = $request->getParam('phone');
    $address = $request->getParam('address');

    $errorList = [];
    if (preg_match('/^[A-Za-z]+$/', $fname) !== 1) {
        $errorList []= "Please check your fisrt name";
    }
    if (preg_match('/^[A-Za-z]+$/', $lname) !== 1) {
        $errorList []= "Please check your last name";
    }
    if (preg_match('/^([1-9]{3})(-)([0-9]{3})(-)([0-9]{4})$/', $phone) !== 1) {
        $errorList []= "phone need to be 888-888-8888";
    }
    if(strlen($address) < 2){
        $errorList []= "Please check your address.";
    }
    if ($errorList) {
        $valuesList = ["fname" => $fname, "lname" => $lname, "phone" => $phone, "address" => $address];
        return $this->view->render($response, 'userinfo.html.twig',
            ['v' => $valuesList, 'errorList' => $errorList]);
    }else{
        $valuesList = ["email" => $_SESSION["email"], "password" => $_SESSION["pass"], "role" => "parent", "firstName" => $fname, "lastName" => $lname, "gender" => $gender, "phoneNumber" => $phone, "address" => $address];
        DB::insert('users', $valuesList);
        $id = DB::queryFirstField("SELECT id FROM users WHERE email=%s",$_SESSION["email"]);
        
        return $this->view->render($response, 'parentaccount.html.twig',["id" => $id]);
        
        unset($_SESSION["email"],$_SESSION["pass"]);
    }
});

$app->get('/addchild_info/{id:[0-9]+}', function ($request, $response, $args) {
    $user = DB::queryFirstRow("SELECT * FROM users WHERE id=%i", $args['id']);
    if ($user) {
        return $this->view->render($response, 'addchild_info.html.twig', ['a' => $user]);
    } else { // not found - cause 404 here
        throw new \Slim\Exception\NotFoundException($request, $response);
    }
});

$app->post('/addchild_info/{id:[0-9]+}', function ($request, $response, $args) use ($log) {
    $user = DB::queryFirstRow("SELECT * FROM users WHERE id=%i", $args['id']);
    if (!$user) { // not found - cause 404 here
        throw new \Slim\Exception\NotFoundException($request, $response);
    }
    $fname = $request->getParam('fname');
    $lname = $request->getParam('lname');
    $gender = $request->getParam('gender');
    $dob = $request->getParam('dob');
    $dob = date("Y-m-d", strtotime($dob));
    $sibling = $request->getParam('sibling');

    $errorList = [];
    if (preg_match('/^[A-Za-z]+$/', $fname) !== 1) {
        $errorList []= "Please check your fisrt name";
    }
    if (preg_match('/^[A-Za-z]+$/', $lname) !== 1) {
        $errorList []= "Please check your last name";
    }
    /*if (preg_match('/^[0-9]{4})(-)([0-9]{2})(-)([0-9]{2})$/', $dob) !== 1) {
        $errorList []= "Date of birth need to like 2019-08-31,Thank you.";
    }*/
    if ($errorList) { // STATE 2: errors
        $valuesList = ["fname" => $fname, "lname" => $lname, "dob" => $dob];
        return $this->view->render($response, 'addchild_info.html.twig',
            ['v' => $valuesList, 'errorList' => $errorList]);
    } else {
        $valuesList = ["firstName" => $fname, "lastName" => $lname, "gender" => $gender, "dateOfBirth" => $dob, "hasSibling" => $sibling, "parentId" => $args["id"]];
        DB::insert('waitinglist', $valuesList);
        return $this->view->render($response, 'addchild_success.html.twig');
    }
});

$app->get('/login', function ($request, $response, $args) {
    return $this->view->render($response, 'login.html.twig');
});


$app->post('/login', function ($request, $response, $args) use ($log) {
    $email = $request->getParam('email');
    $role = $request->getParam('role');
    $password = $request->getParam('password');

    $result = DB::queryFirstRow("SELECT * FROM users WHERE email=%s",$email);
    if(!$result){
        //return $response->write("user account not found");
        throw new \Slim\Exception\NotFoundException($request, $response);
    }
    $loginSuccessful = ($result["role"] == $role) && ($result["password"] == $password);
    if(!$loginSuccessful){
        return $response->write("Invalid username or password <a href='/login'>Go back to login</a>");
    }else{
        unset($result["password"]);
        $_SESSION["user"] = $result;
        if($result["role"] == "parent"){
            $id = DB::queryFirstField("SELECT id FROM users WHERE email=%s",$result["email"]);
            return $this->view->render($response, 'parent_board.html.twig',["id" => $id]);
        }
        if($result["role"] == "educator"){
            return $this->view->render($response, 'educator_board.html.twig');
        }
        if($result["role"] == "admin"){
            return $this->view->render($response, '/admin/manager_board.html.twig');
        }
    }
});  

$app->get('/logout', function ($request, $response, $args) {
    unset($_SESSION["user"]);
    return $this->view->render($response, 'logout.html.twig');
});

// $app->get('/register', function .....);

// $app->get('/login', function .....);

// $app->get('/logout', function .....);

// $app->get('/profile', function .....);