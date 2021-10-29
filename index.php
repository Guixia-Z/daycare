<?php

require_once 'vendor/autoload.php';
require_once 'init.php';
require_once 'utils.php';

// Define app routes below
require_once "user.php";
require_once 'admin.php';
require_once 'educator.php';
require_once 'parent.php';



// Run app
$app->run();