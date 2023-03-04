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
//check if the access_to is valid, if it has the permission of administration.view or administration.* or *
$access_to = json_decode($OAuth['access_to'], true);
if (!isset($access_to['administration.view']) && !isset($access_to['administration.*']) && !isset($access_to['*'])) {
    //the OAuth token is not valid
    header("Location: index.php");
    exit();
}
?>