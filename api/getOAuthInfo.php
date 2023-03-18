<?php
/* Path: api/getOAuthInfo.php
MySQL tables
utenti
id, first_name, last_name, email, last_login, date_joined, class_branch, class_section, can_buy_tickets, username
OAuth
id, type, grade, okey, expiration, access_to, commento


This is an API to get the information of an OAuth key.
OAuth: 32 chars
id: int (the id of the OAuth key)
The OAuth grade must be less than or equal to 1. It must not be expired (expiration is a timestamp).
The access_to, which is a JSON, should have the key/permission administration.OAuth.getKeyInfo set to true or
any of the roots set to true for example administration.OAuth.* or administration.* or *.
*/
//errors
ini_set('display_errors', 1);
//import once the database connection
require_once 'mysqli.php';
header('Content-Type: application/json');

//check the permissiojn using utils
require_once 'utils.php';
if (!checkOAuthPermissionFor("administration.OAuth.getKeyInfo")) {
    echo json_encode(array("exit" => "error", "error" => "OAuth token not valid"));
    exit();
}

//get the id
$id = $_GET['id'];
if (!isset($id)) {
    echo json_encode(array("exit" => "error", "error" => "No id given"));
    exit();
}

//check if the id is an integer
if (!is_numeric($id)) {
    echo json_encode(array("exit" => "error", "error" => "The id is not an integer"));
    exit();
}

//retrieve the OAuth key from the database
$sql = "SELECT * FROM OAuth WHERE id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 0) {
    echo json_encode(array("exit" => "error", "error" => "The OAuth key does not exist"));
    exit();
}
$row = $result->fetch_assoc();

//return the OAuth key
echo json_encode(array("exit" => "success", "data" => $row));
exit();
?>