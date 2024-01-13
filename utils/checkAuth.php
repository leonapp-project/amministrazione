<?php

//get the OAuth token
$OAuth = $_COOKIE['OAuth_key'];
//check if the OAuth token is valid
require_once 'mysqli.php';
$stmt = $mysqli->prepare("SELECT * FROM OAuth WHERE okey = ?");
$stmt->bind_param("s", $OAuth);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    //the OAuth token is not valid
    header("Location: index.php");
    exit();
}
//the OAuth token is valid
$OAuth = $result->fetch_assoc();
if ($OAuth['grade'] > 1) {
    //the OAuth token is not valid
    header("Location: index.php");
    exit();
}
//check if the current timestamp is greater than the expiration timestamp
if (time() > strtotime($OAuth['expiration'])) {
    //the OAuth token is not valid
    header("Location: index.php");
    exit();
}

//permissions needed
// for any view: administration.view
// to see the home page: administration.view.home
// to see the system page: administration.view.system
// so to see sistema.php you need administration.view and administration.system
$permissions_needed = array("administration.view.view");
$requested_page = basename($_SERVER['PHP_SELF']);
if ($requested_page === "home.php") {
    $permissions_needed[] = "administration.view.home";
} else if ($requested_page === "sistema.php") {
    $permissions_needed[] = "administration.view.sistema";
} else if($requested_page === "adminUser.php") {
    $permissions_needed[] = "administration.view.sistema";
}

//now check if the OAuth satifsfies every permission needed
$access_to = json_decode($OAuth['access_to'], true);
foreach ($permissions_needed as $permission) {
    if (!in_array($permission, $access_to) && !in_array(substr($permission, 0, strrpos($permission, ".")), $access_to) && !in_array(substr($permission, 0, strpos($permission, ".")), $access_to) && !in_array("*", $access_to)) {
        //the OAuth token is not valid
        header("Location: index.php");
        exit();
    }
}
/*
//check if the access_to is valid, if it has the permission of administration.view or administration.* or *
$access_to = json_decode($OAuth['access_to'], true);
if (!isset($access_to['administration.view']) && !isset($access_to['administration.*']) && !isset($access_to['*'])) {
    //the OAuth token is not valid
    header("Location: index.php");
    exit();
}*/
?>