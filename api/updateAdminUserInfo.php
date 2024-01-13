<?php
//Path: api/updateAdminUserInfo.php
/* MySQL tables
OAuth
id, type, grade, okey, expiration, access_to, commento
OAuth_map
id, OAuth, email, username, password

*/
/* 
OAuth: 32 chars
then one of the two:
- id: int (id of the admin) 
- field: string (field to update)
- value: string (value to update)
The OAuth grade must be less than or equal to 1. It must not be expired (expiration is a timestamp). 
The access_to, which is a JSON, should have the key/permission administration.admin.updateInfo set to true or
any of the roots set to true for example administration.admin.* or administration.* or *.
*/
require_once 'mysqli.php';
header('Content-Type: application/json');

require_once 'utils.php';
if(!checkOAuthPermissionFor("administration.admin.getInfo")) {
    echo json_encode(array("exit" => "error", "error" => "OAuth token not valid"));
    exit();
}

//check if the id is set
if (!isset($_GET['id'])) {
    //the id is not set
    echo json_encode(array("exit" => "error", "error" => "id not set"));
    exit();
}

//check if the field is set
if (!isset($_GET['field'])) {
    //the field is not set
    echo json_encode(array("exit" => "error", "error" => "field not set"));
    exit();
}

//check if the value is set
if (!isset($_GET['value'])) {
    //the value is not set
    echo json_encode(array("exit" => "error", "error" => "value not set"));
    exit();
}
$id = $_GET['id'];
$field = $_GET['field'];
$value = $_GET['value'];

$allowed_fields = ["OAuth", "email", "username", "password"];

//check if the field is valid
if(!in_array($field, $allowed_fields)) {
    //the field is not valid
    echo json_encode(array("exit" => "error", "error" => "Field is not valid"));
    exit();
}

//check if the value is valid
if ($field == "email" || $field == "username"|| $field == "password") {
    if (strlen($value) > 255) {
        echo json_encode(array("exit" => "error", "error" => "value not valid"));
        exit();
    }
} else if ($field == "OAuth" ) {
    //the field is a string
    if (strlen($value) > 32) {
        //the value is not valid
        echo json_encode(array("exit" => "error", "error" => "value not valid"));
        exit();
    }
}

// check if the username or the email is already used
if ($field == "email" || $field == "username") {
    $stmt = $mysqli->prepare("SELECT id FROM OAuth_map WHERE $field = ?");
    $stmt->bind_param("s", $value);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        //the username or the email is already used
        echo json_encode(array("exit" => "error", "error" => "$field already used"));
        exit();
    }
}

//update the student info
$stmt = $mysqli->prepare("UPDATE OAuth_map SET $field = ? WHERE id = ?");
$stmt->bind_param("si", $value, $id);
$stmt->execute();
if ($stmt->affected_rows === 0) {
    //the student info was not updated
    echo json_encode(array("exit" => "error", "error" => "admin info not updated"));
    exit();
}
//the student info was updated
echo json_encode(array("exit" => "success", "message" => "Admin info updated"));
exit();
?>