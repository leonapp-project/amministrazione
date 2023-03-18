<?php
/* Path: api/getSystemUsers.php
MySQL tables
utenti
id, first_name, last_name, email, last_login, date_joined, class_branch, class_section, can_buy_tickets, username
OAuth
id, type, grade, okey, expiration, access_to, commento
OAuth_map
id, OAuth, email, username, password

This api lists the system users.
OAuth: 32 chars

The OAuth grade must be less than or equal to 1. It must not be expired (expiration is a timestamp).
The access_to, which is a JSON, should have the key/permission administration.admins.view set to true or
any of the roots set to true for example administration.admins.* or administration.* or *.
*/

//import once the database connection
require_once 'mysqli.php';
header('Content-Type: application/json');

//check the permissiojn using utils
require_once 'utils.php';
if (!checkOAuthPermissionFor("administration.admins.view")) {
    echo json_encode(array("exit" => "error", "error" => "OAuth token not valid"));
    exit();
}

//retrieve the OAuth key from the database
$sql = "SELECT * FROM OAuth_map";
$result = $mysqli->query($sql);

//return the OAuth key
echo json_encode(array("exit" => "success", "data" => $result->fetch_all()));
exit();
?>
