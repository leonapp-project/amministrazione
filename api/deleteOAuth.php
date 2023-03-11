<?php
//Path: api/updateOAuthInfo.php
/* MySQL tables
utenti
id, first_name, last_name, email, last_login, date_joined, class_branch, class_section, can_buy_tickets, username

OAuth
id, type, grade, okey, expiration, access_to, commento

this is an API api/updateOAuthInfo.php?
OAuth: 32 chars
id: int (id of the OAuth key)

The OAuth grade must be less than or equal to 1. It must not be expired (expiration is a timestamp).
The access_to, which is a JSON, should have the key/permission administration.OAuth.delete set to true or
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

//get the id
$id = $_GET['id'];

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
//check if the access_to JSON has the key administration.OAuth.delete set to true
$access_to = json_decode($OAuth['access_to'], true);
if (!isset($access_to['administration.OAuth.delete']) && !isset($access_to['administration.*']) && !isset($access_to['administration.OAuth.*']) && !isset($access_to['*'])) {
    //the OAuth token is not valid
    echo json_encode(array("exit" => "error", "error" => "OAuth token not allowed to administration.studenti.getList"));
    exit();
}

//proceed to delete the auth key
$stmt = $mysqli->prepare("DELETE FROM OAuth WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
if ($stmt->affected_rows === 0) {
    //the OAuth token is not valid
    echo json_encode(array("exit" => "error", "error" => "OAuth key not found"));
    exit();
}
echo json_encode(array("exit" => "success", "message" => "OAuth key deleted"));
exit();
?>
