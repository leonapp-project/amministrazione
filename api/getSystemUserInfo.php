<?php
//Path: api/getSystemUserInfo.php
/* MySQL tables
OAuth
id, type, grade, okey, expiration, access_to, commento
OAuth_map
id, OAuth, email, username, password
*/
/* 
OAuth: 32 chars
then one of the two:
- id: int (id of the useer) 

The OAuth grade must be less than or equal to 1. It must not be expired (expiration is a timestamp). 
The access_to, which is a JSON, should have the key/permission administration.admin.getInfo set to true or
any of the roots set to true for example administration.admin.* or administration.* or *.
*/

//import once the database connection
require_once 'mysqli.php';
header('Content-Type: application/json');

require_once 'utils.php';
if(!checkOAuthPermissionFor("administration.admin.getInfo")) {
    echo json_encode(array("exit" => "error", "error" => "OAuth token not valid"));
    exit();
}

//check if the id or the email is set
if (!isset($_GET['id'])) {
    echo json_encode(array("error" => "id not set"));
    exit();
}

$admin_id = $_GET['id'];

// get from the database the user with the id
$stmt = $mysqli->prepare("SELECT * FROM OAuth_map WHERE id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    //the user does not exist
    echo json_encode(array("exit" => "error", "error" => "user not found"));
    exit();
}

//the user exists
$admin = $result->fetch_assoc();

//return the user
echo json_encode(array("exit" => "success", "data" => $admin));
?>