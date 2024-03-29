<?php
//Path: api/getOAuthList.php
/* MySQL tables
utenti
id, first_name, last_name, email, last_login, date_joined, class_branch, class_section, can_buy_tickets, username

OAuth
id, type, grade, okey, expiration, access_to, commento
*/
/* 
this is an API api/getStudentData.php?
OAuth: 32 chars
then one of the two:
- purpose: string (the purpose of the OAuth key)
The OAuth grade must be less than or equal to 1. It must not be expired (expiration is a timestamp). 
The access_to, which is a JSON, should have the key/permission administration.OAuth.getKeyList set to true or
any of the roots set to true for example administration.OAuth.* or administration.* or *.
*/

//import once the database connection
require_once 'mysqli.php';
header('Content-Type: application/json');

//get the OAuth token
$OAuth = $_GET['OAuth'];
//if the OAuth token is not set, get it from the cookie
if (!isset($OAuth)) {
    $OAuth = $_COOKIE['OAuth_key'];
}

//get the purpose
$purpose = $_GET['purpose'];

//check if the OAuth token is valid
$stmt = $mysqli->prepare("SELECT * FROM OAuth WHERE okey = ?");
$stmt->bind_param("s", $OAuth);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    //the OAuth token is not valid
    echo json_encode(array("exit" => "error", "error" => "OAuth token not valid"));
    exit();
}
//the OAuth token is valid
$OAuth = $result->fetch_assoc();
if ($OAuth['grade'] > 1) {
    //the OAuth token is not valid
    echo json_encode(array("exit" => "error", "error" => "OAuth token not valid"));
    exit();
}
//check if the current timestamp is greater than the expiration timestamp
if (time() > strtotime($OAuth['expiration'])) {
    //the OAuth token is not valid
    echo json_encode(array("exit" => "error", "error" => "OAuth token expired"));
    exit();
}
//check if the access_to is valid
$access_to = json_decode($OAuth['access_to'], true);
if (!isset($access_to['administration.OAuth.getKeyList']) && !isset($access_to['administration.OAuth.*']) && !isset($access_to['administration.*']) && !isset($access_to['*'])) {
    //the OAuth token is not valid
    echo json_encode(array("exit" => "error", "error" => "OAuth token not valid"));
    exit();
}

//get the OAuth keys
$stmt = $mysqli->prepare("SELECT * FROM OAuth WHERE type = ?");
$stmt->bind_param("s", $purpose);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    //there are no OAuth keys
    echo json_encode(array("exit" => "error", "error" => "there are no OAuth keys"));
    exit();
}
//there are OAuth keys
$OAuth_keys = array();
while ($row = $result->fetch_assoc()) {
    $OAuth_keys[] = $row;
    $OAuth_keys[count($OAuth_keys) - 1]['expiration'] = date("d/m/Y H:i:s", strtotime($OAuth_keys[count($OAuth_keys) - 1]['expiration']));
}
echo json_encode(array("exit" => "success", "OAuth_keys" => $OAuth_keys));
exit();
?>
