<?php
session_start();
require_once 'vendor/autoload.php';
require_once 'init.php';
require_once 'utils.php';

// Define app routes below
require_once "user.php";
require_once 'admin.php';
require_once 'auctions.php';


// Run app
$app->run();