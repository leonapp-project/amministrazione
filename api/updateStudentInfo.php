<?php
//Path: api/updateStudentInfo.php
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
- id: int (id of the student) 
- field: string (field to update)
- value: string (value to update)
The OAuth grade must be less than or equal to 1. It must not be expired (expiration is a timestamp). 
The access_to, which is a JSON, should have the key/permission administration.studenti.updateInfo set to true or
any of the roots set to true for example administration.studenti.* or administration.* or *.
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

//get the id or the email
$id = $_GET['id'];
$field = $_GET['field'];
$value = $_GET['value'];

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
if (!isset($access_to['administration.studenti.updateInfo']) && !isset($access_to['administration.studenti.*']) && !isset($access_to['administration.*']) && !isset($access_to['*'])) {
    //the OAuth token is not valid
    echo json_encode(array("exit" => "error", "error" => "OAuth token not valid"));
    exit();
}

//check if the id is set
if (!isset($id)) {
    //the id is not set
    echo json_encode(array("exit" => "error", "error" => "id not set"));
    exit();
}

//check if the field is set
if (!isset($field)) {
    //the field is not set
    echo json_encode(array("exit" => "error", "error" => "field not set"));
    exit();
}

//check if the value is set
if (!isset($value)) {
    //the value is not set
    echo json_encode(array("exit" => "error", "error" => "value not set"));
    exit();
}

//check if the field is valid
if ($field != "first_name" && $field != "last_name" && $field != "email" && $field != "class_number" && $field != "class_section" && $field != "can_buy_tickets" && $field != "username") {
    //the field is not valid
    echo json_encode(array("exit" => "error", "error" => "field not valid"));
    exit();
}

//check if the value is valid
if ($field == "first_name" || $field == "last_name" || $field == "email" || $field == "username") {
    //the field is a string
    if (strlen($value) > 255) {
        //the value is not valid
        echo json_encode(array("exit" => "error", "error" => "value not valid"));
        exit();
    }
} else if ($field == "class_branch" || $field == "class_section") {
    //the field is a string
    if (strlen($value) > 10) {
        //the value is not valid
        echo json_encode(array("exit" => "error", "error" => "value not valid"));
        exit();
    }
} else if ($field == "can_buy_tickets") {
    //the field is a boolean
    if ($value != "true" && $value != "false") {
        //the value is not valid
        echo json_encode(array("exit" => "error", "error" => "value not valid"));
        exit();
    }
    //convert from boolean to int
    if ($value == "true") {
        $value = 1;
    } else {
        $value = 0;
    }
}
//LATER HERE THE USERNAME CHECKS IF THEY ARE VALID AND IF THEY ARE NOT ALREADY TAKEN

//update the student info
$stmt = $mysqli->prepare("UPDATE utenti SET $field = ? WHERE id = ?");
$stmt->bind_param("si", $value, $id);
$stmt->execute();
if ($stmt->affected_rows === 0) {
    //the student info was not updated
    echo json_encode(array("exit" => "error", "error" => "student info not updated"));
    exit();
}
//the student info was updated
echo json_encode(array("exit" => "success", "message" => "Student info updated"));
exit();
?>